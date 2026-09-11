<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotService
{
    /**
     * Return a predefined answer without persisting or forwarding the question.
     */
    public function answer(string $question): array
    {
        $normalized = Str::lower(Str::ascii(trim($question)));

        if ($this->isOnlineUsersQuestion($normalized)) {
            $count = $this->onlineUserCount();

            return [
                'intent' => 'online_users',
                'answer' => sprintf(
                    'En este momento hay %d %s en linea. La cifra corresponde a usuarios autenticados con actividad dentro de la ventana activa de la plataforma.',
                    $count,
                    $count === 1 ? 'usuario' : 'usuarios',
                ),
                'suggestions' => ['Como protegeis mis datos?', 'Como funcionan los equipos?'],
            ];
        }

        foreach ($this->intents() as $intent) {
            foreach ($intent['keywords'] as $keyword) {
                if (Str::contains($normalized, $keyword)) {
                    return [
                        'intent' => $intent['id'],
                        'answer' => $intent['answer'],
                        'suggestions' => $intent['suggestions'],
                    ];
                }
            }
        }

        return [
            'intent' => 'fallback',
            'answer' => 'No encuentro una respuesta para esa consulta. Prueba con privacidad, seguridad, equipos, noticias, chat o reportar contenido.',
            'suggestions' => ['Como protegeis mis datos?', 'Como reporto contenido?', 'Como funcionan los equipos?'],
        ];
    }

    private function isOnlineUsersQuestion(string $question): bool
    {
        return Str::contains($question, [
            'usuarios en linea',
            'usuarios online',
            'usuarios conectados',
            'cuantos usuarios',
            'cuanta gente esta conectada',
            'personas en linea',
        ]);
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
                'keywords' => ['privacidad', 'datos personales', 'mis datos', 'privacy'],
                'answer' => 'Tus mensajes privados se almacenan cifrados. El bot no guarda tus preguntas ni las envia a servicios externos. Puedes solicitar ayuda sobre tus datos desde la configuracion de tu cuenta.',
                'suggestions' => ['Que datos guarda la plataforma?', 'Como elimino mi cuenta?'],
            ],
            [
                'id' => 'security',
                'keywords' => ['seguridad', 'contrasena', 'passkey', '2fa', 'dos factores', 'security'],
                'answer' => 'Activa Passkeys o la autenticacion de dos factores desde Seguridad. No compartas codigos, contrasenas ni enlaces de recuperacion con otros usuarios.',
                'suggestions' => ['Como activo 2FA?', 'Que hago si detecto una cuenta comprometida?'],
            ],
            [
                'id' => 'teams',
                'keywords' => ['equipo', 'equipos', 'team', 'miembro', 'invitacion'],
                'answer' => 'Los equipos separan comunidades, noticias y problemas. Solo los miembros del equipo pueden acceder a su contenido y cada rol limita las acciones administrativas.',
                'suggestions' => ['Como invito a alguien?', 'Que permisos tiene un administrador?'],
            ],
            [
                'id' => 'news',
                'keywords' => ['noticia', 'noticias', 'rss', 'oficial'],
                'answer' => 'Las noticias oficiales se sincronizan desde fuentes configuradas por la plataforma. El contenido externo conserva su enlace de origen para que puedas verificarlo.',
                'suggestions' => ['Como comento una noticia?', 'Como se verifica una fuente?'],
            ],
            [
                'id' => 'chat',
                'keywords' => ['chat', 'mensaje', 'mensajes', 'conversacion'],
                'answer' => 'El chat privado solo muestra la conversacion entre sus participantes. Los mensajes se cifran en la base de datos y puedes evitar compartir informacion sensible.',
                'suggestions' => ['Como bloqueo a un usuario?', 'Como reporto un mensaje?'],
            ],
            [
                'id' => 'report',
                'keywords' => ['reportar', 'reporte', 'denunciar', 'acoso', 'spam', 'contenido'],
                'answer' => 'Usa los reportes de moderacion para denunciar acoso, spam o contenido inseguro. Incluye contexto concreto y no publiques datos personales en el reporte.',
                'suggestions' => ['Que puedo reportar?', 'Como contacto con soporte?'],
            ],
            [
                'id' => 'account',
                'keywords' => ['cuenta', 'eliminar mi cuenta', 'perfil', 'correo'],
                'answer' => 'Puedes gestionar tu perfil, sesiones, Passkeys, 2FA y eliminacion de cuenta desde Configuracion. La eliminacion de cuenta requiere confirmacion y no debe hacerse desde enlaces recibidos por chat.',
                'suggestions' => ['Como cambio mi correo?', 'Como cierro mis sesiones?'],
            ],
        ];
    }
}
