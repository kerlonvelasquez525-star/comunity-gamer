<?php

namespace Database\Factories;

use App\Models\noticias;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<noticias>
 */
class NoticiasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Patch 3.8: nuevas rutas de raid y recompensas de temporada',
            'Los equipos de esports reparten sus estrategias para la liga de otoño',
            'Actualización del mapa de la temporada con eventos diarios y jefes',
            'Guía rápida para prepararte antes de la llegada del nuevo parche',
            'Novedades del modo cooperativo: objetivos, loot y recompensas',
            'La comunidad descubre secretos ocultos en la nueva zona de PvE',
            'Análisis del balance de armas tras la última actualización',
            'El torneo mensual suma premios y nuevos equipos inscritos',
            'Revisión del rendimiento del servidor y mejoras de estabilidad',
            'Los creadores de contenido prueban la nueva mecánica de movilidad',
            'Llegan nuevas misiones de temporada con desafíos diarios',
            'Actualización del sistema de progreso para guilds y clanes',
        ];

        $sources = [
            'Nexus Journal',
            'Game Update Lab',
            'PlayStation Pulse',
            'Xbox Insider',
            'Steam Newsroom',
            'PC Gamer Weekly',
        ];

        $categories = ['Actualización', 'Evento', 'Análisis', 'Patch', 'Lanzamiento', 'Torneo'];
        $images = [
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1593642634367-d91a135587b5?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?auto=format&fit=crop&w=1200&q=80',
        ];

        return [
            'team_id' => null,
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'titulo' => fake()->randomElement($titles),
            'contenido' => fake()->paragraphs(2, true),
            'categoria' => fake()->randomElement($categories),
            'imagen_url' => fake()->randomElement($images),
            'fuente_nombre' => fake()->randomElement($sources),
            'fuente_url' => fake()->url(),
            'es_oficial' => true,
            'created_at' => fake()->dateTimeBetween('-45 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
