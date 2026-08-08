<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sesion;
use Illuminate\Http\Request;

/**
 * Flujo: Grupo -> Sesion -> Pasar lista -> Guardar asistencia.
 * La asistencia SIEMPRE pertenece a una sesion concreta; no existe un
 * modulo "Asistencias" independiente en el menu.
 */
class AsistenciaController extends Controller
{
    /** Pantalla de "pasar lista" para una sesion del grupo. */
    public function pasarLista(Grupo $grupo, Sesion $sesion)
    {
        $this->authorize('update', $grupo);
        abort_unless($sesion->grupo_id === $grupo->id, 404);

        $inscripciones = $grupo->inscripciones()
            ->with(['estudiante', 'asistencias' => fn ($q) => $q->where('sesion_id', $sesion->id)])
            ->where('estado', 'ACTIVA')
            ->get();

        return view('grupos.pasar-lista', compact('grupo', 'sesion', 'inscripciones'));
    }

    public function guardar(Request $request, Grupo $grupo, Sesion $sesion)
    {
        $this->authorize('update', $grupo);
        abort_unless($sesion->grupo_id === $grupo->id, 404);

        $data = $request->validate([
            'asistencia' => 'array',
            'asistencia.*' => 'boolean',
            'observacion' => 'array',
            'observacion.*' => 'nullable|string|max:255',
        ]);

        foreach ($grupo->inscripciones()->where('estado', 'ACTIVA')->pluck('id') as $inscripcionId) {
            $sesion->asistencias()->updateOrCreate(
                ['inscripcion_id' => $inscripcionId],
                [
                    'asistio' => (bool) ($data['asistencia'][$inscripcionId] ?? false),
                    'observacion' => $data['observacion'][$inscripcionId] ?? null,
                    'registrado_por' => $request->user()->id,
                ]
            );
        }

        return redirect()->route('grupos.show', ['grupo' => $grupo, 'tab' => 'asistencia'])
            ->with('status', "Asistencia de la sesion {$sesion->numero_sesion} guardada correctamente.");
    }
}
