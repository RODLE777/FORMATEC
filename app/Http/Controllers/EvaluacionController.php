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

        foreach ($data['nota'] as $numero => $nota) {
            if ($nota === null || $nota === '') {
                continue;
            }

            // El UPDATE/INSERT dispara el trigger que recalcula
            // inscripciones.nota_final; no se toca esa columna desde aqui.
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

        return redirect()
            ->route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes'])
            ->with('status', "Evaluaciones de {$inscripcion->estudiante->nombre_completo} guardadas correctamente.");
    }
}
