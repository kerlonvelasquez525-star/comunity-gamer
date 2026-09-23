<?php

namespace Database\Factories;

use App\Models\Publicacion;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Publicacion> */
class PublicacionFactory extends Factory
{
    protected $model = Publicacion::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'titulo' => fake()->sentence(5),
            'contenido' => fake()->paragraphs(2, true),
        ];
    }
}
