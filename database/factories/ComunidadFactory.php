<?php

namespace Database\Factories;

use App\Models\Comunidad;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comunidad>
 */
class ComunidadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'nombre' => fake()->unique()->sentence(3),
            'descripcion' => fake()->sentence(),
            'creador_id' => User::factory(),
        ];
    }
}
