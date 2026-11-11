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
use App\Models\MensajePrivado;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $primaryUser = User::updateOrCreate(
            ['email' => 'admin@nexuscommunity.test'],
            [
                'name' => 'Nexus Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ],
        );

        $gamerNames = [
            'ShadowKnight', 'PixelReaper', 'VaporNova', 'LevelZero', 'RiftEcho', 'AfterGlow',
            'JinxRunner', 'NeonBasilisk', 'CyanRook', 'PhantomLynx', 'DragonPulse', 'CrimsonZero',
            'VectorVolt', 'ArcaneFox', 'NightCinder', 'VoltTrooper', 'GhostByte', 'LunaTitan',
            'QuestBunny', 'FrostKite', 'AstraRune', 'SteelManta', 'GlitchGhost', 'StormJager',
            'EchoNova', 'MonarchByte', 'DarkPine', 'FluxMage', 'OnyxPixel', 'ThunderVibe',
            'BlueComet', 'GoldenHex', 'BlueOrbit', 'MagmaDrift', 'ZenithAce', 'NightThread',
            'WardenHex', 'NovaPixel', 'SolarFury', 'KineticBloom', 'DuneRhythm', 'Hexblade',
        ];

        $users = collect();
        $users->push($primaryUser);

        foreach ($gamerNames as $index => $gamerName) {
            $user = User::updateOrCreate(
                ['email' => 'demo'.($index + 1).'@nexuscommunity.test'],
                [
                    'name' => $gamerName,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                ],
            );

            $users->push($user);
        }

        $users = User::query()->orderBy('id')->get();

        $demoTeam = Team::firstOrCreate(
            ['slug' => 'nexus-demo-community'],
            [
                'name' => 'Nexus Demo Community',
                'is_personal' => false,
            ],
        );

        foreach ($users as $user) {
            $demoTeam->members()->syncWithoutDetaching([
                $user->id => [
                    'role' => $user->id === $primaryUser->id ? TeamRole::Owner->value : TeamRole::Member->value,
                ],
            ]);
            $user->forceFill(['current_team_id' => $demoTeam->id])->save();
        }

        $this->seedPrivateConversations($users, $primaryUser);
        $this->seedOfficialNews($users);
        $this->call(CommunityNewsSeeder::class);
        $this->seedPublications($demoTeam, $users);
        $this->seedCommunities($demoTeam, $users);
        $this->seedCatalogs();
        $this->seedSupportIssues($demoTeam, $users);
        $this->seedNotifications($demoTeam, $users);
        $this->seedFriendships($users);

        $this->command->info(
            'Demo data ready: '.User::count().' users, shared team '.$demoTeam->slug.', '.Publicacion::count().' publications, '.noticias::count().' official posts and '.NoticiasComentario::count().' comments.'
        );
    }

    protected function seedPrivateConversations(Collection $users, User $primaryUser): void
    {
        $contacts = $users->where('id', '!=', $primaryUser->id)->take(6);

        foreach ($contacts as $index => $contact) {
            $conversationExists = MensajePrivado::query()
                ->where(fn ($query) => $query->where('id_emisor', $primaryUser->id)->where('id_receptor', $contact->id))
                ->orWhere(fn ($query) => $query->where('id_emisor', $contact->id)->where('id_receptor', $primaryUser->id))
                ->exists();

            if ($conversationExists) {
                continue;
            }

            $starterMessages = [
                'Hola, ¿te interesa una partida de ranked esta noche?',
                'He visto que estás en la comunidad de Valorant. ¿Te apuntas?',
                '¿Quieres formar equipo para el torneo del fin de semana?',
                'Acabo de publicar una guía para mejorar la coordinación del squad.',
                'Te recomiendo el canal del equipo, hay mucha actividad esta semana.',
                'Nos vemos en la sala para probar nuevas estrategias.',
            ];

            MensajePrivado::query()->create([
                'id_emisor' => $contact->id,
                'id_receptor' => $primaryUser->id,
                'contenido' => $starterMessages[$index % count($starterMessages)],
                'leido' => false,
            ]);

            MensajePrivado::query()->create([
                'id_emisor' => $primaryUser->id,
                'id_receptor' => $contact->id,
                'contenido' => '¡Claro! Me encantaría. Vamos a organizar la partida y hablar de la estrategia.',
                'leido' => true,
            ]);
        }
    }

    protected function seedOfficialNews(Collection $users): void
    {
        $newsData = [
            ['title' => 'Nexus League abre inscripciones para la temporada de invierno', 'content' => 'La comunidad prepara la primera edición de la liga competitiva del año con torneos regionales y premios para los equipos mejor clasificados.'],
            ['title' => 'Se lanza el calendario de eventos comunitarios del mes', 'content' => 'Durante este mes habrá partidas nocturnas, torneos de construcción, scrims y talleres de estrategia para nuevos jugadores.'],
            ['title' => 'Nexus Academy ofrece sesiones de mejora para jugadores nuevos', 'content' => 'Los miembros con más experiencia abrirán workshops sobre comunicación, posicionamiento y gestión de equipo para principiantes.'],
            ['title' => 'Comunidad de Minecraft crea una ciudad de prueba cooperativa', 'content' => 'Un grupo de miembros diseñó un mapa de supervivencia con retos diarios, rutas de recursos y construcciones colaborativas.'],
            ['title' => 'Pautas para organizar partidas con claridad y respeto', 'content' => 'La comunidad publicó una guía para coordinar squads, definir roles y mantener una experiencia agradable para todos.'],
            ['title' => 'Nexus Awards celebra a la comunidad del mes', 'content' => 'El evento reconoce contribuciones en contenido, apoyo, mentoría y participación en torneos y foros.'],
            ['title' => 'Se abre un espacio para compartir montajes y clips del mes', 'content' => 'Los usuarios podrán publicar sus mejores jugadas, combinaciones y momentos épicos dentro del feed de la comunidad.'],
            ['title' => 'Talleres de análisis de partidas en directo', 'content' => 'Se organizan sesiones para revisar jugadas, estrategias y decisiones tácticas con comentarios de la comunidad.'],
            ['title' => 'La comunidad lanza un proyecto de mentoría por niveles', 'content' => 'Jugadores experimentados acompañarán a nuevos miembros para acompañarles en su evolución y adaptación a la plataforma.'],
            ['title' => 'Nexus Radio presenta una playlist de motivación para jugar', 'content' => 'Un hilo musical comunitario acompaña sesiones de práctica y partidas amistosas con energía y ritmo.'],
            ['title' => 'Nueva zona de entrenamiento para squads de alto nivel', 'content' => 'Una sala práctica especial permite coordinar ejecuciones, rotaciones y timing de objetivos en partidas competitivas.'],
            ['title' => 'Torneo nocturno de construcción y supervivencia', 'content' => 'Los equipos tienen una semana para preparar defensas, rutas de recursos y estrategias de expansión en mapas personalizados.'],
            ['title' => 'Foro de estrategias: mejores rutas para esta temporada', 'content' => 'La comunidad comparte una guía visual con rutas más seguras, momentos clave y decisiones útiles para principiantes y veteranos.'],
            ['title' => 'Se anuncia la segunda edición del International Nexus Cup', 'content' => 'El torneo comunitario aumenta su alcance con nuevas categorías, premios y apoyo para equipos locales y regionales.'],
            ['title' => 'Sesión de análisis de partidas con comentarios en vivo', 'content' => 'La comunidad recibirá feedback directo de jugadores con experiencia para mejorar la coordinación de cada equipo.'],
            ['title' => 'Nexus Marketplace abre un espacio para contenido de comunidad', 'content' => 'Se habilita un área para compartir recursos, guides, ideas y material útil para crear contenido del club.'],
            ['title' => 'Se celebrará la semana de mentoría de nuevo ingreso', 'content' => 'Jugadores experimentados acompañarán a los recién llegados para explicar sistema de equipos, canales y torneos.'],
            ['title' => 'Los equipos de apoyo aportan nuevas recomendaciones para squads', 'content' => 'La comunidad identifica mejores prácticas para sostener comunicación, roles y trabajo en equipo durante largas partidas.'],
            ['title' => 'Se presentan los resultados del primer torneo de la comunidad', 'content' => 'Los líderes del club comparten estadísticas, mejores jugadas y retrospectivas de la primera edición.'],
            ['title' => 'Nexus Social Week abre actividades para todas las edades', 'content' => 'La comunidad prepara partidas amistosas, chats, dinámicas de cohesión y torneos para miembros nuevos y veteranos.'],
            ['title' => 'Lanzamiento de la guía de comunicación en voz y texto', 'content' => 'Un estudio sencillo explica cómo mantener una coordinación clara, rápida y respetuosa durante partidos competitivos.'],
            ['title' => 'Se crea una sala de clips y montajes para la comunidad', 'content' => 'Los miembros podrán compartir sus mejores jugadas, momentos épicos y retos personales para enriquecer la experiencia colectiva.'],
            ['title' => 'Nexus Crew confirma más actividades para este fin de semana', 'content' => 'Habrá partidas de entrenamiento, preguntas rápidas y mesas de apoyo para el próximo evento abierto de la comunidad.'],
            ['title' => 'Nuevo canal de ayuda para jugadores nuevos', 'content' => 'La comunidad prepara un espacio de apoyo para resolver dudas de configuración, roles y adaptación al ecosistema.'],
            ['title' => 'Análisis comunitario de la temporada: equipos y tendencias', 'content' => 'Los expertos locales revisan las principales tendencias del meta, estilos de juego y estrategias más exitosas.'],
            ['title' => 'La comunidad activa un sistema de reconocimiento por ayuda', 'content' => 'Los miembros con mayor apoyo serán destacados para motivar la colaboración y el espíritu del club.'],
            ['title' => 'Se habilitan partidas de prueba por modalidad y rango', 'content' => 'Los usuarios pueden probar la experiencia según su nivel y preferencia, con equipos adecuados para cada caso.'],
            ['title' => 'Nexus Arena prepara un calendario completo para la próxima quincena', 'content' => 'Se suman competiciones rápidas, sesiones de práctica y actividades sociales para mantener a la comunidad activa.'],
            ['title' => 'Nuevas recomendaciones para crear squads equilibrados', 'content' => 'La comunidad comparte ideas para formar grupos con combinación adecuada de roles y objetivos.'],
            ['title' => 'Se suman más streamers de la comunidad a la programación', 'content' => 'Los contenidos en directo permitirán ver análisis, partidas y vivo del club con más interacción desde la comunidad.'],
            ['title' => 'Nexus Team House abre convocatoria para voluntarios comunitarios', 'content' => 'Se buscan personas para apoyar torneos, moderación y actividades de soporte dentro de la comunidad.'],
        ];

        $comments = [
            'La logística del torneo se ve bastante sólida.',
            'Esto va a ayudar muchísimo a los nuevos miembros.',
            'Me gusta la idea de tener encuentros para practicar en equipo.',
            'La comunidad cada vez se siente más activa.',
            'Espero ver más sesiones de análisis de partidas.',
            'Este tipo de iniciativas hace crecer a la comunidad.',
            'Muy buen trabajo del equipo organizador.',
            'Esto va a animar a más gente a participar.',
            'Lo mejor es que incluye a jugadores de todos los niveles.',
            'Una comunidad con objetivos claros siempre crece mejor.',
        ];

        $officialPosts = collect($newsData)->map(function (array $data, int $index) use ($users): noticias {
            return noticias::updateOrCreate(
                ['team_id' => null, 'es_oficial' => true, 'titulo' => $data['title']],
                [
                    'user_id' => $users->values()->get($index % $users->count())->id,
                    'contenido' => $data['content'],
                    'categoria' => 'Comunidad',
                    'imagen_url' => fake()->imageUrl(1200, 800, 'gaming', true, 'NexusCommunity'),
                    'fuente_nombre' => 'Nexus Community',
                    'fuente_url' => 'https://nexus-community.test/noticias/' . ($index + 1),
                    'es_oficial' => true,
                ],
            );
        });

        foreach ($officialPosts as $index => $post) {
            foreach ([0, 1, 2] as $commentOffset) {
                $commentBody = $comments[$index % count($comments)] . ' — ' . ['comentario principal', 'respuesta comunitaria', 'apoyo del equipo'][$commentOffset];

                NoticiasComentario::firstOrCreate(
                    ['noticia_id' => $post->id, 'contenido' => $commentBody],
                    ['user_id' => $users->values()->get(($index + $commentOffset + 2) % $users->count())->id],
                );
            }
        }
    }

    protected function seedPublications(Team $demoTeam, Collection $users): void
    {
        $publicationTemplates = [
            'Busco squad para jugar esta noche y mejorar el rank.',
            '¿Alguien quiere probar una build nueva para esta temporada?',
            'Compartiendo mis clips de la última partida competitiva.',
            'Colección de consejos para mejorar la comunicación del equipo.',
            'Hay partido abierto para formar equipo en la comunidad.',
            'Recomiendo este setup para mejorar el rendimiento en ranked.',
        ];

        $publications = collect(range(1, 14))->map(function (int $index) use ($demoTeam, $users, $publicationTemplates): Publicacion {
            $title = $publicationTemplates[$index % count($publicationTemplates)] . ' #' . $index;

            return Publicacion::create([
                'team_id' => $demoTeam->id,
                'user_id' => $users->random()->id,
                'titulo' => $title,
                'contenido' => fake()->paragraphs(2, true),
            ]);
        });

        foreach ($publications as $publication) {
            foreach (range(1, random_int(2, 5)) as $unused) {
                Comentarios::create([
                    'team_id' => $demoTeam->id,
                    'id_publicacion' => $publication->id,
                    'id_usuario' => $users->random()->id,
                    'contenido' => fake()->randomElement([
                        'Se ve muy bien, me apunto.',
                        'Muy útil, gracias por compartirlo.',
                        'Yo también estoy usando algo parecido.',
                        'Podemos organizar una sesión en el canal general.',
                        'Excelente aporte para la comunidad.',
                    ]),
                ]);
            }

            foreach ($users->random(random_int(2, 6)) as $user) {
                Reaccion::firstOrCreate(
                    ['id_usuario' => $user->id, 'id_publicacion' => $publication->id],
                    ['tipo' => fake()->randomElement(['like', 'apoyo', 'interesante'])],
                );
            }
        }
    }

    protected function seedCommunities(Team $demoTeam, Collection $users): void
    {
        $communityGroups = [
            ['name' => 'Zona de Squad', 'description' => 'Equipo competitivo para ranked y torneos semanales.', 'game' => 'Valorant', 'platform' => 'PC', 'region' => 'LATAM', 'language' => 'Español', 'mode' => 'Competitiva', 'schedule' => 'Noche', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Estrategia y Guias', 'description' => 'Compartimos builds, estrategias y recomendaciones para maximizar el rendimiento.', 'game' => 'Fortnite', 'platform' => 'PC', 'region' => 'Global', 'language' => 'Español', 'mode' => 'Casual', 'schedule' => 'Flexible', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Torneos Nexus', 'description' => 'Exploración, supervivencia y construcción para partidas cooperativas.', 'game' => 'Minecraft', 'platform' => 'PC', 'region' => 'Global', 'language' => 'Español', 'mode' => 'Cooperativa', 'schedule' => 'Tarde', 'image' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Carreras Nocturnas', 'description' => 'Club de pilotos casuales y competiciones amistosas.', 'game' => 'Rocket League', 'platform' => 'Xbox Series', 'region' => 'Europa', 'language' => 'Español', 'mode' => 'Casual', 'schedule' => 'Noche', 'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Nexus Tactical', 'description' => 'Análisis, game sense y organización para partidas competitivas.', 'game' => 'Apex Legends', 'platform' => 'PlayStation 5', 'region' => 'Norteamérica', 'language' => 'Inglés', 'mode' => 'Competitiva', 'schedule' => 'Fines de semana', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Arena Creativa', 'description' => 'Espacio para explorar ideas, clips y desafíos creativos.', 'game' => 'Elden Ring', 'platform' => 'Nintendo Switch', 'region' => 'LATAM', 'language' => 'Español', 'mode' => 'Social', 'schedule' => 'Flexible', 'image' => 'https://images.unsplash.com/photo-1528819622761-6bcf032f3f1d?auto=format&fit=crop&w=1200&q=80'],
        ];

        collect($communityGroups)->each(function (array $data, int $index) use ($demoTeam, $users): void {
            $community = Comunidad::updateOrCreate(
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
                    'rango' => $data['mode'] === 'Competitiva' ? 'Gold+' : 'Todos',
                    'estado' => 'Abierta',
                    'max_miembros' => 60,
                    'creador_id' => $users->values()->get(($index + 2) % $users->count())->id,
                ],
            );

            $members = $users
                ->where('id', '!=', $community->creador_id)
                ->random(min(12, $users->count() - 1))
                ->push($users->firstWhere('id', $community->creador_id));

            $community->miembros()->syncWithoutDetaching(
                $members->mapWithKeys(fn (User $user) => [
                    $user->id => ['rol' => $user->id === $community->creador_id ? 'admin' : 'miembro'],
                ])->all(),
            );

            $generalChannel = Canal::firstOrCreate(
                ['comunidad_id' => $community->id, 'nombre' => 'general'],
                ['tipo' => 'texto', 'fecha_creacion' => now()],
            );

            foreach (range(1, 4) as $messageCounter) {
                Mensaje::create([
                    'id' => (string) Str::uuid(),
                    'canal_id' => $generalChannel->id_canal,
                    'user_id' => $users->random()->id,
                    'contenido' => fake()->randomElement([
                        '¡Buenas! ¿Alguien se apunta para la partida de esta tarde?',
                        'Ajusté la estrategia del grupo y va bastante bien.',
                        'Hay nueva guía en el foro para esta semana.',
                        'La próxima sesión queda abierta para nuevos jugadores.',
                    ]),
                ]);
            }

            $forum = Foro::firstOrCreate(
                ['comunidad_id' => $community->id, 'nombre' => 'Estrategias y debates'],
                ['descripcion' => 'Compartimos guías, teoría, debates y mejores prácticas.', 'fecha_creacion' => now()],
            );

            foreach (range(1, 3) as $threadCounter) {
                $thread = Hilo::create([
                    'id_foro' => $forum->id_foro,
                    'id_usuario' => $users->random()->id,
                    'titulo' => fake()->randomElement([
                        'Qué build es mejor para esta temporada',
                        'Ideas para mejorar la coordinación del equipo',
                        'Cómo organizar un grupo nuevo sin perder ritmo',
                    ]),
                    'contenido' => fake()->paragraphs(2, true),
                    'fecha_creacion' => now(),
                ]);

                foreach (range(1, 2) as $replyCounter) {
                    $thread->aportes()->create([
                        'id_usuario' => $users->random()->id,
                        'contenido' => fake()->sentence(14),
                        'fecha_aporte' => now(),
                    ]);
                }
            }
        });
    }

    protected function seedCatalogs(): void
    {
        foreach (['PC', 'PlayStation 5', 'Xbox Series', 'Nintendo Switch'] as $name) {
            Plataforma::firstOrCreate(['nombre' => $name]);
        }

        foreach ([
            ['codigo' => 'es', 'nombre' => 'Español'],
            ['codigo' => 'en', 'nombre' => 'English'],
            ['codigo' => 'pt', 'nombre' => 'Português'],
        ] as $language) {
            Idioma::firstOrCreate(['codigo' => $language['codigo']], ['nombre' => $language['nombre']]);
        }

        $platforms = Plataforma::all();
        $languages = Idioma::all();

        foreach (['Valorant', 'Fortnite', 'Minecraft', 'Rocket League', 'Apex Legends', 'Elden Ring'] as $title) {
            $game = Juego::firstOrCreate(
                ['titulo' => $title],
                [
                    'descripcion' => 'Espacio dedicado a la comunidad para hablar de ' . $title . ', estrategias, contenido y eventos.',
                    'desarrollador' => fake()->company(),
                    'fecha_lanzamiento' => fake()->date(),
                ],
            );

            $game->plataformas()->syncWithoutDetaching($platforms->random(random_int(1, 3))->pluck('id'));
            $game->idiomas()->syncWithoutDetaching($languages->random(random_int(1, 2))->pluck('id'));
        }
    }

    protected function seedSupportIssues(Team $demoTeam, Collection $users): void
    {
        $issueTemplates = [
            ['titulo' => 'Error al conectar con la sala de voz', 'descripcion' => 'La conexión se corta al entrar a la sala de voz y parece afectar a varios miembros del squad.', 'estado' => 'abierto', 'prioridad' => 'alta', 'plataforma' => 'PC'],
            ['titulo' => 'Problema visual en el perfil', 'descripcion' => 'La sección de estadísticas no carga correctamente en algunos navegadores móviles.', 'estado' => 'en_progreso', 'prioridad' => 'media', 'plataforma' => 'PlayStation 5'],
            ['titulo' => 'Sugerencia para mejorar la navegación en comunidad', 'descripcion' => 'Sería útil separar mejor los foros por juego y comunidad para encontrar contenido más rápido.', 'estado' => 'abierto', 'prioridad' => 'baja', 'plataforma' => 'Xbox Series'],
            ['titulo' => 'Fallo al crear una solicitud de ingreso', 'descripcion' => 'El formulario no guarda bien la comunidad principal en algunos casos.', 'estado' => 'en_progreso', 'prioridad' => 'alta', 'plataforma' => 'PC'],
        ];

        foreach ($issueTemplates as $issue) {
            Problemas::create([
                'team_id' => $demoTeam->id,
                'user_id' => $users->random()->id,
                'titulo' => $issue['titulo'],
                'descripcion' => $issue['descripcion'],
                'estado' => $issue['estado'],
                'prioridad' => $issue['prioridad'],
                'plataforma' => $issue['plataforma'],
            ]);
        }
    }

    protected function seedNotifications(Team $demoTeam, Collection $users): void
    {
        $messages = [
            'Tenemos nueva actividad en tu comunidad de Valorant.',
            'Un miembro respondió a tu hilo en estrategias.',
            'Hay un nuevo torneo disponible para equipos de la comunidad.',
            'Una nueva noticia oficial fue publicada por Nexus.',
            'Tienes solicitudes pendientes por revisar en tu equipo.',
        ];

        foreach ($users->random(min(18, $users->count())) as $user) {
            Notificacion::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'tipo' => 'community_activity',
                'data' => [
                    'message' => $messages[array_rand($messages)],
                    'team_id' => $demoTeam->id,
                ],
            ]);
        }
    }

    protected function seedFriendships(Collection $users): void
    {
        foreach ($users as $user) {
            $candidates = $users->where('id', '!=', $user->id);

            if ($candidates->isEmpty()) {
                continue;
            }

            $friend = $candidates->random();

            Amistad::firstOrCreate(
                ['user_id' => $user->id, 'amigo_id' => $friend->id],
                ['estado' => 'aceptada'],
            );
        }
    }
}
