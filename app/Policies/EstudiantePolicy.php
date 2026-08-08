<?php

namespace App\Policies;

use App\Models\Estudiante;
use App\Models\User;

class EstudiantePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** PROFESOR solo puede ver estudiantes inscritos en sus propios grupos. */
    public function view(User $user, Estudiante $estudiante): bool
    {
        if ($user->puedeGestionarOperacion()) {
            return true;
        }

        if (! $user->esProfesor() || ! $user->profesor) {
            return false;
        }

        return $estudiante->inscripciones()
            ->whereHas('grupo', fn ($q) => $q->where('profesor_id', $user->profesor->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->puedeGestionarOperacion();
    }

    public function update(User $user, Estudiante $estudiante): bool
    {
        return $this->view($user, $estudiante);
    }

    public function delete(User $user, Estudiante $estudiante): bool
    {
        return $user->puedeGestionarOperacion();
    }
}
