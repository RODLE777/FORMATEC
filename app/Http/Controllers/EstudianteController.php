<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    private function esAdministrador($user): bool
    {
        return in_array(
            $user?->rol,
            ['ROOT', 'ADMINISTRADOR'],
            true
        );
    }

    private function exigirAdministrador(): void
    {
        abort_unless(
            $this->esAdministrador(auth()->user()),
            403,
            'No tienes permiso para modificar estudiantes.'
        );
    }

    private function profesorId($user): int
    {
        $id = $user->profesor?->id;

        abort_unless(
            $id,
            403,
            'No tienes una ficha de profesor asociada.'
        );

        return (int) $id;
    }

    private function verificarAcceso(Estudiante $estudiante): void
    {
        $user = auth()->user();

        if ($this->esAdministrador($user)) {
            return;
        }

        abort_unless(
            $user && $user->esProfesor(),
            403
        );

        $profesorId = $this->profesorId($user);

        $permitido = $estudiante->inscripciones()
            ->whereHas(
                'grupo',
                fn ($q) => $q->where(
                    'profesor_id',
                    $profesorId
                )
            )
            ->exists();

        abort_unless(
            $permitido,
            403,
            'Este estudiante no pertenece a tus grupos.'
        );
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $esAdmin = $this->esAdministrador($user);

        if (!$esAdmin) {
            abort_unless($user->esProfesor(), 403);
        }

        $profesorId = $esAdmin
            ? null
            : $this->profesorId($user);

        $query = Estudiante::query();

        // Restricción obligatoria, independiente de los filtros.
        if (!$esAdmin) {
            $query->whereHas(
                'inscripciones.grupo',
                fn ($q) => $q->where(
                    'profesor_id',
                    $profesorId
                )
            );
        }

        if ($request->filled('q')) {
            $texto = trim($request->input('q'));

            $query->where(function ($q) use ($texto) {
                $q->where('nombres', 'like', "%{$texto}%")
                    ->orWhere('apellidos', 'like', "%{$texto}%")
                    ->orWhere(
                        'codigo_formatec',
                        'like',
                        "%{$texto}%"
                    );
            });
        }

        if ($request->boolean('filtro_mayor_18')) {
            $fechaCorte = Carbon::now()
                ->subYears(18)
                ->toDateString();

            $query->where(
                'fecha_nacimiento',
                '<=',
                $fechaCorte
            );
        }

        if (
            $request->filled('curso_id') ||
            $request->boolean('solo_graduados')
        ) {
            $query->whereHas(
                'inscripciones',
                function ($q) use (
                    $request,
                    $esAdmin,
                    $profesorId
                ) {
                    if ($request->boolean('solo_graduados')) {
                        $q->where(
                            'resultado_final',
                            'GRADUADO'
                        );
                    }

                    if (!$esAdmin || $request->filled('curso_id')) {
                        $q->whereHas(
                            'grupo',
                            function ($grupo) use (
                                $request,
                                $esAdmin,
                                $profesorId
                            ) {
                                if (!$esAdmin) {
                                    $grupo->where(
                                        'profesor_id',
                                        $profesorId
                                    );
                                }

                                if ($request->filled('curso_id')) {
                                    $grupo->where(
                                        'curso_id',
                                        $request->curso_id
                                    );
                                }
                            }
                        );
                    }
                }
            );
        }

        $estudiantes = $query
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        $cursos = Curso::query()
            ->when(
                !$esAdmin,
                fn ($q) => $q->whereHas(
                    'grupos',
                    fn ($g) => $g->where(
                        'profesor_id',
                        $profesorId
                    )
                )
            )
            ->orderBy('nombre')
            ->get();

        return view(
            'estudiantes.index',
            compact('estudiantes', 'cursos')
        );
    }

    public function create()
    {
        $this->exigirAdministrador();

        $estudiante = new Estudiante();

        return view(
            'estudiantes.create',
            compact('estudiante')
        );
    }

    public function store(Request $request)
    {
        $this->exigirAdministrador();

        $data = $this->validated($request);

        $esMenor = Carbon::parse(
            $data['fecha_nacimiento']
        )->age < 18;

        $estudiante = DB::transaction(
            function () use ($data, $request, $esMenor) {

                $estudiante = Estudiante::create($data);

                if ($esMenor) {
                    $estudiante->encargadoMenor()->create([
                        'nombre_completo' =>
                            $request->encargado_nombre_completo,
                        'parentesco' =>
                            $request->encargado_parentesco,
                        'telefono' =>
                            $request->encargado_telefono,
                    ]);
                }

                return $estudiante;
            }
        );

        return redirect()
            ->route('estudiantes.show', $estudiante)
            ->with(
                'status',
                'Estudiante registrado con código ' .
                $estudiante->fresh()->codigo_formatec
            );
    }

    public function show(Estudiante $estudiante)
    {
        $this->verificarAcceso($estudiante);

        $user = auth()->user();

        $estudiante->load('encargadoMenor');

        if ($this->esAdministrador($user)) {
            $estudiante->load([
                'inscripciones.grupo.curso',
                'inscripciones.evaluaciones',
            ]);
        } else {
            $profesorId = $this->profesorId($user);

            // No revelar inscripciones de otros profesores.
            $estudiante->load([
                'inscripciones' => fn ($q) =>
                    $q->whereHas(
                        'grupo',
                        fn ($g) => $g->where(
                            'profesor_id',
                            $profesorId
                        )
                    ),
                'inscripciones.grupo.curso',
                'inscripciones.evaluaciones',
            ]);
        }

        return view(
            'estudiantes.show',
            compact('estudiante')
        );
    }

    public function edit(Estudiante $estudiante)
    {
        $this->exigirAdministrador();

        $estudiante->load('encargadoMenor');

        return view(
            'estudiantes.edit',
            compact('estudiante')
        );
    }

    public function update(
        Request $request,
        Estudiante $estudiante
    ) {
        $this->exigirAdministrador();

        $data = $this->validated(
            $request,
            $estudiante->id
        );

        $esMenor = Carbon::parse(
            $data['fecha_nacimiento']
        )->age < 18;

        DB::transaction(
            function () use (
                $estudiante,
                $data,
                $request,
                $esMenor
            ) {
                $estudiante->update($data);

                if ($esMenor) {
                    $estudiante->encargadoMenor()
                        ->updateOrCreate([], [
                            'nombre_completo' =>
                                $request->encargado_nombre_completo,
                            'parentesco' =>
                                $request->encargado_parentesco,
                            'telefono' =>
                                $request->encargado_telefono,
                        ]);
                } else {
                    $estudiante->encargadoMenor()->delete();
                }
            }
        );

        return redirect()
            ->route('estudiantes.show', $estudiante)
            ->with(
                'status',
                'Ficha actualizada correctamente.'
            );
    }

    public function destroy(Estudiante $estudiante)
    {
        $this->exigirAdministrador();

        if ($estudiante->inscripciones()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar: el estudiante tiene inscripciones. Desactívalo en su lugar.'
            );
        }

        $estudiante->delete();

        return redirect()
            ->route('estudiantes.index')
            ->with('status', 'Estudiante eliminado.');
    }

    private function validated(
        Request $request,
        ?int $ignoreId = null
    ): array {
        $data = $request->validate([
            'nombres' =>
                'required|string|max:150',
            'apellidos' =>
                'required|string|max:150',
            'sexo' =>
                'required|in:MASCULINO,FEMENINO',
            'fecha_nacimiento' =>
                'required|date|before:today',
            'correo' =>
                'nullable|email|max:150',
            'telefono_celular' =>
                'nullable|string|max:20',
            'direccion' =>
                'nullable|string|max:255',
            'profesion_oficio' =>
                'nullable|string|max:150',
            'nivel_estudio' =>
                'nullable|string|max:100',
            'activo' =>
                'boolean',
        ]);

        $esMenor = Carbon::parse(
            $data['fecha_nacimiento']
        )->age < 18;

        $request->validate([
            'dui' => [
                $esMenor ? 'nullable' : 'required',
                'string',
                'max:15',
                Rule::unique('estudiantes', 'dui')
                    ->ignore($ignoreId),
            ],
        ]);

        $data['dui'] = $request->input('dui');

        if ($esMenor) {
            $request->validate([
                'encargado_nombre_completo' =>
                    'required|string|max:200',
                'encargado_parentesco' =>
                    'nullable|string|max:50',
                'encargado_telefono' =>
                    'nullable|string|max:20',
            ]);
        }

        return $data;
    }
}
