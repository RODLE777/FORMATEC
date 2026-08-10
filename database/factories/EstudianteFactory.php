<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EstudianteFactory extends Factory
{
    protected $model = \App\Models\Estudiante::class;

    public function definition(): array
    {
        return [
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'sexo' => fake()->randomElement(['MASCULINO', 'FEMENINO']),
            'fecha_nacimiento' => fake()->dateTimeBetween('-40 years', '-16 years'),
            'activo' => true,
        ];
    }
}
