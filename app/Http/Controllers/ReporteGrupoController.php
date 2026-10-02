<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\ReporteGenerado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Reportes que pertenecen a UN grupo especifico (constancia de
 * asistencia, acta de notas). Se generan desde la pestaña "Reportes"
 * del workspace del grupo, nunca desde un modulo aislado.
 */
class ReporteGrupoController extends Controller
{
   public function actaNotas(Request $request, Grupo $grupo)
{
    $this->authorize('view', $grupo);

    $grupo->load(['curso', 'profesor', 'inscripciones.estudiante', 'inscripciones.evaluaciones']);

    $config = $grupo->configEvaluaciones();

    $pdf = Pdf::loadView('reportes.acta-notas', compact('grupo', 'config'))
        ->setPaper('letter', 'landscape');

    $this->registrar($request, 'ACTA_NOTAS', ['grupo_id' => $grupo->id]);

    return $pdf->download("acta-notas-{$grupo->codigo_grupo}.pdf");
}

    public function actaNotasExcel(Request $request, Grupo $grupo)
    {
        $this->authorize('view', $grupo);

        $grupo->load(['inscripciones.estudiante', 'inscripciones.evaluaciones']);

        $this->registrar($request, 'ACTA_NOTAS_EXCEL', ['grupo_id' => $grupo->id]);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActaNotasExport($grupo),
            "acta-notas-{$grupo->codigo_grupo}.xlsx"
        );
    }

    public function constanciaAsistencia(Request $request, Grupo $grupo)
    {
        $this->authorize('view', $grupo);

        $grupo->load(['curso', 'sesiones' => fn ($q) => $q->orderBy('numero_sesion'), 'inscripciones.estudiante', 'inscripciones.asistencias']);

        $pdf = Pdf::loadView('reportes.constancia-asistencia', compact('grupo'))->setPaper('letter', 'landscape');

        $this->registrar($request, 'CONSTANCIA_ASISTENCIA', ['grupo_id' => $grupo->id]);

        return $pdf->download("asistencia-{$grupo->codigo_grupo}.pdf");
    }

    /** Constancia individual: un estudiante graduado de un grupo. */
    public function constanciaEstudiante(Request $request, Grupo $grupo, \App\Models\Inscripcion $inscripcion)
    {
        $this->authorize('view', $grupo);
        abort_unless($inscripcion->grupo_id === $grupo->id, 404);

        $inscripcion->load('estudiante');
        $grupo->load('curso');

        $pdf = Pdf::loadView('reportes.constancia-estudiante', compact('grupo', 'inscripcion'))->setPaper('letter', 'portrait');

        $this->registrar($request, 'CONSTANCIA_ESTUDIANTE', ['grupo_id' => $grupo->id, 'inscripcion_id' => $inscripcion->id]);

        return $pdf->download("constancia-{$inscripcion->estudiante->codigo_formatec}.pdf");
    }

    private function registrar(Request $request, string $tipo, array $parametros): void
    {
        ReporteGenerado::create([
            'usuario_id' => $request->user()->id,
            'tipo_reporte' => $tipo,
            'parametros' => $parametros,
        ]);
    }
}
