<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Grupo;
use Illuminate\Http\Request;

class BuscarController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $user = $request->user();

        if ($q === '') {
            return view('buscar.index', ['q' => $q, 'estudiantes' => collect(), 'grupos' => collect(), 'cursos' => collect()]);
        }

        $estudiantesQuery = Estudiante::where(function ($qq) use ($q) {
            $qq->where('nombres', 'like', "%{$q}%")
                ->orWhere('apellidos', 'like', "%{$q}%")
                ->orWhere('codigo_formatec', 'like', "%{$q}%")
                ->orWhere('dui', 'like', "%{$q}%");
        });

        $gruposQuery = Grupo::with('curso')->where('codigo_grupo', 'like', "%{$q}%");

        // PROFESOR: resultados acotados a lo suyo, igual que en el resto del sistema.
        if ($user->esProfesor() && $user->profesor) {
            $estudiantesQuery->whereHas('inscripciones.grupo', fn ($qq) => $qq->where('profesor_id', $user->profesor->id));
            $gruposQuery->where('profesor_id', $user->profesor->id);
        }

        return view('buscar.index', [
            'q' => $q,
            'estudiantes' => $estudiantesQuery->limit(10)->get(),
            'grupos' => $gruposQuery->limit(10)->get(),
            'cursos' => $user->puedeGestionarOperacion() ? Curso::where('nombre', 'like', "%{$q}%")->limit(10)->get() : collect(),
        ]);
    }
}
