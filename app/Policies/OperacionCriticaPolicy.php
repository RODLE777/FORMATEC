<?php

namespace App\Policies;

use App\Models\User;

/**
 * Backups, restauracion e importacion masiva son EXCLUSIVOS de ROOT.
 * No se exponen ni siquiera de forma oculta a otros roles (seccion 5).
 */
class OperacionCriticaPolicy
{
    public function acceder(User $user): bool
    {
        return $user->esRoot();
    }
}
