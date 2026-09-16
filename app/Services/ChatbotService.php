<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotService
{
    /**
     * Devuelve una respuesta predefinida sin guardar ni reenviar la pregunta.
     */
    public function answer(string $question): array
    {
        $normalized = Str::lower(Str::ascii(trim($question)));

        if ($this->isOnlineUsersQuestion($normalized)) {
            return $this->onlineUsersResponse();
        }

        $intent = $this->bestIntent($normalized);

        if ($intent !== null) {
            return [
                'intent' => $intent['id'],
                'answer' => $intent['answer'],
                'suggestions' => $intent['suggestions'],
            ];
        }

        return [
            'intent' => 'fallback',
            'answer' => 'No encuentro una respuesta para esa consulta. Prueba con privacidad, seguridad, equipos, noticias, chat o reportar contenido.',
            'suggestions' => ['¿Cómo protegen mis datos?', '¿Cómo reporto contenido?', '¿Cómo funcionan los equipos?'],
        ];
    }

    /**
     * @return array{intent: string, answer: string, suggestions: array<int, string>}
     */
    private function onlineUsersResponse(): array
    {
        $count = $this->onlineUserCount();

        return [
            'intent' => 'online_users',
            'answer' => sprintf(
                'Ahora mismo hay %d %s en línea. La cifra incluye usuarios autenticados con actividad dentro de la ventana activa de la plataforma.',
                $count,
                $count === 1 ? 'usuario' : 'usuarios',
            ),
            'suggestions' => ['¿Cómo protegeis mis datos?', '¿Cómo funcionan los equipos?'],
        ];
    }

    /**
     * Prioriza coincidencias específicas de varias palabras sobre términos generales.
     *
     * @return array{id: string, keywords: array<int, string>, answer: string, suggestions: array<int, string>}|null
     */
    private function bestIntent(string $question): ?array
    {
        $bestIntent = null;
        $bestScore = 0;

        foreach ($this->intents() as $intent) {
            $score = 0;

            foreach ($intent['keywords'] as $keyword) {
                if (Str::contains($question, $keyword)) {
                    $score += str_contains($keyword, ' ') ? 3 : 1;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIntent = $intent;
            }
        }

        return $bestIntent;
    }

    private function isOnlineUsersQuestion(string $question): bool
    {
        return preg_match('/\b(cuantos|cuantas|numero|cantidad)\b.*\b(usuarios?|personas?)\b.*\b(linea|online|conectad)/', $question) === 1
            || preg_match('/\b(usuarios?|personas?)\b.*\b(linea|online|conectad)/', $question) === 1;
    }

    private function onlineUserCount(): int
    {
        $cutoff = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;

        return (int) DB::table(config('session.table', 'sessions'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $cutoff)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * @return array<int, array{id: string, keywords: array<int, string>, answer: string, suggestions: array<int, string>}
     */
    private function intents(): array
    {
        return [
            [
                'id' => 'privacy',
                'keywords' => ['privacidad', 'datos personales', 'mis datos', 'protegen mis datos', 'privacy'],
                'answer' => 'Tus mensajes privados se almacenan cifrados. Esta asistencia no guarda tus preguntas ni las envía a servicios externos. Para gestionar tus datos, abre Configuración y revisa las opciones de cuenta y seguridad.',
                'suggestions' => ['¿Qué datos guarda la plataforma?', '¿Cómo elimino mi cuenta?'],
            ],
            [
                'id' => 'security',
                'keywords' => ['seguridad', 'contrasena', 'passkey', '2fa', 'dos factores', 'autenticacion', 'security'],
                'answer' => 'Puedes activar Passkeys o la autenticación de dos factores desde Seguridad. Nunca compartas códigos, contraseñas ni enlaces de recuperación. Si sospechas un acceso no autorizado, cierra tus sesiones y cambia tu contraseña.',
                'suggestions' => ['¿Cómo activo 2FA?', '¿Qué hago si detecto una cuenta comprometida?'],
            ],
            [
                'id' => 'teams',
                'keywords' => ['equipo', 'equipos', 'team', 'miembro', 'invitacion', 'permisos'],
                'answer' => 'Los equipos separan comunidades, noticias y problemas. Solo sus miembros pueden acceder al contenido, y cada rol limita las acciones administrativas.',
                'suggestions' => ['¿Cómo invito a alguien?', '¿Qué permisos tiene un administrador?'],
            ],
            [
                'id' => 'news',
                'keywords' => ['noticia', 'noticias', 'rss', 'oficial'],
                'answer' => 'Las noticias oficiales se sincronizan desde las fuentes configuradas por la plataforma. Cada noticia externa conserva su enlace de origen para que puedas verificarla.',
                'suggestions' => ['¿Cómo comento una noticia?', '¿Cómo se verifica una fuente?'],
            ],
            [
                'id' => 'chat',
                'keywords' => ['chat', 'mensaje', 'mensajes', 'conversacion'],
                'answer' => 'El chat privado solo muestra la conversación entre sus participantes. Los mensajes se cifran en la base de datos. No compartas información sensible ni credenciales.',
                'suggestions' => ['¿Cómo bloqueo a un usuario?', '¿Cómo reporto un mensaje?'],
            ],
            [
                'id' => 'report',
                'keywords' => ['reportar', 'reporto', 'reporte', 'denunciar', 'denuncio', 'acoso', 'spam', 'contenido inseguro'],
                'answer' => 'Usa los reportes de moderación para denunciar acoso, spam o contenido inseguro. Describe hechos concretos, añade el contexto necesario y no incluyas datos personales innecesarios.',
                'suggestions' => ['¿Qué puedo reportar?', '¿Cómo contacto con soporte?'],
            ],
            [
                'id' => 'account',
                'keywords' => ['cuenta', 'eliminar mi cuenta', 'perfil', 'correo'],
                'answer' => 'Puedes gestionar tu perfil, sesiones, Passkeys, 2FA y eliminación de cuenta desde Configuración. Confirma siempre estas acciones desde la plataforma, no desde enlaces recibidos por chat.',
                'suggestions' => ['¿Cómo cambio mi correo?', '¿Cómo cierro mis sesiones?'],
            ],
        ];
    }
}
