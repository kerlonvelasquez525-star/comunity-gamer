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
            'imagen_url' => fake()->randomElement([
                'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80',
            ]),
            'creador_id' => User::factory(),
        ];
    }
}
