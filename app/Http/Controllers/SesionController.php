<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sesion;
use Illuminate\Http\Request;

/**
 * Las sesiones NUNCA se administran desde un CRUD aislado: siempre
 * cuelgan de un Grupo especifico y se accede a ellas desde la pestaña
 * "Sesiones" del workspace del grupo (grupos/{grupo}). Ver instruccion
 * de flujo real de FORMATEC: el Grupo es la unidad de trabajo.
 */
class SesionController extends Controller
{
    protected $table = 'sesiones';
    public function store(Request $request, Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        $data = $request->validate([
            'numero_sesion' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'tema' => 'nullable|string|max:200',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
        ]);

        $grupo->sesiones()->create($data);

        return redirect()->route('grupos.show', ['grupo' => $grupo, 'tab' => 'sesiones'])
            ->with('status', 'Sesion agregada correctamente.');
    }

    public function destroy(Grupo $grupo, Sesion $sesion)
    {
        $this->authorize('update', $grupo);

        if ($sesion->asistencias()->exists()) {
            return back()->with('error', 'No se puede eliminar: ya tiene asistencia registrada.');
        }

        $sesion->delete();

        return back()->with('status', 'Sesion eliminada.');
    }
}
