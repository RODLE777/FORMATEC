<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'rol' => 'ADMINISTRADOR',
            'activo' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function root(): static
    {
        return $this->state(['rol' => 'ROOT']);
    }

    public function profesor(): static
    {
        return $this->state(['rol' => 'PROFESOR']);
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }
}
