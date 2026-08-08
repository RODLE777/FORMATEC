<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Estudiante::with(['departamento', 'municipio', 'distrito'])
            ->when($request->q, fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('nombres', 'like', "%{$request->q}%")
                    ->orWhere('apellidos', 'like', "%{$request->q}%")
                    ->orWhere('codigo_formatec', 'like', "%{$request->q}%");
            }));

        // PROFESOR solo ve estudiantes inscritos en SUS grupos — filtro a
        // nivel de query, no solo de UI (seccion 5 del prompt maestro).
        if ($user->esProfesor() && $user->profesor) {
            $query->whereHas('inscripciones.grupo', fn ($q) => $q->where('profesor_id', $user->profesor->id));
        }

        $estudiantes = $query->orderBy('apellidos')->paginate(15)->withQueryString();

        return view('estudiantes.index', compact('estudiantes'));
    }

    public function create()
    {
        $this->authorize('create', Estudiante::class);

        // Se crea un modelo vacío para pasarlo a la vista
        $estudiante = new Estudiante();

        return view('estudiantes.create', array_merge(
            ['estudiante' => $estudiante],
            $this->catalogos()
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Estudiante::class);

        $data = $this->validated($request);
        $esMenor = $this->esMenorEdad($data['fecha_nacimiento']);

        $estudiante = DB::transaction(function () use ($data, $request, $esMenor) {
            // 1. Crear el estudiante (el campo codigo_formatec se deja como NULL)
            $estudiante = Estudiante::create($data);

            // 2. Generar el código manualmente después de la inserción
            $estudiante->codigo_formatec = 'FTEC-' . str_pad($estudiante->id, 6, '0', STR_PAD_LEFT);
            $estudiante->save();

            // 3. Si es menor de edad, guardar el encargado
            if ($esMenor) {
                $estudiante->encargadoMenor()->create([
                    'nombre_completo' => $request->encargado_nombre_completo,
                    'parentesco' => $request->encargado_parentesco,
                    'telefono' => $request->encargado_telefono,
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

        $estudiante->load(['departamento', 'municipio', 'distrito', 'encargadoMenor',
            'inscripciones.grupo.curso', 'inscripciones.evaluaciones']);

        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        $this->authorize('update', $estudiante);

        $estudiante->load('encargadoMenor');

        return view('estudiantes.edit', array_merge(['estudiante' => $estudiante], $this->catalogos()));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $this->authorize('update', $estudiante);

        $data = $this->validated($request, $estudiante->id);
        $esMenor = $this->esMenorEdad($data['fecha_nacimiento']);

        DB::transaction(function () use ($estudiante, $data, $request, $esMenor) {
            $estudiante->update($data);

            if ($esMenor) {
                $estudiante->encargadoMenor()->updateOrCreate([], [
                    'nombre_completo' => $request->encargado_nombre_completo,
                    'parentesco' => $request->encargado_parentesco,
                    'telefono' => $request->encargado_telefono,
                ]);
            } else {
                $estudiante->encargadoMenor()->delete();
            }
        });

        return redirect()->route('estudiantes.show', $estudiante)->with('status', 'Ficha actualizada correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $this->authorize('delete', $estudiante);

        if ($estudiante->inscripciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: el estudiante tiene inscripciones. Desactivalo en su lugar.');
        }

        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('status', 'Estudiante eliminado.');
    }

    private function catalogos(): array
    {
        return [
            'departamentos' => \App\Models\Departamento::with('municipios.distritos')->orderBy('nombre')->get(),
        ];
    }

    private function esMenorEdad(string $fechaNacimiento): bool
    {
        return \Illuminate\Support\Carbon::parse($fechaNacimiento)->age < 18;
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $duiRule = 'nullable|string|max:15|unique:estudiantes,dui'.($ignoreId ? ",{$ignoreId}" : '');

        $data = $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'sexo' => 'required|in:MASCULINO,FEMENINO',
            'fecha_nacimiento' => 'required|date|before:today',
            'dui' => $duiRule,
            'nit' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'telefono_fijo' => 'nullable|string|max:20',
            'telefono_celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'distrito_id' => 'nullable|exists:distritos,id',
            'comunidad' => 'nullable|string|max:150',
            'profesion_oficio' => 'nullable|string|max:150',
            'nivel_estudio' => 'nullable|string|max:100',
            'enfermedades' => 'nullable|string',
            'usuario_certiport' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        // Si el estudiante resulta menor de edad, el encargado es obligatorio.
        if ($this->esMenorEdad($data['fecha_nacimiento'])) {
            $request->validate([
                'encargado_nombre_completo' => 'required|string|max:200',
                'encargado_parentesco' => 'nullable|string|max:50',
                'encargado_telefono' => 'nullable|string|max:20',
            ]);
        }

        return $data;
    }
}