<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <!-- BANNER PRINCIPAL / HERO -->
        <div class="relative overflow-hidden rounded-2xl border border-cyan-500/30 bg-gradient-to-r from-zinc-950 via-zinc-900 to-zinc-950 p-8 text-white shadow-2xl">
            <!-- Glow decorativo de fondo -->
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-fuchsia-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/40 bg-cyan-950/40 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-cyan-300">
                        <span class="h-2 w-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ $team->name }}
                    </span>
                    <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                        Centro de Comando Gamer
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm sm:text-base text-zinc-400">
                        Gestiona tus noticias, participa en comunidades activas y mantén tu escuadrón al día.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-fuchsia-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 transition hover:opacity-90 hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Noticia
                    </a>
                </div>
            </div>
        </div>

        <!-- TARJETAS DE MÉTRICAS (GRID 4 COLUMNAS) -->
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $metricCards = [
                    [
                        'label' => 'Noticias publicadas',
                        'value' => $noticias_totales,
                        'route' => 'noticias.index',
                        'color' => 'text-cyan-400',
                        'border_hover' => 'hover:border-cyan-500/60',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
                    ],
                    [
                        'label' => 'Comentarios',
                        'value' => $comentarios_totales,
                        'route' => 'comentarios.index',
                        'color' => 'text-fuchsia-400',
                        'border_hover' => 'hover:border-fuchsia-500/60',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
                    ],
                    [
                        'label' => 'Comunidades activas',
                        'value' => $comunidades_activas->count(),
                        'route' => 'comunidad.index',
                        'color' => 'text-emerald-400',
                        'border_hover' => 'hover:border-emerald-500/60',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                    ],
                    [
                        'label' => 'Problemas abiertos',
                        'value' => $problemas_abiertos,
                        'route' => 'problemas.index',
                        'color' => 'text-amber-400',
                        'border_hover' => 'hover:border-amber-500/60',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                    ],
                ];
            @endphp

            @foreach ($metricCards as $card)
                <a href="{{ route($card['route'], $team->slug) }}" wire:navigate
                   class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900/80 p-6 backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-950/20 {{ $card['border_hover'] }}">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ $card['label'] }}</p>
                        <div class="rounded-xl bg-zinc-800/80 p-2.5 {{ $card['color'] }} group-hover:scale-110 transition-transform">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                {!! $card['icon'] !!}
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-3xl font-extrabold text-white tracking-tight">{{ $card['value'] }}</p>
                    <div class="mt-3 flex items-center gap-1 text-xs text-zinc-500 group-hover:text-zinc-300 transition-colors">
                        <span>Ver detalles</span>
                        <svg class="h-3.5 w-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- SECCIONES PRINCIPALES (NOTICIAS Y COMUNIDADES) -->
        <div class="grid gap-8 lg:grid-cols-2">

            <!-- NOTICIAS RECIENTES -->
            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        <h2 class="text-lg font-bold uppercase tracking-wider text-white">Noticias Recientes</h2>
                    </div>
                    <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                       class="text-xs font-semibold uppercase tracking-wider text-cyan-400 hover:text-cyan-300 hover:underline">
                        Ver todas &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($noticias_recientes as $noticia)
                        <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                           class="group block rounded-xl border border-zinc-800/90 bg-zinc-900/60 p-5 transition-all duration-200 hover:border-cyan-500/40 hover:bg-zinc-900/90 hover:shadow-lg hover:shadow-cyan-950/20">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="font-bold text-zinc-100 group-hover:text-cyan-300 transition-colors line-clamp-1">
                                    {{ $noticia->titulo }}
                                </h3>
                                <span class="shrink-0 text-xs text-zinc-500">
                                    {{ $noticia->created_at?->diffForHumans() }}
                                </span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm text-zinc-400 leading-relaxed">
                                {{ $noticia->contenido }}
                            </p>
                            <div class="mt-3 flex items-center gap-2 text-xs text-zinc-500">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-800 font-bold text-zinc-300">
                                    {{ strtoupper(substr($noticia->autor?->name ?? 'A', 0, 1)) }}
                                </span>
                                <span>{{ $noticia->autor?->name ?? 'Autor desconocido' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-800 p-10 text-center">
                            <svg class="h-10 w-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <p class="mt-3 text-sm text-zinc-400 font-medium">Todavía no hay noticias publicadas</p>
                            <p class="mt-1 text-xs text-zinc-600">Sé el primero en compartir novedades con tu equipo.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- COMUNIDADES ACTIVAS -->
            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-fuchsia-400"></span>
                        <h2 class="text-lg font-bold uppercase tracking-wider text-white">Comunidades Activas</h2>
                    </div>
                    <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate
                       class="text-xs font-semibold uppercase tracking-wider text-fuchsia-400 hover:text-fuchsia-300 hover:underline">
                        Explorar &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($comunidades_activas as $comunidad)
                        <a href="{{ route('comunidad.show', [$team->slug, $comunidad]) }}" wire:navigate
                           class="group block rounded-xl border border-zinc-800/90 bg-zinc-900/60 p-5 transition-all duration-200 hover:border-fuchsia-500/40 hover:bg-zinc-900/90 hover:shadow-lg hover:shadow-fuchsia-950/20">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="font-bold text-zinc-100 group-hover:text-fuchsia-300 transition-colors">
                                    {{ $comunidad->nombre }}
                                </h3>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-800/80 px-2.5 py-0.5 text-xs font-semibold text-zinc-300 border border-zinc-700/50">
                                    <svg class="h-3 w-3 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    {{ $comunidad->miembros_count }} {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                                </span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm text-zinc-400 leading-relaxed">
                                {{ $comunidad->descripcion ?: 'Una nueva comunidad gamer.' }}
                            </p>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-800 p-10 text-center">
                            <svg class="h-10 w-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-3 text-sm text-zinc-400 font-medium">Todavía no hay comunidades creadas</p>
                            <p class="mt-1 text-xs text-zinc-600">Explora o crea una nueva comunidad para tu escuadrón.</p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-layouts::app>