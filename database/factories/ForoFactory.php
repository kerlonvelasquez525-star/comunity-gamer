<?php

namespace Database\Factories;

use App\Models\Comunidad;
use App\Models\Foro;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Foro> */
class ForoFactory extends Factory
{
    protected $model = Foro::class;

    public function definition(): array
    {
        return [
            'comunidad_id' => Comunidad::factory(),
            'nombre' => fake()->unique()->words(2, true),
            'descripcion' => fake()->sentence(),
            'fecha_creacion' => now(),
        ];
    }
}
