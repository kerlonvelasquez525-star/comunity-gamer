<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Models\Amistad;
use App\Models\Canal;
use App\Models\Comentarios;
use App\Models\Comunidad;
use App\Models\Foro;
use App\Models\Hilo;
use App\Models\Idioma;
use App\Models\Juego;
use App\Models\Mensaje;
use App\Models\noticias;
use App\Models\NoticiasComentario;
use App\Models\Notificacion;
use App\Models\Plataforma;
use App\Models\Problemas;
use App\Models\Publicacion;
use App\Models\Reaccion;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );

        User::factory()->count(30)->create();
        $users = User::query()->get();

        $demoTeam = Team::firstOrCreate(
            ['slug' => 'nexus-demo-community'],
            ['name' => 'Nexus Demo Community', 'is_personal' => false],
        );

        foreach ($users as $user) {
            $demoTeam->members()->syncWithoutDetaching([
                $user->id => ['role' => $user->id === $users->first()->id
                    ? TeamRole::Owner->value
                    : TeamRole::Member->value],
            ]);
            $user->forceFill(['current_team_id' => $demoTeam->id])->save();
        }

        $officialNews = noticias::factory()->count(30)->create();

        $minimumNewsComments = max(3, (int) ceil($users->count() * .10));

        foreach ($officialNews as $noticia) {
            $commentCount = random_int($minimumNewsComments, $users->count());

            foreach ($users->random($commentCount) as $user) {
                NoticiasComentario::create([
                    'noticia_id' => $noticia->id,
                    'user_id' => $user->id,
                    'contenido' => fake()->randomElement([
                        'Tiene muy buena pinta esta actualización.',
                        'Ya estoy preparando mi grupo para probarlo.',
                        'Esta mecánica va a cambiar mucho la experiencia.',
                        'La comunidad va a estar muy activa con esto.',
                        'Me encanta la dirección que está tomando.',
                        'Esto parece una mejora muy necesaria.',
                        'Puedo ver mucho potencial en este cambio.',
                        'Muy bien pensado, se nota que han escuchado feedback.',
                    ]),
                ]);
            }
        }

        $publications = collect(range(1, 12))->map(fn (int $index) => Publicacion::create([
            'team_id' => $demoTeam->id,
            'user_id' => $users->random()->id,
            'titulo' => fake()->randomElement([
                'Busco squad para jugar esta noche',
                'Que build estan usando esta temporada?',
                'Clips y jugadas de la comunidad',
                'Recomendaciones para jugadores nuevos',
            ]).' #'.$index,
            'contenido' => fake()->paragraphs(2, true),
        ]));

        foreach ($publications as $publication) {
            foreach (range(1, random_int(2, 4)) as $unused) {
                Comentarios::create([
                    'team_id' => $demoTeam->id,
                    'id_publicacion' => $publication->id,
                    'id_usuario' => $users->random()->id,
                    'contenido' => fake()->randomElement([
                        'Me apunto a esa partida.',
                        'Muy buen aporte, gracias por compartirlo.',
                        'Yo estoy usando una estrategia parecida.',
                        'Podemos organizarlo en el canal de la comunidad.',
                    ]),
                ]);
            }

            foreach ($users->random(random_int(2, 5)) as $user) {
                Reaccion::firstOrCreate(
                    ['id_usuario' => $user->id, 'id_publicacion' => $publication->id],
                    ['tipo' => fake()->randomElement(['like', 'apoyo', 'interesante'])],
                );
            }
        }

        $communities = collect([
            ['name' => 'Zona de Squad', 'description' => 'Encuentra jugadores y arma tu equipo.', 'game' => 'Fortnite', 'platform' => 'Multiplataforma', 'region' => 'LATAM', 'language' => 'Español', 'mode' => 'Casual', 'schedule' => 'Noche', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Estrategia y Guias', 'description' => 'Consejos, builds y rutas compartidas por la comunidad.', 'game' => 'Minecraft', 'platform' => 'PC', 'region' => 'Global', 'language' => 'Español', 'mode' => 'Cooperativa', 'schedule' => 'Flexible', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Torneos Nexus', 'description' => 'Organizacion de eventos y competencias semanales.', 'game' => 'Valorant', 'platform' => 'PC', 'region' => 'Norteamérica', 'language' => 'Inglés', 'mode' => 'Competitiva', 'schedule' => 'Fines de semana', 'image' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=1200&q=80'],
        ])->map(fn (array $data) => Comunidad::updateOrCreate(
            ['team_id' => $demoTeam->id, 'nombre' => $data['name']],
            [
                'descripcion' => $data['description'],
                'imagen_url' => $data['image'],
                'juego_principal' => $data['game'],
                'plataforma' => $data['platform'],
                'region' => $data['region'],
                'idioma' => $data['language'],
                'modalidad' => $data['mode'],
                'horario' => $data['schedule'],
                'tipo' => 'publica',
                'nivel' => $data['mode'] === 'Competitiva' ? 'Pro gamer' : 'Casual',
                'estado' => 'Abierta',
                'creador_id' => $users->random()->id,
            ],
        ));

        foreach ($communities as $index => $community) {
            $memberCount = [12, 7, 18][$index] ?? 10;
            $community->miembros()->syncWithoutDetaching(
                $users->random($memberCount)->mapWithKeys(fn (User $user) => [
                    $user->id => ['rol' => $user->id === $community->creador_id ? 'admin' : 'miembro'],
                ])->all(),
            );

            $channel = Canal::firstOrCreate(
                ['comunidad_id' => $community->id, 'nombre' => 'general'],
                ['tipo' => 'texto', 'fecha_creacion' => now()],
            );

            foreach (range(1, 6) as $unused) {
                Mensaje::create([
                    'id' => (string) Str::uuid(),
                    'canal_id' => $channel->id_canal,
                    'user_id' => $users->random()->id,
                    'contenido' => fake()->randomElement([
                        'Alguien se conecta para la partida de esta noche?',
                        'Ya esta disponible la guia nueva en el foro.',
                        'Bienvenidos a los nuevos miembros del grupo.',
                        'El evento empieza a las 20:00, nos vemos dentro.',
                    ]),
                ]);
            }

            $forum = Foro::firstOrCreate(
                ['comunidad_id' => $community->id, 'nombre' => 'Debates y estrategias'],
                ['descripcion' => 'Conversaciones largas y guias de la comunidad.', 'fecha_creacion' => now()],
            );

            foreach (range(1, 3) as $unused) {
                $thread = Hilo::create([
                    'id_foro' => $forum->id_foro,
                    'id_usuario' => $users->random()->id,
                    'titulo' => fake()->sentence(6),
                    'contenido' => fake()->paragraphs(2, true),
                    'fecha_creacion' => now(),
                ]);

                foreach (range(1, 2) as $reply) {
                    $thread->aportes()->create([
                        'id_usuario' => $users->random()->id,
                        'contenido' => fake()->sentence(14),
                        'fecha_aporte' => now(),
                    ]);
                }
            }
        }

        foreach ([
            ['codigo' => 'es', 'nombre' => 'Español'],
            ['codigo' => 'en', 'nombre' => 'English'],
            ['codigo' => 'pt', 'nombre' => 'Português'],
        ] as $language) {
            Idioma::firstOrCreate(['codigo' => $language['codigo']], ['nombre' => $language['nombre']]);
        }

        $platforms = collect(['PC', 'PlayStation 5', 'Xbox Series', 'Nintendo Switch'])
            ->map(fn (string $name) => Plataforma::firstOrCreate(['nombre' => $name]));
        $languages = Idioma::all();

        foreach (['Apex Legends', 'Valorant', 'Minecraft', 'Elden Ring', 'Rocket League', 'Fortnite'] as $title) {
            $game = Juego::firstOrCreate(
                ['titulo' => $title],
                [
                    'descripcion' => 'Espacio de la comunidad para hablar de '.$title.'.',
                    'desarrollador' => fake()->company(),
                    'fecha_lanzamiento' => fake()->date(),
                ],
            );
            $game->plataformas()->syncWithoutDetaching($platforms->random(random_int(1, 3))->pluck('id'));
            $game->idiomas()->syncWithoutDetaching($languages->random(random_int(1, 2))->pluck('id'));
        }

        foreach ($users->random(min(20, $users->count())) as $user) {
            Problemas::create([
                'team_id' => $demoTeam->id,
                'user_id' => $user->id,
                'titulo' => fake()->randomElement(['Error al conectar con una sala', 'Problema visual en el perfil', 'Sugerencia para mejorar el chat']),
                'descripcion' => fake()->paragraph(),
                'estado' => fake()->randomElement(['abierto', 'en_progreso']),
                'prioridad' => fake()->randomElement(['baja', 'media', 'alta']),
                'plataforma' => fake()->randomElement(['PC', 'PlayStation 5', 'Xbox Series']),
            ]);
        }

        foreach ($users->random(min(12, $users->count())) as $user) {
            Notificacion::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'tipo' => 'community_activity',
                'data' => ['message' => 'Hay nueva actividad en Nexus Demo Community.', 'team_id' => $demoTeam->id],
            ]);
        }

        foreach ($users->random(min(15, $users->count())) as $user) {
            $friend = $users->where('id', '!=', $user->id)->random();
            Amistad::firstOrCreate(
                ['user_id' => $user->id, 'amigo_id' => $friend->id],
                ['estado' => 'aceptada'],
            );
        }

        $this->command->info('Demo data ready: '.User::count().' users, shared team '.$demoTeam->slug.', '.Publicacion::count().' publications and '.NoticiasComentario::count().' comments.');
    }
}
