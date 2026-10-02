<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Grupo::with(['curso', 'profesor'])->withCount('inscripciones');

        if ($user->esProfesor() && $user->profesor) {
            $query->where('profesor_id', $user->profesor->id);
        }

        $grupos = $query
            ->when($request->anio, fn ($q) => $q->where('anio', $request->anio))
            ->when($request->curso_id, fn ($q) => $q->where('curso_id', $request->curso_id))
            ->orderByDesc('anio')->orderByDesc('mes')
            ->paginate(15)
            ->withQueryString();

        return view('grupos.index', [
            'grupos' => $grupos,
            'cursos' => Curso::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Grupo::class);
        $grupo = new Grupo();
        return view('grupos.create', array_merge(['grupo' => $grupo], $this->catalogos()));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Grupo::class);

        $data = $this->validated($request);
        $data['evaluaciones_config'] = Grupo::generarConfigPorDefecto((int) $data['numero_evaluaciones']);

        $grupo = Grupo::create($data);

        return redirect()->route('grupos.show', $grupo)
            ->with('status', 'Grupo creado correctamente. Ajusta los porcentajes en la pestaña "Config. evaluaciones".');
    }

    public function show(Grupo $grupo)
    {
        $this->authorize('view', $grupo);

        $grupo->load([
            'curso', 'profesor',
            'inscripciones.estudiante',
            'inscripciones.evaluaciones',
            'sesiones' => fn ($q) => $q->orderBy('numero_sesion'),
        ]);

        return view('grupos.show', compact('grupo'));
    }

    public function edit(Grupo $grupo)
    {
        $this->authorize('update', $grupo);
        return view('grupos.edit', array_merge(['grupo' => $grupo], $this->catalogos()));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        $data = $this->validated($request, $grupo->id);

        // Si cambió la cantidad de evaluaciones, regenerar la config por defecto
        if ((int) $data['numero_evaluaciones'] !== count($grupo->configEvaluaciones())) {
            $data['evaluaciones_config'] = Grupo::generarConfigPorDefecto((int) $data['numero_evaluaciones']);
        }

        $grupo->update($data);

        return redirect()->route('grupos.show', $grupo)
            ->with('status', 'Grupo actualizado correctamente.');
    }

    public function destroy(Grupo $grupo)
    {
        $this->authorize('delete', $grupo);

        if ($grupo->inscripciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: el grupo tiene inscripciones. Cambia su estado a CANCELADO en su lugar.');
        }

        $grupo->delete();

        return redirect()->route('grupos.index')->with('status', 'Grupo eliminado.');
    }

    // ──────────────────────────────────────────────────────────────
    // CONFIGURACIÓN DE EVALUACIONES (solo ROOT/ADMINISTRADOR)
    // ──────────────────────────────────────────────────────────────

    public function configEvaluaciones(Grupo $grupo)
    {
        $this->authorize('view', $grupo);
        $config = $grupo->configEvaluaciones();
        return view('grupos.config-evaluaciones', compact('grupo', 'config'));
    }

    public function guardarConfigEvaluaciones(Request $request, Grupo $grupo)
    {
        abort_unless(
            in_array($request->user()->rol, ['ROOT', 'ADMINISTRADOR'], true),
            403,
            'Solo ROOT y ADMINISTRADOR pueden editar la configuración de evaluaciones.'
        );

        $data = $request->validate([
            'evaluaciones'               => 'required|array|min:1|max:50',
            'evaluaciones.*.nombre'      => 'required|string|max:100',
            'evaluaciones.*.porcentaje'  => 'required|numeric|min:0|max:100',
        ]);

        $config = array_values($data['evaluaciones']);
        $suma = round(array_sum(array_column($config, 'porcentaje')), 2);

        if (abs($suma - 100) > 0.01) {
            return back()->withInput()
                ->with('error', "La suma de los porcentajes debe ser exactamente 100%. Actualmente suma {$suma}%.");
        }

        $totalNuevo = count($config);
        $totalAnterior = (int) $grupo->numero_evaluaciones;

        // Verificar que si se reducen evaluaciones, las que se eliminan no tengan notas
        if ($totalNuevo < $totalAnterior) {
            foreach ($grupo->inscripciones as $insc) {
                $tieneNotas = $insc->evaluaciones()
                    ->where('numero_evaluacion', '>', $totalNuevo)
                    ->where('nota', '>', 0)
                    ->exists();
                if ($tieneNotas) {
                    return back()->withInput()
                        ->with('error', 'No se puede reducir el número de evaluaciones: hay notas registradas en evaluaciones que se eliminarían.');
                }
            }
        }

        DB::transaction(function () use ($grupo, $config, $totalNuevo, $totalAnterior) {
            $grupo->update([
                'evaluaciones_config' => $config,
                'numero_evaluaciones' => $totalNuevo,
            ]);

            // Si creció: generar filas para las evaluaciones nuevas en cada inscripción
            if ($totalNuevo > $totalAnterior) {
                foreach ($grupo->inscripciones as $insc) {
                    for ($n = $totalAnterior + 1; $n <= $totalNuevo; $n++) {
                        $insc->evaluaciones()->firstOrCreate(
                            ['numero_evaluacion' => $n],
                            [
                                'nombre_evaluacion' => $config[$n - 1]['nombre'],
                                'nota'              => 0,
                            ]
                        );
                    }
                }
            }

            // Si decreció: eliminar las filas sobrantes (todas tienen nota 0 por la verificación previa)
            if ($totalNuevo < $totalAnterior) {
                foreach ($grupo->inscripciones as $insc) {
                    $insc->evaluaciones()
                        ->where('numero_evaluacion', '>', $totalNuevo)
                        ->delete();
                }
            }

            // Sincronizar nombres en las evaluaciones existentes
            foreach ($grupo->inscripciones as $insc) {
                for ($n = 1; $n <= $totalNuevo; $n++) {
                    $insc->evaluaciones()
                        ->where('numero_evaluacion', $n)
                        ->update(['nombre_evaluacion' => $config[$n - 1]['nombre']]);
                }
            }

            // Recalcular nota_final de todas las inscripciones del grupo
            foreach ($grupo->inscripciones()->pluck('id') as $inscId) {
                DB::statement('CALL sp_recalcular_inscripcion(?)', [$inscId]);
            }
        });

        return redirect()
            ->route('grupos.show', ['grupo' => $grupo, 'tab' => 'config-evaluaciones'])
            ->with('status', 'Configuración de evaluaciones guardada. Notas finales recalculadas.');
    }

    private function catalogos(): array
    {
        return [
            'cursos'     => Curso::where('activo', true)->orderBy('nombre')->get(),
            'profesores' => Profesor::where('activo', true)->orderBy('apellidos')->get(),
        ];
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:grupos,codigo_grupo,' . ($ignoreId ?? 'NULL') . ',id,anio,' . $request->anio;

        return $request->validate([
            'curso_id'                 => 'required|exists:cursos,id',
            'profesor_id'              => 'nullable|exists:profesores,id',
            'codigo_grupo'             => "required|string|max:50|{$uniqueRule}",
            'anio'                     => 'required|integer|min:2000|max:2100',
            'mes'                      => 'required|integer|min:1|max:12',
            'fecha_inicio'             => 'nullable|date',
            'fecha_fin'                => 'nullable|date|after_or_equal:fecha_inicio',
            'duracion_horas'           => 'nullable|integer|min:1',
            'horario'                  => 'nullable|string|max:100',
            'lugar'                    => 'nullable|string|max:150',
            'numero_evaluaciones'      => 'required|integer|min:1|max:50',
            'cupo_maximo'              => 'nullable|integer|min:1|max:200',
            'plataforma_certificacion' => 'nullable|string|max:50',
            'estado'                   => 'required|in:PLANIFICADO,EN_CURSO,FINALIZADO,CANCELADO',
        ]);
    }
}