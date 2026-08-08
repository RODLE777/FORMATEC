<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso en rutas: ->middleware('rol:ROOT') o ->middleware('rol:ROOT,ADMINISTRADOR')
 * Complementa las Policies: aqui se corta el acceso a nivel de ruta
 * completa; las Policies afinan el acceso a nivel de registro individual.
 */
class EnsureRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->rol, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}
