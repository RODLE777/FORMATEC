<?php

namespace App\Http\Middleware;

use App\Models\Grupo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarGrupoAsignado
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless($user, 401);

        if (in_array($user->rol, ['ROOT', 'ADMINISTRADOR'], true)) {
            return $next($request);
        }

        abort_unless(
            $user->esProfesor() && $user->profesor,
            403,
            'No tienes una ficha de profesor asociada.'
        );

        $parametro = $request->route('grupo');

        $grupo = $parametro instanceof Grupo
            ? $parametro
            : Grupo::findOrFail($parametro);

        abort_unless(
            (int) $grupo->profesor_id ===
            (int) $user->profesor->id,
            403,
            'No tienes acceso a este grupo.'
        );

        $inscripcion = $request->route('inscripcion');

        if ($inscripcion !== null) {
            $grupoInscripcion = is_object($inscripcion)
                ? $inscripcion->grupo_id
                : \App\Models\Inscripcion::findOrFail(
                    $inscripcion
                )->grupo_id;

            abort_unless(
                (int) $grupoInscripcion === (int) $grupo->id,
                403,
                'Esta inscripción no pertenece al grupo.'
            );
        }

        $sesion = $request->route('sesion');

        if ($sesion !== null) {
            $grupoSesion = is_object($sesion)
                ? $sesion->grupo_id
                : \App\Models\Sesion::findOrFail(
                    $sesion
                )->grupo_id;

            abort_unless(
                (int) $grupoSesion === (int) $grupo->id,
                403,
                'Esta sesión no pertenece al grupo.'
            );
        }

        return $next($request);
    }
}
