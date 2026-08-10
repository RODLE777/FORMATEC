<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

/**
 * Flujo: Grupo -> pestana Estudiantes -> seleccionar estudiante ->
 * registrar sus evaluaciones. NUNCA se calcula nota_final aqui: al
 * guardar una evaluacion, el trigger trg_evaluacion_after_insert/update
 * recalcula automaticamente inscripciones.nota_final.
 */
class EvaluacionController extends Controller
{

protected $table = 'evaluaciones';

    public function editar(Grupo $grupo, Inscripcion $inscripcion)
    {
        $this->authorize('update', $grupo);
        abort_unless($inscripcion->grupo_id === $grupo->id, 404);

        $inscripcion->load(['estudiante', 'evaluaciones']);

        return view('grupos.evaluaciones', compact('grupo', 'inscripcion'));
    }

    public function guardar(Request $request, Grupo $grupo, Inscripcion $inscripcion)
    {
        $this->authorize('update', $grupo);
        abort_unless($inscripcion->grupo_id === $grupo->id, 404);

        $data = $request->validate([
            'nota' => 'required|array',
            'nota.*' => 'nullable|numeric|min:0|max:10',
            'nombre_evaluacion' => 'array',
            'nombre_evaluacion.*' => 'nullable|string|max:100',
        ]);

        $resultadoAntes = $inscripcion->resultado_final;

        foreach ($data['nota'] as $numero => $nota) {
            if ($nota === null || $nota === '') {
                continue;
            }

            // El UPDATE/INSERT dispara el trigger que recalcula
            // inscripciones.nota_final y, si ya estan todas las
            // evaluaciones, decide GRADUADO/REPROBADO automaticamente;
            // no se toca esa columna desde aqui.
            $inscripcion->evaluaciones()->updateOrCreate(
                ['numero_evaluacion' => $numero],
                [
                    'nombre_evaluacion' => $data['nombre_evaluacion'][$numero] ?? "Evaluacion {$numero}",
                    'nota' => $nota,
                    'fecha_evaluacion' => now()->toDateString(),
                    'registrado_por' => $request->user()->id,
                ]
            );
        }

        // El trigger pudo haber cambiado resultado_final en la base de
        // datos; se avisa por correo solo si acaba de pasar a GRADUADO
        // en esta misma peticion (evita reenviar el correo cada vez
        // que se edita una nota de alguien que ya se habia graduado).
        $inscripcion->refresh();
        if ($resultadoAntes !== 'GRADUADO' && $inscripcion->resultado_final === 'GRADUADO') {
            $this->notificarGraduacion($inscripcion);
        }

        return redirect()
            ->route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes'])
            ->with('status', "Evaluaciones de {$inscripcion->estudiante->nombre_completo} guardadas correctamente.");
    }

    private function notificarGraduacion(Inscripcion $inscripcion): void
    {
        $inscripcion->loadMissing('estudiante', 'grupo.curso');
        $correo = $inscripcion->estudiante->correo;

        if ($correo) {
            \Illuminate\Support\Facades\Notification::route('mail', $correo)
                ->notify(new \App\Notifications\EstudianteGraduadoNotification($inscripcion));
        }
    }
}
