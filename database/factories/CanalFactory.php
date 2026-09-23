<?php

namespace Database\Factories;

use App\Models\Canal;
use App\Models\Comunidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Canal> */
class CanalFactory extends Factory
{
    protected $model = Canal::class;

    public function definition(): array
    {
        return [
            'comunidad_id' => Comunidad::factory(),
            'nombre' => fake()->unique()->word(),
            'tipo' => 'texto',
            'fecha_creacion' => now(),
        ];
    }
}
