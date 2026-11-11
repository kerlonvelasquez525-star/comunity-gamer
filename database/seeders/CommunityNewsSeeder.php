<?php

namespace Database\Seeders;

use App\Models\noticias;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityNewsSeeder extends Seeder
{
    /**
     * Add original editorial articles inspired by common gaming-community moments.
     */
    public function run(): void
    {
        $authorId = User::query()->value('id');

        $articles = [
            [
                'titulo' => 'Perú vs. México: cuando la rivalidad llega al mando',
                'contenido' => 'Los duelos entre países siempre despiertan pasión, pero en los videojuegos lo mejor es convertir esa energía en partidas amistosas. Armamos esta nota de humor para imaginar un enfrentamiento épico entre comunidades: estrategia, buena conexión y respeto hasta la última ronda. ¿Qué país se lleva la victoria en tu juego favorito?',
                'categoria' => 'Humor gamer',
                'imagen_url' => '/images/community-news/duelo-de-comunidades.svg',
            ],
            [
                'titulo' => 'Volver al lobby también tiene su propio cambio de temporada',
                'contenido' => 'Después de una pausa larga, muchos regresan con nuevas rutinas, menos reflejos y una lista pendiente de partidas. Esta ilustración celebra ese reencuentro con el setup y recuerda que no hace falta jugar perfecto para volver a disfrutar: invita a tu squad, ajusta los controles y empieza con una partida casual.',
                'categoria' => 'Vida gamer',
                'imagen_url' => '/images/community-news/regreso-al-lobby.svg',
            ],
            [
                'titulo' => 'Cuando el parche cambia todo: reírse también es parte del juego',
                'contenido' => 'Una actualización puede convertir una jugada segura en un momento inolvidable. En vez de tomarnos cada derrota como una tragedia, podemos compartir los tropiezos con buen humor y aprender del siguiente intento. ¿Qué cambio de una actualización te obligó a reaprender tu estrategia?',
                'categoria' => 'Conversación',
                'imagen_url' => '/images/community-news/parche-inesperado.svg',
            ],
            [
                'titulo' => 'Nueva temporada, nuevas teorías: la comunidad se pone creativa',
                'contenido' => 'Cada temporada trae preguntas, expectativas y muchas teorías entre jugadores. Antes de dar por cierto cualquier rumor, conviene separar las especulaciones de los anuncios oficiales y disfrutar el intercambio de ideas. ¿Qué mecánica, mapa o desafío te gustaría ver en tu próximo juego?',
                'categoria' => 'Debate gamer',
                'imagen_url' => '/images/community-news/nuevas-teorias.svg',
            ],
            [
                'titulo' => 'Bromas entre gamers: que la risa sea para todos',
                'contenido' => 'Las bromas pueden convertirse en anécdotas memorables cuando todas las personas están cómodas. Si alguien no se ríe, toca parar, aclarar y seguir con respeto. Compartimos esta nota como invitación a mantener el buen ambiente en el chat, el juego y cualquier grupo de amigos.',
                'categoria' => 'Convivencia',
                'imagen_url' => '/images/community-news/broma-con-respeto.svg',
            ],
            [
                'titulo' => 'Humor en el chat de voz: el contexto importa',
                'contenido' => 'En una partida intensa, una frase puede sonar distinta de lo que pretendíamos. Antes de responder con otra provocación, vale la pena preguntar, bajar el tono y cuidar que el chat siga siendo un espacio agradable. Competir fuerte y tratar bien a los demás sí pueden ir de la mano.',
                'categoria' => 'Convivencia',
                'imagen_url' => '/images/community-news/chat-con-respeto.svg',
            ],
            [
                'titulo' => 'La nostalgia también suma puntos: clásicos que siguen reuniendo squads',
                'contenido' => 'Los juegos de otras generaciones siguen creando conversaciones, retos y recuerdos compartidos. Volver a un clásico puede ser una gran oportunidad para invitar a alguien nuevo, descubrir cómo ha cambiado el género y comparar nuestras primeras partidas con las de hoy. ¿Qué juego retro volverías a instalar?',
                'categoria' => 'Cultura gamer',
                'imagen_url' => '/images/community-news/nostalgia-retro.svg',
            ],
        ];

        foreach ($articles as $article) {
            noticias::updateOrCreate(
                ['team_id' => null, 'titulo' => $article['titulo']],
                [
                    ...$article,
                    'user_id' => $authorId,
                    'fuente_nombre' => 'Nexus Community',
                    'fuente_url' => null,
                    'fuente_hash' => null,
                    'es_oficial' => true,
                ],
            );
        }
    }
}
