<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ✅ Relaciones geográficas eliminadas
        $query = Estudiante::query()
            // 1. Buscador de texto normal
            ->when($request->q, fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('nombres', 'like', "%{$request->q}%")
                    ->orWhere('apellidos', 'like', "%{$request->q}%")
                    ->orWhere('codigo_formatec', 'like', "%{$request->q}%");
            }))
            // 2. Filtro: Solo mayores de 18 años
            ->when($request->filtro_mayor_18, function ($q) {
                $fechaCorte = \Illuminate\Support\Carbon::now()->subYears(18)->format('Y-m-d');
                $q->where('fecha_nacimiento', '<=', $fechaCorte);
            })
            // 3. Filtro: Curso y/o Solo Graduados
            ->when($request->curso_id || $request->solo_graduados, function ($q) use ($request) {
                $q->whereHas('inscripciones', function ($qq) use ($request) {
                    
                    if ($request->solo_graduados) {
                        $qq->where('resultado_final', 'GRADUADO');
                    }
                    
                    if ($request->curso_id) {
                        $qq->whereHas('grupo', function ($qg) use ($request) {
                            $qg->where('curso_id', $request->curso_id);
                        });
                    }
                });
            });

        // Si es profesor, solo ve a sus estudiantes
        if ($user->esProfesor() && $user->profesor) {
            $query->whereHas('inscripciones.grupo', fn ($q) => $q->where('profesor_id', $user->profesor->id));
        }

        $estudiantes = $query->orderBy('apellidos')->paginate(15)->withQueryString();
        
        $cursos = \App\Models\Curso::orderBy('nombre')->get();

        return view('estudiantes.index', compact('estudiantes', 'cursos'));
    }

    public function create()
    {
        $this->authorize('create', Estudiante::class);

        $estudiante = new Estudiante();

        return view('estudiantes.create', compact('estudiante'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Estudiante::class);

        $data = $this->validated($request);
        $esMenor = Carbon::parse($data['fecha_nacimiento'])->age < 18;

        $estudiante = DB::transaction(function () use ($data, $request, $esMenor) {
            $estudiante = Estudiante::create($data);

            if ($esMenor) {
                $estudiante->encargadoMenor()->create([
                    'nombre_completo' => $request->encargado_nombre_completo,
                    'parentesco'      => $request->encargado_parentesco,
                    'telefono'        => $request->encargado_telefono,
                ]);
            }

            return $estudiante;
        });

        return redirect()->route('estudiantes.show', $estudiante)
            ->with('status', "Estudiante registrado con código {$estudiante->fresh()->codigo_formatec}.");
    }

    public function show(Estudiante $estudiante)
    {
        $this->authorize('view', $estudiante);

        $estudiante->load(['encargadoMenor', 'inscripciones.grupo.curso', 'inscripciones.evaluaciones']);

        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        $this->authorize('update', $estudiante);

        $estudiante->load('encargadoMenor');

        return view('estudiantes.edit', compact('estudiante'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $this->authorize('update', $estudiante);

        $data = $this->validated($request, $estudiante->id);
        $esMenor = Carbon::parse($data['fecha_nacimiento'])->age < 18;

        DB::transaction(function () use ($estudiante, $data, $request, $esMenor) {
            $estudiante->update($data);

            if ($esMenor) {
                $estudiante->encargadoMenor()->updateOrCreate([], [
                    'nombre_completo' => $request->encargado_nombre_completo,
                    'parentesco'      => $request->encargado_parentesco,
                    'telefono'        => $request->encargado_telefono,
                ]);
            } else {
                $estudiante->encargadoMenor()->delete();
            }
        });

        return redirect()->route('estudiantes.show', $estudiante)
            ->with('status', 'Ficha actualizada correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $this->authorize('delete', $estudiante);

        if ($estudiante->inscripciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: el estudiante tiene inscripciones. Desactívalo en su lugar.');
        }

        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('status', 'Estudiante eliminado.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nombres'          => 'required|string|max:150',
            'apellidos'        => 'required|string|max:150',
            'sexo'             => 'required|in:MASCULINO,FEMENINO',
            'fecha_nacimiento' => 'required|date|before:today',
            'correo'           => 'nullable|email|max:150',
            'telefono_celular' => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:255',
            'profesion_oficio' => 'nullable|string|max:150',
            'nivel_estudio'    => 'nullable|string|max:100',
            'activo'           => 'boolean',
        ]);

        $esMenor = Carbon::parse($data['fecha_nacimiento'])->age < 18;

        $request->validate([
            'dui' => [
                $esMenor ? 'nullable' : 'required',
                'string',
                'max:15',
                Rule::unique('estudiantes', 'dui')->ignore($ignoreId),
            ],
        ]);
        $data['dui'] = $request->input('dui');

        if ($esMenor) {
            $request->validate([
                'encargado_nombre_completo' => 'required|string|max:200',
                'encargado_parentesco'      => 'nullable|string|max:50',
                'encargado_telefono'        => 'nullable|string|max:20',
            ]);
        }

        return $data;
    }
}