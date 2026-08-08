<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::withCount('grupos')
            ->when($request->q, fn ($q) => $q->where('nombre', 'like', "%{$request->q}%"))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('cursos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:cursos,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        Curso::create($data);

        return redirect()->route('cursos.index')->with('status', 'Curso creado correctamente.');
    }

    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:cursos,nombre,'.$curso->id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $curso->update($data);

        return redirect()->route('cursos.index')->with('status', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso)
    {
        if ($curso->grupos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el curso tiene grupos asociados. Desactivalo en su lugar.');
        }

        $curso->delete();

        return redirect()->route('cursos.index')->with('status', 'Curso eliminado.');
    }
}
