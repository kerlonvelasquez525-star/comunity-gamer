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
        $testUser = User::updateOrCreate(
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

        foreach ($users->where('id', '!=', $testUser->id)->values()->take(3) as $index => $contact) {
            $conversationExists = MensajePrivado::query()
                ->where(function ($query) use ($testUser, $contact): void {
                    $query->where('id_emisor', $testUser->id)
                        ->where('id_receptor', $contact->id);
                })
                ->orWhere(function ($query) use ($testUser, $contact): void {
                    $query->where('id_emisor', $contact->id)
                        ->where('id_receptor', $testUser->id);
                })
                ->exists();

            if ($conversationExists) {
                continue;
            }

            MensajePrivado::query()->create([
                'id_emisor' => $contact->id,
                'id_receptor' => $testUser->id,
                'contenido' => [
                    'Bienvenido a Nexus. Ya tienes una conversación de ejemplo preparada.',
                    '¿Te apuntas a organizar una partida esta semana?',
                    'He dejado este mensaje para que puedas probar el chat privado.',
                ][$index],
                'leido' => false,
            ]);

            MensajePrivado::query()->create([
                'id_emisor' => $testUser->id,
                'id_receptor' => $contact->id,
                'contenido' => '¡Hola! Gracias por escribir. Nos vemos en la comunidad.',
                'leido' => true,
            ]);
        }

        $officialNewsIds = noticias::query()
            ->whereNull('team_id')
            ->where('es_oficial', true)
            ->pluck('id');

        NoticiasComentario::query()->whereIn('noticia_id', $officialNewsIds)->delete();
        noticias::query()->whereIn('id', $officialNewsIds)->delete();

        $newsData = [
            ['title' => 'La astronomía aficionada estrena un mapa de lluvias de meteoros', 'content' => 'Un observatorio comunitario publicó un calendario para localizar lluvias de meteoros durante las próximas noches despejadas.'],
            ['title' => 'Un museo digitaliza su colección de instrumentos antiguos', 'content' => 'La nueva biblioteca virtual permite explorar piezas musicales históricas con fotografías de alta resolución y fichas técnicas.'],
            ['title' => 'El transporte urbano prueba marquesinas con techos verdes', 'content' => 'Varias paradas incorporaron plantas resistentes para estudiar su impacto en la temperatura y la calidad del aire.'],
            ['title' => 'Una biblioteca de barrio amplía su horario de lectura', 'content' => 'El nuevo programa abre las salas hasta la noche y reserva espacios tranquilos para estudiantes y vecinos.'],
            ['title' => 'Científicos observan una colonia de corales en recuperación', 'content' => 'Un seguimiento submarino registró señales positivas en una zona protegida después de varios años de restauración.'],
            ['title' => 'La cocina regional recupera una variedad de trigo', 'content' => 'Agricultores y panaderos colaboran para volver a cultivar una semilla local adaptada a los suelos de la región.'],
            ['title' => 'Un festival de cine independiente anuncia su programación', 'content' => 'La muestra reunirá cortometrajes de doce países y organizará conversaciones abiertas con sus directores.'],
            ['title' => 'El jardín botánico presenta una ruta nocturna', 'content' => 'La visita guiada explicará cómo cambian los aromas y los polinizadores cuando cae el sol.'],
            ['title' => 'Una escuela incorpora talleres de reparación de bicicletas', 'content' => 'El proyecto enseña mantenimiento básico y promueve desplazamientos sostenibles entre el alumnado.'],
            ['title' => 'Una fotógrafa publica un archivo sobre arquitectura rural', 'content' => 'La colección reúne fachadas, molinos y puentes que muestran la evolución de varias comunidades pequeñas.'],
            ['title' => 'El observatorio meteorológico instala sensores en la costa', 'content' => 'Los nuevos dispositivos medirán cambios de viento y humedad para mejorar los avisos locales.'],
            ['title' => 'Un grupo de estudiantes construye un reloj solar público', 'content' => 'El diseño fue instalado en una plaza y permite aprender sobre el movimiento aparente del sol durante el año.'],
            ['title' => 'La orquesta juvenil prepara un concierto al aire libre', 'content' => 'Más de cuarenta músicos ensayarán un repertorio de compositores contemporáneos en un parque municipal.'],
            ['title' => 'Un archivo familiar descubre cartas de hace un siglo', 'content' => 'La correspondencia aporta detalles cotidianos sobre viajes, oficios y celebraciones de una localidad.'],
            ['title' => 'La universidad abre un laboratorio de cerámica experimental', 'content' => 'El espacio ofrecerá herramientas para investigar materiales, esmaltes y técnicas de cocción de bajo consumo.'],
            ['title' => 'Una reserva natural registra el regreso de aves migratorias', 'content' => 'Los equipos de campo identificaron varias especies que no se observaban en la zona desde hacía una década.'],
            ['title' => 'Un mercado local organiza una jornada de intercambio de semillas', 'content' => 'La actividad facilitará que los horticultores compartan variedades tradicionales y conocimientos de cultivo.'],
            ['title' => 'El planetario renueva su proyección sobre las lunas de Júpiter', 'content' => 'La experiencia combina imágenes científicas y narración para explicar las particularidades de Europa, Io, Ganímedes y Calisto.'],
            ['title' => 'Un taller textil recupera técnicas de teñido natural', 'content' => 'El colectivo documentó procesos con plantas locales y creó una pequeña muestra de tejidos artesanales.'],
            ['title' => 'Una plaza estrena mobiliario diseñado por vecinos', 'content' => 'El proyecto participativo transformó bancos y zonas de sombra a partir de propuestas recogidas durante el invierno.'],
            ['title' => 'La estación de tren conserva un mural restaurado', 'content' => 'Especialistas en patrimonio recuperaron los colores originales de una obra pintada en la década de 1960.'],
            ['title' => 'Un centro cultural ofrece clases gratuitas de encuadernación', 'content' => 'Las sesiones enseñarán a coser cuadernos y reutilizar papel para elaborar libretas duraderas.'],
            ['title' => 'El río contará con una nueva red de medición de agua', 'content' => 'Los sensores permitirán consultar variaciones de caudal y temperatura en distintos puntos del recorrido.'],
            ['title' => 'Una compañía de teatro adapta relatos de tradición oral', 'content' => 'La obra mezcla narración, música y sombras chinescas para acercar historias populares a nuevos públicos.'],
            ['title' => 'Un vivero comunitario entrega plantas para balcones', 'content' => 'La iniciativa recomienda especies resistentes y explica cómo crear pequeños espacios verdes en viviendas urbanas.'],
            ['title' => 'La feria científica muestra experimentos con sonido', 'content' => 'El público podrá observar vibraciones, frecuencias y resonancias mediante instrumentos construidos por estudiantes.'],
            ['title' => 'Un grupo de montañismo cartografía senderos históricos', 'content' => 'El mapa colaborativo reúne caminos antiguos, fuentes y refugios para facilitar excursiones responsables.'],
            ['title' => 'La hemeroteca restaura periódicos de la década de 1930', 'content' => 'El trabajo de conservación permitirá consultar noticias locales que hasta ahora solo estaban disponibles en papel frágil.'],
            ['title' => 'Un comedor escolar prueba un menú de temporada', 'content' => 'La propuesta utiliza productos cercanos y cambia cada mes según la disponibilidad de agricultores locales.'],
            ['title' => 'El centro de artesanía inaugura una exposición de vidrio', 'content' => 'La muestra presenta piezas utilitarias y esculturas creadas con técnicas de soplado y reciclaje.'],
        ];

        $newsComments = [
            'La luz de esta mañana cambió por completo el paisaje.',
            'Tengo una taza favorita que siempre uso los domingos.',
            'El sonido de la lluvia ayuda a concentrarse.',
            'Ayer encontré una receta escrita a mano en un cajón.',
            'Las piedras de la orilla tienen formas sorprendentes.',
            'Me gusta observar cómo cambia una ciudad al anochecer.',
            'Un paseo corto puede aclarar muchas ideas.',
            'La sombra de los árboles hacía fresco en la plaza.',
            'Guardar fotografías antiguas permite recordar pequeños detalles.',
            'El olor del pan recién hecho llenó toda la calle.',
            'Hay nubes que parecen dibujos cuando se miran con calma.',
            'Una libreta sencilla resulta útil para anotar pensamientos.',
            'El silencio de una sala vacía también tiene su propio ritmo.',
            'Los colores de una pared cambian según la hora del día.',
            'Aprendí una palabra nueva leyendo una etiqueta.',
            'El café sabe distinto cuando se comparte una conversación.',
            'Un banco al sol puede convertirse en el mejor lugar para descansar.',
            'Las hojas secas forman patrones distintos después del viento.',
            'Encontrar una canción inesperada mejora cualquier trayecto.',
            'El papel reciclado tiene texturas muy particulares.',
            'Una ventana abierta deja entrar sonidos de lugares lejanos.',
            'Las pequeñas reparaciones evitan que los objetos terminen olvidados.',
            'El agua refleja colores que no se ven a simple vista.',
            'Una caminata sin rumbo puede revelar calles desconocidas.',
            'Los objetos heredados conservan historias aunque nadie las escriba.',
            'La paciencia suele ser la mejor herramienta para aprender.',
            'Las semillas parecen pequeñas, pero contienen una posibilidad enorme.',
            'Una fotografía puede guardar más ambiente que una descripción larga.',
            'El viento modifica el paisaje incluso cuando nadie lo nota.',
            'Las conversaciones breves también pueden dejar una buena impresión.',
        ];

        $officialNews = collect($newsData)->map(function (array $data, int $index) use ($users): noticias {
            return noticias::updateOrCreate(
                ['titulo' => $data['title']],
                [
                    'team_id' => null,
                    'user_id' => $users->values()->get($index % $users->count())->id,
                    'contenido' => $data['content'],
                    'categoria' => 'Actualidad',
                    'imagen_url' => null,
                    'fuente_nombre' => 'Nexus Journal',
                    'fuente_url' => 'https://nexus-community.test/noticias/'.$index,
                    'es_oficial' => true,
                ],
            );
        });

        foreach ($officialNews as $index => $noticia) {
            NoticiasComentario::firstOrCreate(
                ['noticia_id' => $noticia->id, 'contenido' => $newsComments[$index]],
                ['user_id' => $users->values()->get(($index + 1) % $users->count())->id],
            );
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
            ['name' => 'Carreras Nocturnas', 'description' => 'Partidas amistosas y campeonatos de Rocket League.', 'game' => 'Rocket League', 'platform' => 'Xbox Series', 'region' => 'Europa', 'language' => 'Español', 'mode' => 'Competitiva', 'schedule' => 'Noche', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Supervivencia Cooperativa', 'description' => 'Construccion, exploracion y aventuras para jugar en grupo.', 'game' => 'Minecraft', 'platform' => 'PC', 'region' => 'Global', 'language' => 'Español', 'mode' => 'Cooperativa', 'schedule' => 'Tarde', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Arena Competitiva', 'description' => 'Encuentra compañeros para mejorar tu rango y tus estrategias.', 'game' => 'Apex Legends', 'platform' => 'PlayStation 5', 'region' => 'Norteamérica', 'language' => 'Inglés', 'mode' => 'Competitiva', 'schedule' => 'Fines de semana', 'image' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Nuevos Aventureros', 'description' => 'Un espacio relajado para aprender y compartir consejos.', 'game' => 'Elden Ring', 'platform' => 'PlayStation 5', 'region' => 'LATAM', 'language' => 'Español', 'mode' => 'Casual', 'schedule' => 'Flexible', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Liga de Constructores', 'description' => 'Proyectos creativos y retos de construccion para todos.', 'game' => 'Minecraft', 'platform' => 'Nintendo Switch', 'region' => 'Global', 'language' => 'Portugués', 'mode' => 'Social', 'schedule' => 'Mañana', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Escuadron Tactico', 'description' => 'Comunicacion, coordinacion y partidas organizadas.', 'game' => 'Valorant', 'platform' => 'PC', 'region' => 'Europa', 'language' => 'Español', 'mode' => 'Competitiva', 'schedule' => 'Noche', 'image' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Pistas y Derrapes', 'description' => 'Comparte circuitos, configuraciones y carreras casuales.', 'game' => 'Rocket League', 'platform' => 'Xbox Series', 'region' => 'LATAM', 'language' => 'Español', 'mode' => 'Casual', 'schedule' => 'Tarde', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80'],
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
            $memberCount = [12, 7, 18, 10, 14, 11, 16, 13, 15, 10][$index] ?? 10;
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
