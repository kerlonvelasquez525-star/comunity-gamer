<?php

namespace Database\Factories;

use App\Models\ReporteSoporte;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReporteSoporte> */
class ReporteSoporteFactory extends Factory
{
    protected $model = ReporteSoporte::class;

    public function definition(): array
    {
        return [
            'id_usuario' => User::factory(),
            'asunto' => fake()->sentence(6),
            'descripcion' => fake()->paragraphs(2, true),
            'estado' => 'abierto',
            'fecha_creacion' => now(),
        ];
    }
}
