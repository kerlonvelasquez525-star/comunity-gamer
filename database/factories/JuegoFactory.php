<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Juego> */
class JuegoFactory extends Factory
{
    protected $model = Juego::class;

    public function definition(): array
    {
        return [
            'titulo' => fake()->unique()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'desarrollador' => fake()->company(),
            'fecha_lanzamiento' => fake()->date(),
        ];
    }
}
