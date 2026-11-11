<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SesionController extends Controller
{
    private function autorizarGestion(Request $request, Grupo $grupo): void
    {
        $usuario = $request->user();

        abort_unless($usuario, 401);

        if (in_array($usuario->rol, ['ROOT', 'ADMINISTRADOR'], true)) {
            return;
        }

        abort_unless(
            $usuario->esProfesor()
            && $usuario->profesor
            && (int) $grupo->profesor_id === (int) $usuario->profesor->id,
            403,
            'No tienes permiso para gestionar las sesiones de este grupo.'
        );

        abort_unless(
            $grupo->estado === 'EN_CURSO',
            403,
            'Solo puedes modificar sesiones de grupos en curso.'
        );
    }

    public function store(Request $request, Grupo $grupo)
    {
        $this->autorizarGestion($request, $grupo);

        $datos = $request->validate([
            'numero_sesion' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('sesiones', 'numero_sesion')
                    ->where('grupo_id', $grupo->id),
            ],
            'fecha' => ['required', 'date'],
            'tema' => ['nullable', 'string', 'max:200'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
        ], [
            'numero_sesion.unique' =>
                'Ya existe una sesión con ese número en este grupo.',
        ]);

        if (
            !empty($datos['hora_inicio'])
            && !empty($datos['hora_fin'])
            && $datos['hora_fin'] <= $datos['hora_inicio']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_fin' =>
                        'La hora de finalización debe ser posterior a la hora de inicio.',
                ]);
        }

        $grupo->sesiones()->create($datos);

        return redirect()
            ->route('grupos.show', [
                'grupo' => $grupo,
                'tab' => 'sesiones',
            ])
            ->with('status', 'Sesión agregada correctamente.');
    }

    public function destroy(
        Request $request,
        Grupo $grupo,
        Sesion $sesion
    ) {
        $this->autorizarGestion($request, $grupo);

        abort_unless(
            (int) $sesion->grupo_id === (int) $grupo->id,
            404
        );

        if ($sesion->asistencias()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar esta sesión porque ya tiene asistencia registrada.'
            );
        }

        $sesion->delete();

        return redirect()
            ->route('grupos.show', [
                'grupo' => $grupo,
                'tab' => 'sesiones',
            ])
            ->with('status', 'Sesión eliminada correctamente.');
    }
}
