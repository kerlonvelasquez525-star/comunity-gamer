<?php

namespace Database\Factories;

use App\Models\Aporte;
use App\Models\Hilo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Aporte> */
class AporteFactory extends Factory
{
    protected $model = Aporte::class;

    public function definition(): array
    {
        return [
            'id_hilo' => Hilo::factory(),
            'id_usuario' => User::factory(),
            'contenido' => fake()->paragraph(),
            'fecha_aporte' => now(),
        ];
    }
}
