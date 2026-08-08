<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

/**
 * Alta de inscripciones y cambio de estado/resultado_final. nota_final
 * JAMAS se escribe aqui: se recalcula solo por trigger de base de datos
 * al insertar/actualizar/borrar en `evaluaciones` (seccion 4.2 del
 * prompt maestro).
 */
class InscripcionController extends Controller
{
    public function create(Request $request)
    {
        $grupo = Grupo::findOrFail($request->grupo_id);
        $this->authorize('update', $grupo);

        return view('inscripciones.create', [
            'grupo' => $grupo,
            'estudiantes' => Estudiante::where('activo', true)
                ->whereDoesntHave('inscripciones', fn ($q) => $q->where('grupo_id', $grupo->id))
                ->orderBy('apellidos')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'estudiante_id' => 'required|exists:estudiantes,id',
            'fecha_inscripcion' => 'required|date',
        ]);

        $grupo = Grupo::findOrFail($data['grupo_id']);
        $this->authorize('update', $grupo);

        Inscripcion::create([
            'estudiante_id' => $data['estudiante_id'],
            'grupo_id' => $data['grupo_id'],
            'fecha_inscripcion' => $data['fecha_inscripcion'],
            'estado' => 'ACTIVA',
            'resultado_final' => 'EN_CURSO',
        ]);

        return redirect()->route('grupos.show', $grupo)->with('status', 'Estudiante inscrito correctamente.');
    }

    public function updateEstado(Request $request, Inscripcion $inscripcion)
    {
        $this->authorize('update', $inscripcion->grupo);

        $data = $request->validate([
            'estado' => 'required|in:ACTIVA,RETIRADA,FINALIZADA',
            'resultado_final' => 'required|in:EN_CURSO,GRADUADO,DESERTADO,REPROBADO',
            'fecha_finalizacion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        // Nunca incluir nota_final aqui: pertenece exclusivamente al trigger.
        $inscripcion->update($data);

        return back()->with('status', 'Estado de inscripcion actualizado.');
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $this->authorize('update', $inscripcion->grupo);

        if ($inscripcion->evaluaciones()->exists() || $inscripcion->asistencias()->exists()) {
            return back()->with('error', 'No se puede eliminar: ya tiene notas o asistencia registrada. Usa RETIRADA en su lugar.');
        }

        $inscripcion->delete();

        return back()->with('status', 'Inscripcion eliminada.');
    }
}
