<x-layouts::app :title="__('Dashboard')">
    @vite(['resources/css/dashboard.css', 'resources/css/chat.css'])

    <!-- CONTENEDOR FLUIDO (Sin mx-auto ni max-w-7xl) -->
    <div class="dashboard-shell w-full space-y-12 p-6 lg:p-10">

        <!-- BANNER PRINCIPAL -->
        <div class="rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <p class="text-sm font-semibold text-amber-400">{{ $team->name }}</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight">Todo lo que pasa en tu comunidad</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                Noticias, grupos y soporte reunidos en un solo lugar.
            </p>
        </div>

        <!-- TARJETAS DE MÉTRICAS -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Noticias',            'value' => $noticias_totales,     'route' => 'noticias.index', 'theme' => 'news'],
                ['label' => 'Comentarios',         'value' => $comentarios_totales,  'route' => 'comentarios.index', 'theme' => 'comments'],
                ['label' => 'Comunidades',         'value' => $comunidades_activas->count(), 'route' => 'comunidad.index', 'theme' => 'communities'],
                ['label' => 'Casos abiertos',      'value' => $problemas_abiertos,   'route' => 'problemas.index', 'theme' => 'issues'],
            ] as $card)
                <a href="{{ route($card['route'], $team->slug) }}" wire:navigate
                   class="metric-card metric-card--{{ $card['theme'] }} rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $card['value'] }}</p>
                </a>
            @endforeach
        </div>

        <!-- NOTICIAS Y COMUNIDADES -->
        <div class="grid gap-8 lg:grid-cols-2">
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Noticias recientes</h2>
                    <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Ver todas</a>
                </div>

                @forelse ($noticias_recientes as $noticia)
                    <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                       class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $noticia->titulo }}</p>
                        <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $noticia->contenido }}</p>
                        <p class="mt-3 text-xs text-zinc-400">
                            {{ $noticia->autor?->name ?? 'Autor desconocido' }} · {{ $noticia->created_at?->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay noticias.
                    </p>
                @endforelse
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Comunidades activas</h2>
                    <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Explorar</a>
                </div>

                @forelse ($comunidades_activas as $comunidad)
                    <a href="{{ route('comunidad.show', [$team->slug, $comunidad]) }}" wire:navigate
                       class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $comunidad->nombre }}</p>
                        <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $comunidad->descripcion ?: 'Una nueva comunidad gamer.' }}
                        </p>
                        <p class="mt-3 text-xs text-zinc-400">
                            {{ $comunidad->miembros_count }}
                            {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay comunidades.
                    </p>
                @endforelse
            </section>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN DE RANKING (INCLUIDA)                -->
        <!-- ============================================ -->
        <section class="pt-4">
            @include('partials.ranking-preview')
        </section>

        <!-- ============================================ -->
        <!-- LOBBY CHAT                                   -->
        <!-- ============================================ -->
        <div class="chat-wrapper">
            <!-- Barra lateral de Amigos / Jugadores -->
            <div class="chat-sidebar">
                <div class="sidebar-header">Jugadores activos</div>
                <ul class="friends-list">
                    <li class="friend-item active">
                        <div class="avatar">V</div>
                        <div class="friend-info">
                            <div class="name">ViperGamer</div>
                            <div class="status">● En el lobby</div>
                        </div>
                    </li>
                    <li class="friend-item">
                        <div class="avatar avatar-playing">S</div>
                        <div class="friend-info">
                            <div class="name">ShadowNinja</div>
                            <div class="status chat-status-playing">● En partida</div>
                        </div>
                    </li>
                    <li class="friend-item">
                        <div class="avatar avatar-offline">K</div>
                        <div class="friend-info">
                            <div class="name">Kraken99</div>
                            <div class="status chat-status-offline">○ Desconectado</div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Área Principal de Conversación -->
            <div class="chat-main">
                <div class="chat-header">
                    <span class="chat-title" style="font-weight: bold;">Chat con: ViperGamer</span>
                </div>

                <div class="messages-container" id="messagesBox">
                    <div class="message-bubble message-incoming">
                        ¡Hey! ¿Listo para la partida de torneo hoy?
                        <span class="message-time">12:30 PM</span>
                    </div>
                    <div class="message-bubble message-outgoing">
                        ¡Claro que sí! Ya tengo el equipo listo en el Discord.
                        <span class="message-time">12:32 PM</span>
                    </div>
                    <div class="message-bubble message-incoming">
                        Perfecto, entra a la sala 4 cuando puedas.
                        <span class="message-time">12:33 PM</span>
                    </div>
                </div>

                <form class="chat-input-area" onsubmit="event.preventDefault(); enviarMensajeDemo();">
                    <input
                        type="text"
                        id="inputMensaje"
                        class="chat-input"
                        placeholder="Escribe un mensaje al jugador..."
                        autocomplete="off"
                    >
                    <button type="submit" class="btn-send">Enviar</button>
                </form>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN DE CONTACTO (INCLUIDA)               -->
        <!-- ============================================ -->
        <section class="pt-4">
            @include('partials.contacto-preview')
        </section>

    </div>

    <script>
        function enviarMensajeDemo() {
            const input = document.getElementById('inputMensaje');
            const box = document.getElementById('messagesBox');

            if (input.value.trim() !== '') {
                const bubble = document.createElement('div');
                bubble.className = 'message-bubble message-outgoing';
                bubble.textContent = input.value;
                const time = document.createElement('span');
                time.className = 'message-time';
                time.textContent = 'Ahora';
                bubble.appendChild(time);

                box.appendChild(bubble);
                input.value = '';
                box.scrollTop = box.scrollHeight;
            }
        }
    </script>
</x-layouts::app>