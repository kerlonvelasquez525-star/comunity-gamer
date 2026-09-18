<?php

namespace Database\Factories;

use App\Models\Comentarios;
use App\Models\noticias;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comentarios>
 */
class ComentariosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $noticia = noticias::query()->inRandomOrder()->first() ?? noticias::factory()->create();

        return [
            'noticia_id' => $noticia->id,
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'contenido' => fake()->randomElement([
                'Esto va a cambiar mucho la forma de jugar en equipo.',
                'No me esperaba este tipo de mejora; está muy bien pensada.',
                'Yo ya lo probé y la diferencia se nota bastante.',
                'La comunidad va a disfrutar mucho este cambio.',
                'Parece una actualización con mucha más variedad.',
                'Muy buen aporte; esto ayuda mucho a la coordinación.',
                'Me gusta cómo están equilibrando el contenido nuevo.',
                'Esto ya se siente más vivo y más completo.',
                'Buen análisis; aporta mucho a la experiencia.',
                'Se ve muy interesante, sobre todo para grupos nuevos.',
            ]),
            'created_at' => fake()->dateTimeBetween('-15 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
