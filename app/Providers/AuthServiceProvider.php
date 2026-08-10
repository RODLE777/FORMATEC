<?php

namespace App\Providers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Policies\EstudiantePolicy;
use App\Policies\GrupoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Grupo::class, GrupoPolicy::class);
        Gate::policy(Estudiante::class, EstudiantePolicy::class);

        // Gates simples para operaciones criticas exclusivas de ROOT
        // (backups, restauracion, importacion masiva).
        Gate::define('gestionar-backups', fn ($user) => $user->esRoot());
        Gate::define('importar-excel', fn ($user) => $user->esRoot());
        Gate::define('gestionar-usuarios', fn ($user) => $user->esRoot());
        Gate::define('gestionar-operacion', fn ($user) => $user->puedeGestionarOperacion());
    }
}
