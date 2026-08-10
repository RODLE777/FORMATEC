<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Método invocable para que funcione con rutas tipo:
     * Route::get('/dashboard', DashboardController::class)
     */
    public function __invoke()
    {
        // Simplemente redirige al método index
        return $this->index();
    }

    /**
     * Método principal que muestra el dashboard según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->esProfesor() && $user->profesor) {
            
            $grupoIds = Grupo::where('profesor_id', $user->profesor->id)->pluck('id');

            return view('dashboard.profesor', [
                'totalGrupos' => $grupoIds->count(),
                'totalEstudiantes' => Inscripcion::whereIn('grupo_id', $grupoIds)->distinct('estudiante_id')->count('estudiante_id'),
                'gruposEnCurso' => Grupo::whereIn('id', $grupoIds)->where('estado', 'EN_CURSO')->count(),
            ]);
        }

        // Dashboard para administradores (y otros roles que no sean profesores)
        $anioActual = now()->year;

        $matriculaPorMes = Inscripcion::join('grupos', 'grupos.id', '=', 'inscripciones.grupo_id')
            ->where('grupos.anio', $anioActual)
            ->selectRaw('grupos.mes as mes, COUNT(*) as total')
            ->groupBy('grupos.mes')
            ->pluck('total', 'mes');

        $matriculaMensual = collect(range(1, 12))->map(fn ($mes) => $matriculaPorMes[$mes] ?? 0);

        return view('dashboard.admin', [
            'totalEstudiantes' => Estudiante::count(),
            'totalGruposEnCurso' => Grupo::where('estado', 'EN_CURSO')->count(),
            'totalGraduados' => Inscripcion::where('resultado_final', 'GRADUADO')->count(),
            'totalDesertados' => Inscripcion::where('resultado_final', 'DESERTADO')->count(),
            'matriculaMensual' => $matriculaMensual,
            'resultados' => [
                'GRADUADO' => Inscripcion::where('resultado_final', 'GRADUADO')->count(),
                'DESERTADO' => Inscripcion::where('resultado_final', 'DESERTADO')->count(),
                'REPROBADO' => Inscripcion::where('resultado_final', 'REPROBADO')->count(),
                'EN_CURSO' => Inscripcion::where('resultado_final', 'EN_CURSO')->count(),
            ],
        ]);
    }
}