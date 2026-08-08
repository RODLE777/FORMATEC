<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->esProfesor() && $user->profesor) {
            // Fase 3/4: el profesor solo ve metricas de sus propios grupos.
            $grupoIds = Grupo::where('profesor_id', $user->profesor->id)->pluck('id');

            return view('dashboard.profesor', [
                'totalGrupos' => $grupoIds->count(),
                'totalEstudiantes' => Inscripcion::whereIn('grupo_id', $grupoIds)->distinct('estudiante_id')->count('estudiante_id'),
                'gruposEnCurso' => Grupo::whereIn('id', $grupoIds)->where('estado', 'EN_CURSO')->count(),
            ]);
        }

        return view('dashboard.admin', [
            'totalEstudiantes' => Estudiante::count(),
            'totalGruposEnCurso' => Grupo::where('estado', 'EN_CURSO')->count(),
            'totalGraduados' => Inscripcion::where('resultado_final', 'GRADUADO')->count(),
            'totalDesertados' => Inscripcion::where('resultado_final', 'DESERTADO')->count(),
        ]);
    }
}
