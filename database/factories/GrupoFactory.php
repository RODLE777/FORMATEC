<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    protected $model = \App\Models\Grupo::class;

    public function definition(): array
    {
        return [
            'curso_id' => Curso::factory(),
            'codigo_grupo' => strtoupper(fake()->bothify('GRP-###')),
            'anio' => now()->year,
            'mes' => fake()->numberBetween(1, 12),
            'numero_evaluaciones' => 3,
            'estado' => 'PLANIFICADO',
        ];
    }
}
