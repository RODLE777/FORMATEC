<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->rol, $roles, true)) {
            // Si la petición viene de un fetch/JS, devolvemos JSON en lugar de HTML
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            
            abort(403, 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}