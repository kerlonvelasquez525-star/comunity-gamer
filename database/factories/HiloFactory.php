<?php

namespace Database\Factories;

use App\Models\Foro;
use App\Models\Hilo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Hilo> */
class HiloFactory extends Factory
{
    protected $model = Hilo::class;

    public function definition(): array
    {
        return [
            'id_foro' => Foro::factory(),
            'id_usuario' => User::factory(),
            'titulo' => fake()->sentence(6),
            'contenido' => fake()->paragraphs(2, true),
            'fecha_creacion' => now(),
        ];
    }
}
