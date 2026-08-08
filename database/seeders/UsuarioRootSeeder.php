<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea la primera cuenta ROOT para poder entrar al sistema. A partir de
 * aqui, ROOT crea el resto de cuentas desde el modulo Usuarios — no hay
 * registro publico (seccion 5 del prompt maestro).
 *
 * IMPORTANTE: cambia esta contrasena inmediatamente despues del primer
 * login, desde /password.
 */
class UsuarioRootSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'root@formatec.local'],
            [
                'name' => 'Administrador ROOT',
                'password' => Hash::make('CambiarInmediatamente123!'),
                'rol' => 'ROOT',
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
