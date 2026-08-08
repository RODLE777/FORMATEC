<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    public function index(Request $request)
    {
        $profesores = Profesor::with('user')
            ->when($request->q, fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('nombres', 'like', "%{$request->q}%")
                    ->orWhere('apellidos', 'like', "%{$request->q}%");
            }))
            ->orderBy('apellidos')
            ->paginate(15)
            ->withQueryString();

        return view('profesores.index', compact('profesores'));
    }

    public function create()
    {
        // Solo usuarios con rol PROFESOR y que aun no tengan un registro
        // de profesor vinculado pueden ofrecerse en el selector.
        $usuariosDisponibles = User::where('rol', 'PROFESOR')
            ->doesntHave('profesor')
            ->orderBy('name')
            ->get();

        return view('profesores.create', compact('usuariosDisponibles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:profesores,user_id',
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dui' => 'nullable|string|max:15',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'especialidad' => 'nullable|string|max:150',
            'activo' => 'boolean',
        ]);

        Profesor::create($data);

        return redirect()->route('profesores.index')->with('status', 'Profesor registrado correctamente.');
    }

    public function edit(Profesor $profesore)
    {
        $usuariosDisponibles = User::where('rol', 'PROFESOR')
            ->where(fn ($q) => $q->doesntHave('profesor')->orWhere('id', $profesore->user_id))
            ->orderBy('name')
            ->get();

        return view('profesores.edit', ['profesor' => $profesore, 'usuariosDisponibles' => $usuariosDisponibles]);
    }

    public function update(Request $request, Profesor $profesore)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:profesores,user_id,'.$profesore->id,
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dui' => 'nullable|string|max:15',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'especialidad' => 'nullable|string|max:150',
            'activo' => 'boolean',
        ]);

        $profesore->update($data);

        return redirect()->route('profesores.index')->with('status', 'Profesor actualizado correctamente.');
    }

    public function destroy(Profesor $profesore)
    {
        if ($profesore->grupos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el profesor tiene grupos asignados. Desactivalo en su lugar.');
        }

        $profesore->delete();

        return redirect()->route('profesores.index')->with('status', 'Profesor eliminado.');
    }
}
