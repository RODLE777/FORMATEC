<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->esProfesor() && $user->profesor) {
            return $this->dashboardProfesor($user);
        }

        return $this->dashboardAdministrador($request);
    }

    private function dashboardAdministrador(Request $request)
    {
        $anioActual = now()->year;

        $anios = Grupo::query()
            ->whereNotNull('anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->map(fn ($anio) => (int) $anio)
            ->values();

        if (!$anios->contains($anioActual)) {
            $anios->prepend($anioActual);
        }

        $anio = (int) $request->query('anio', $anioActual);

        if (!$anios->contains($anio)) {
            $anio = $anioActual;
        }

        $gruposDelAnio = Grupo::where('anio', $anio);

        $totalGrupos = (clone $gruposDelAnio)->count();

        $totalGruposEnCurso = (clone $gruposDelAnio)
            ->where('estado', 'EN_CURSO')
            ->count();

        $gruposPlanificados = (clone $gruposDelAnio)
            ->where('estado', 'PLANIFICADO')
            ->count();

        $gruposFinalizados = (clone $gruposDelAnio)
            ->where('estado', 'FINALIZADO')
            ->count();

        $totalInscripcionesAnio = Inscripcion::query()
            ->whereHas('grupo', fn ($q) =>
                $q->where('anio', $anio)
            )
            ->count();

        $estudiantesDelAnio = Inscripcion::query()
            ->whereHas('grupo', fn ($q) =>
                $q->where('anio', $anio)
            )
            ->distinct('estudiante_id')
            ->count('estudiante_id');

        $matriculaPorMes = Inscripcion::query()
            ->join(
                'grupos',
                'grupos.id',
                '=',
                'inscripciones.grupo_id'
            )
            ->where('grupos.anio', $anio)
            ->selectRaw(
                'grupos.mes AS mes, COUNT(*) AS total'
            )
            ->groupBy('grupos.mes')
            ->pluck('total', 'mes');

        $matriculaMensual = collect(range(1, 12))
            ->map(fn ($mes) =>
                (int) ($matriculaPorMes[$mes] ?? 0)
            );

        $resultados = [
            'GRADUADO' => Inscripcion::where(
                'resultado_final',
                'GRADUADO'
            )->count(),

            'DESERTADO' => Inscripcion::where(
                'resultado_final',
                'DESERTADO'
            )->count(),

            'REPROBADO' => Inscripcion::where(
                'resultado_final',
                'REPROBADO'
            )->count(),

            'EN_CURSO' => Inscripcion::where(
                'resultado_final',
                'EN_CURSO'
            )->count(),
        ];

        $totalResultados = array_sum($resultados);

        $ultimasInscripciones = Inscripcion::query()
            ->with(['estudiante', 'grupo.curso'])
            ->whereHas('grupo', fn ($q) =>
                $q->where('anio', $anio)
            )
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $gruposRecientes = Grupo::query()
            ->with('curso')
            ->where('anio', $anio)
            ->whereIn('estado', [
                'PLANIFICADO',
                'EN_CURSO'
            ])
            ->orderByRaw(
                "CASE WHEN estado = 'EN_CURSO' THEN 0 ELSE 1 END"
            )
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $gruposSinCupos = Grupo::query()
            ->where('anio', $anio)
            ->whereIn('estado', [
                'PLANIFICADO',
                'EN_CURSO'
            ])
            ->get()
            ->filter(fn ($grupo) =>
                $grupo->cuposDisponibles() !== null &&
                $grupo->cuposDisponibles() <= 0
            )
            ->count();

        return view('dashboard.admin', [
            'anio' => $anio,
            'anios' => $anios,
            'totalEstudiantes' => Estudiante::count(),
            'estudiantesDelAnio' => $estudiantesDelAnio,
            'totalInscripcionesAnio' => $totalInscripcionesAnio,
            'totalGrupos' => $totalGrupos,
            'totalGruposEnCurso' => $totalGruposEnCurso,
            'gruposPlanificados' => $gruposPlanificados,
            'gruposFinalizados' => $gruposFinalizados,
            'totalGraduados' => $resultados['GRADUADO'],
            'totalDesertados' => $resultados['DESERTADO'],
            'matriculaMensual' => $matriculaMensual,
            'resultados' => $resultados,
            'totalResultados' => $totalResultados,
            'ultimasInscripciones' => $ultimasInscripciones,
            'gruposRecientes' => $gruposRecientes,
            'gruposSinCupos' => $gruposSinCupos,
        ]);
    }

    private function dashboardProfesor($user)
    {
        $profesorId = $user->profesor->id;

        $grupos = Grupo::query()
            ->with('curso')
            ->where('profesor_id', $profesorId)
            ->orderByDesc('id')
            ->get();

        $grupoIds = $grupos->pluck('id');

        $totalEstudiantes = Inscripcion::query()
            ->whereIn('grupo_id', $grupoIds)
            ->distinct('estudiante_id')
            ->count('estudiante_id');

        $totalInscripciones = Inscripcion::query()
            ->whereIn('grupo_id', $grupoIds)
            ->count();

        $misGrupos = $grupos
            ->filter(fn ($grupo) =>
                in_array($grupo->estado, [
                    'PLANIFICADO',
                    'EN_CURSO'
                ])
            )
            ->take(6)
            ->values();

        $ultimasInscripciones = Inscripcion::query()
            ->with(['estudiante', 'grupo.curso'])
            ->whereIn('grupo_id', $grupoIds)
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('dashboard.profesor', [
            'totalGrupos' => $grupos->count(),
            'gruposEnCurso' => $grupos
                ->where('estado', 'EN_CURSO')
                ->count(),
            'gruposPlanificados' => $grupos
                ->where('estado', 'PLANIFICADO')
                ->count(),
            'totalEstudiantes' => $totalEstudiantes,
            'totalInscripciones' => $totalInscripciones,
            'misGrupos' => $misGrupos,
            'ultimasInscripciones' => $ultimasInscripciones,
        ]);
    }
}
