<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\Request;

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

    // ahora pasa $grupo (vacío) a la vista de creación
    public function create()
    {
        $this->authorize('create', Grupo::class);

        $grupo = new Grupo(); // modelo vacío
        $catalogos = $this->catalogos();

        return view('grupos.create', array_merge(['grupo' => $grupo], $catalogos));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Grupo::class);

        $data = $this->validated($request);
        $grupo = Grupo::create($data);

        return redirect()->route('grupos.show', $grupo)
            ->with('status', 'Grupo creado correctamente.');
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

        $grupo->update($this->validated($request, $grupo->id));

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

        return redirect()->route('grupos.index')
            ->with('status', 'Grupo eliminado.');
    }

    private function catalogos(): array
    {
        return [
            'cursos' => Curso::where('activo', true)->orderBy('nombre')->get(),
            'profesores' => Profesor::where('activo', true)->orderBy('apellidos')->get(),
        ];
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:grupos,codigo_grupo,' . ($ignoreId ?? 'NULL') . ',id,anio,' . $request->anio;

        return $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'profesor_id' => 'nullable|exists:profesores,id',
            'codigo_grupo' => "required|string|max:50|{$uniqueRule}",
            'anio' => 'required|integer|min:2000|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'duracion_horas' => 'nullable|integer|min:1',
            'horario' => 'nullable|string|max:100',
            'lugar' => 'nullable|string|max:150',
            'numero_evaluaciones' => 'required|integer|min:1|max:20',
            'cupo_maximo' => 'nullable|integer|min:1|max:200',
            'plataforma_certificacion' => 'nullable|string|max:50',
            'estado' => 'required|in:PLANIFICADO,EN_CURSO,FINALIZADO,CANCELADO',
        ]);
    }
}