<?php

namespace App\Policies;

use App\Models\Grupo;
use App\Models\User;

/**
 * PROFESOR solo ve/gestiona SUS PROPIOS grupos (grupos.profesor_id).
 * Esta restriccion se aplica aqui (policy) y TAMBIEN en el controlador
 * via scope de query — nunca solo en el menu/UI. Ver seccion 5 del
 * prompt maestro.
 */
class GrupoPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // cada rol ve su propio conjunto, filtrado en el controlador
    }

    public function view(User $user, Grupo $grupo): bool
    {
        if ($user->puedeGestionarOperacion()) {
            return true;
        }

        return $user->esProfesor()
            && $user->profesor?->id === $grupo->profesor_id;
    }

    public function create(User $user): bool
    {
        return $user->puedeGestionarOperacion();
    }

    public function update(User $user, Grupo $grupo): bool
    {
        if ($user->puedeGestionarOperacion()) {
            return true;
        }

        // El profesor puede actualizar datos academicos (asistencia/notas)
        // de su propio grupo, pero no los datos administrativos del grupo.
        return $user->esProfesor()
            && $user->profesor?->id === $grupo->profesor_id;
    }

    public function delete(User $user, Grupo $grupo): bool
    {
        return $user->puedeGestionarOperacion();
    }
}
