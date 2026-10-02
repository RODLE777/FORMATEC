<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class EvaluacionController extends Controller
{
    public function editar(Grupo $grupo, Inscripcion $inscripcion)
    {
        $this->authorize('update', $grupo);
        abort_unless($inscripcion->grupo_id === $grupo->id, 404);

        $inscripcion->load(['estudiante', 'evaluaciones']);
        $config = $grupo->configEvaluaciones();

        return view('grupos.evaluaciones', compact('grupo', 'inscripcion', 'config'));
    }

    public function guardar(Request $request, Grupo $grupo, Inscripcion $inscripcion)
    {
        $this->authorize('update', $grupo);
        abort_unless($inscripcion->grupo_id === $grupo->id, 404);

        $config = $grupo->configEvaluaciones();
        $totalN = count($config);

        $data = $request->validate([
            'nota'   => 'required|array',
            'nota.*' => 'nullable|numeric|min:0|max:10',
        ]);

        $resultadoAntes = $inscripcion->resultado_final;

        // Guardar SOLO las notas que el profesor efectivamente escribió.
        // Las que queden vacías se calculan como 0 en el procedure.
        for ($n = 1; $n <= $totalN; $n++) {
            $nota = $data['nota'][$n] ?? null;

            if ($nota === null || $nota === '') {
                continue;
            }

            $inscripcion->evaluaciones()->updateOrCreate(
                ['numero_evaluacion' => $n],
                [
                    'nombre_evaluacion' => $config[$n - 1]['nombre'],
                    'nota'              => $nota,
                    'fecha_evaluacion'  => now()->toDateString(),
                    'registrado_por'    => $request->user()->id,
                ]
            );
        }

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