<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


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
