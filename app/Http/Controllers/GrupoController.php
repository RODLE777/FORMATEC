<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GrupoController extends Controller
{
    private function autorizarConsulta(Request $request, Grupo $grupo): void
    {
        $usuario = $request->user();

        abort_unless($usuario, 401);

        if (in_array($usuario->rol, ['ROOT', 'ADMINISTRADOR'], true)) {
            return;
        }

        abort_unless(
            $usuario->esProfesor()
            && $usuario->profesor
            && (int) $grupo->profesor_id === (int) $usuario->profesor->id,
            403,
            'No tienes acceso a este grupo.'
        );
    }

    private function autorizarConfiguracion(
        Request $request,
        Grupo $grupo,
        bool $escritura = false
    ): void {
        $this->autorizarConsulta($request, $grupo);

        $usuario = $request->user();

        $esAdmin = in_array(
            $usuario->rol,
            ['ROOT', 'ADMINISTRADOR'],
            true
        );

        if ($escritura && !$esAdmin) {
            abort_unless(
                $grupo->estado === 'EN_CURSO',
                403,
                'Solo puedes modificar evaluaciones de grupos en curso.'
            );
        }
    }

    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Grupo::with(['curso', 'profesor'])
            ->withCount('inscripciones');

        $esAdmin = in_array(
            $usuario->rol,
            ['ROOT', 'ADMINISTRADOR'],
            true
        );

        if (!$esAdmin) {
            abort_unless(
                $usuario->esProfesor() && $usuario->profesor,
                403
            );

            $query->where('profesor_id', $usuario->profesor->id);
        }

        $grupos = $query
            ->when(
                $request->filled('anio'),
                fn ($q) => $q->where('anio', $request->anio)
            )
            ->when(
                $request->filled('curso_id'),
                fn ($q) => $q->where('curso_id', $request->curso_id)
            )
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->paginate(15)
            ->withQueryString();

        $cursosQuery = Curso::query();

        if (!$esAdmin) {
            $profesorId = $usuario->profesor->id;

            $cursosQuery->whereIn(
                'id',
                Grupo::where('profesor_id', $profesorId)
                    ->select('curso_id')
            );
        }

        return view('grupos.index', [
            'grupos' => $grupos,
            'cursos' => $cursosQuery->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Grupo::class);

        $grupo = new Grupo();

        return view(
            'grupos.create',
            array_merge(
                ['grupo' => $grupo],
                $this->catalogos()
            )
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', Grupo::class);

        $datos = $this->validated($request);

        $datos['evaluaciones_config'] =
            Grupo::generarConfigPorDefecto(
                (int) $datos['numero_evaluaciones']
            );

        $grupo = Grupo::create($datos);

        return redirect()
            ->route('grupos.show', $grupo)
            ->with(
                'status',
                'Grupo creado correctamente. Puedes configurar sus evaluaciones.'
            );
    }

    public function show(Request $request, Grupo $grupo)
    {
        $this->autorizarConsulta($request, $grupo);

        $grupo->load([
            'curso',
            'profesor',
            'inscripciones.estudiante',
            'inscripciones.evaluaciones',
            'sesiones' => fn ($q) => $q->orderBy('numero_sesion'),
        ]);

        return view('grupos.show', compact('grupo'));
    }

    public function edit(Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        return view(
            'grupos.edit',
            array_merge(
                ['grupo' => $grupo],
                $this->catalogos()
            )
        );
    }

    public function update(Request $request, Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        $datos = $this->validated($request, $grupo->id);

        if (
            (int) $datos['numero_evaluaciones']
            !== count($grupo->configEvaluaciones())
        ) {
            $datos['evaluaciones_config'] =
                Grupo::generarConfigPorDefecto(
                    (int) $datos['numero_evaluaciones']
                );
        }

        $grupo->update($datos);

        return redirect()
            ->route('grupos.show', $grupo)
            ->with('status', 'Grupo actualizado correctamente.');
    }

    public function destroy(Grupo $grupo)
    {
        $this->authorize('delete', $grupo);

        if ($grupo->inscripciones()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar un grupo con inscripciones. Cambia su estado a CANCELADO.'
            );
        }

        $grupo->delete();

        return redirect()
            ->route('grupos.index')
            ->with('status', 'Grupo eliminado.');
    }

    public function configEvaluaciones(
        Request $request,
        Grupo $grupo
    ) {
        $this->autorizarConfiguracion($request, $grupo);

        $grupo->load('curso');

        $config = $grupo->configEvaluaciones();

        return view(
            'grupos.config-evaluaciones',
            compact('grupo', 'config')
        );
    }

    public function guardarConfigEvaluaciones(
        Request $request,
        Grupo $grupo
    ) {
        $this->autorizarConfiguracion(
            $request,
            $grupo,
            true
        );

        $datos = $request->validate([
            'evaluaciones' => 'required|array|min:1|max:50',
            'evaluaciones.*.nombre' =>
                'required|string|max:100',
            'evaluaciones.*.porcentaje' =>
                'required|numeric|min:0|max:100',
        ]);

        $config = array_values($datos['evaluaciones']);

        $suma = round(
            array_sum(array_column($config, 'porcentaje')),
            2
        );

        if (abs($suma - 100) > 0.01) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    "La suma de los porcentajes debe ser 100%. Actualmente es {$suma}%."
                );
        }

        $totalNuevo = count($config);

        DB::transaction(function () use (
            $grupo,
            $config,
            $totalNuevo
        ) {
            $grupoActual = Grupo::whereKey($grupo->id)
                ->lockForUpdate()
                ->firstOrFail();

            $totalAnterior =
                (int) $grupoActual->numero_evaluaciones;

            $inscripciones = $grupoActual
                ->inscripciones()
                ->get();

            if ($totalNuevo < $totalAnterior) {
                foreach ($inscripciones as $inscripcion) {
                    $tieneNotas = $inscripcion
                        ->evaluaciones()
                        ->where('numero_evaluacion', '>', $totalNuevo)
                        ->where('nota', '>', 0)
                        ->exists();

                    if ($tieneNotas) {
                        throw ValidationException::withMessages([
                            'evaluaciones' =>
                                'No puedes eliminar evaluaciones que ya tienen notas registradas.',
                        ]);
                    }
                }
            }

            $grupoActual->update([
                'evaluaciones_config' => $config,
                'numero_evaluaciones' => $totalNuevo,
            ]);

            foreach ($inscripciones as $inscripcion) {
                if ($totalNuevo > $totalAnterior) {
                    for (
                        $n = $totalAnterior + 1;
                        $n <= $totalNuevo;
                        $n++
                    ) {
                        $inscripcion->evaluaciones()->firstOrCreate(
                            ['numero_evaluacion' => $n],
                            [
                                'nombre_evaluacion' =>
                                    $config[$n - 1]['nombre'],
                                'nota' => 0,
                            ]
                        );
                    }
                }

                if ($totalNuevo < $totalAnterior) {
                    $inscripcion->evaluaciones()
                        ->where('numero_evaluacion', '>', $totalNuevo)
                        ->delete();
                }

                for ($n = 1; $n <= $totalNuevo; $n++) {
                    $inscripcion->evaluaciones()
                        ->where('numero_evaluacion', $n)
                        ->update([
                            'nombre_evaluacion' =>
                                $config[$n - 1]['nombre'],
                        ]);
                }
            }

            foreach ($inscripciones as $inscripcion) {
                DB::statement(
                    'CALL sp_recalcular_inscripcion(?)',
                    [$inscripcion->id]
                );
            }
        });

        return redirect()
            ->route('grupos.show', [
                'grupo' => $grupo,
                'tab' => 'config-evaluaciones',
            ])
            ->with(
                'status',
                'Configuración de evaluaciones guardada. Notas finales recalculadas.'
            );
    }

    private function catalogos(): array
    {
        return [
            'cursos' => Curso::where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'profesores' => Profesor::where('activo', true)
                ->orderBy('apellidos')
                ->get(),
        ];
    }

    private function validated(
        Request $request,
        ?int $ignoreId = null
    ): array {
        return $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'profesor_id' => 'nullable|exists:profesores,id',
            'codigo_grupo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('grupos', 'codigo_grupo')
                    ->where('anio', $request->anio)
                    ->ignore($ignoreId),
            ],
            'anio' => 'required|integer|min:2000|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' =>
                'nullable|date|after_or_equal:fecha_inicio',
            'duracion_horas' =>
                'nullable|integer|min:1',
            'horario' => 'nullable|string|max:100',
            'lugar' => 'nullable|string|max:150',
            'numero_evaluaciones' =>
                'required|integer|min:1|max:50',
            'cupo_maximo' =>
                'nullable|integer|min:1|max:200',
            'plataforma_certificacion' =>
                'nullable|string|max:50',
            'estado' =>
                'required|in:PLANIFICADO,EN_CURSO,FINALIZADO,CANCELADO',
        ]);
    }
}
