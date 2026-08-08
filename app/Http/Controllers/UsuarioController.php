<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Gestion de usuarios y roles: EXCLUSIVA de ROOT (seccion 5 del prompt
 * maestro). El middleware 'rol:ROOT' se aplica en routes/web.php sobre
 * todo este grupo de rutas.
 */
class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:ROOT,ADMINISTRADOR,PROFESOR',
            'activo' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,'.$usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|in:ROOT,ADMINISTRADOR,PROFESOR',
            'activo' => 'boolean',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('status', 'Usuario eliminado.');
    }
}
