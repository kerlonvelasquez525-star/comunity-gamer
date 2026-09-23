<x-layouts::app :title="$title">
    <div class="mx-auto max-w-3xl space-y-6 p-6 lg:p-10">
        <a class="text-sm font-semibold text-amber-600" href="{{ route($resource.'.index', $team->slug) }}">Volver al listado</a>

        <article class="rounded-2xl border border-zinc-200 bg-white p-7 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">{{ $team->name }}</p>
            <h1 class="mt-3 text-3xl font-bold">{{ $title }}</h1>

            @if ($item->asunto)
                <p class="mt-4 font-semibold">{{ $item->asunto }}</p>
            @endif

            <div class="mt-6 whitespace-pre-line text-zinc-600 dark:text-zinc-300">
                {{ $item->contenido ?? $item->descripcion ?? '' }}
            </div>

            @if ($resource === 'juegos')
                <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-semibold">Desarrollador</dt>
                        <dd>{{ $item->desarrollador ?: 'No indicado' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Lanzamiento</dt>
                        <dd>{{ $item->fecha_lanzamiento?->format('Y-m-d') ?: 'No indicada' }}</dd>
                    </div>
                </dl>
            @endif

            @if (isset($item->estado))
                <div class="mt-6 flex gap-2 text-sm">
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">{{ $item->estado }}</span>
                    @if(isset($item->prioridad))
                        <span class="rounded-full bg-zinc-100 px-3 py-1 text-zinc-700">{{ $item->prioridad }}</span>
                    @endif
                </div>
            @endif

            @if ($resource === 'juegos' && $item->plataformas->isNotEmpty())
                <p class="mt-5 text-sm">Plataformas: {{ $item->plataformas->pluck('nombre')->join(', ') }}</p>
            @endif

            @if ($resource === 'juegos' && $item->idiomas->isNotEmpty())
                <p class="mt-2 text-sm">Idiomas: {{ $item->idiomas->pluck('nombre')->join(', ') }}</p>
            @endif
        </article>

        @if ($resource === 'noticias')
            <section class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Comentarios</h2>

                <div class="mt-4 space-y-3">
                    @forelse ($item->comentarios ?? collect() as $comentario)
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                    {{ $comentario->autor?->name ?? 'Usuario' }}
                                </p>
                                @if ($comentario->user_id === auth()->id())
                                    <form method="POST" action="{{ route('noticias.comentarios.destroy', [$team->slug, $item, $comentario]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-500">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $comentario->contenido }}</p>
                            @if ($comentario->user_id === auth()->id())
                                <form method="POST" action="{{ route('noticias.comentarios.update', [$team->slug, $item, $comentario]) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="contenido" value="{{ $comentario->contenido }}" maxlength="2000" required class="min-w-0 flex-1 rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-900">
                                    <button type="submit" class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-400">Guardar</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aún no hay comentarios en esta noticia.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('noticias.comentarios.store', [$team->slug, $item]) }}" class="mt-6 space-y-3">
                    @csrf
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                        Escribe tu comentario
                        <textarea name="contenido" rows="3" maxlength="2000" required class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"></textarea>
                    </label>
                    <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-zinc-950 hover:bg-amber-400">
                        Comentar
                    </button>
                </form>
            </section>
        @endif

        @if ($resource === 'comunidad')
            <section class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Miembros y roles</h2>
                    <span class="rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-300">
                        {{ $item->miembros()->count() }} miembros
                    </span>
                </div>

                @if (auth()->user() && auth()->user()->can('update', [$item, $team]))
                    <form method="POST" action="{{ route('comunidad.miembros.invitar', [$team->slug, $item]) }}" class="mt-4 flex flex-col gap-3 rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:flex-row sm:items-end">
                        @csrf
                        <label class="flex-1 text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Invitar miembro del equipo
                            <select name="user_id" class="mt-2 block w-full rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-900" required>
                                <option value="">Selecciona un miembro</option>
                                @foreach ($team->members()->whereKeyNot($item->miembros()->pluck('users.id'))->get() as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-zinc-950">Invitar</button>
                    </form>
                @endif

                <div class="mt-4 space-y-3">
                    @foreach (($item->miembros()->withPivot('rol')->get() ?? collect()) as $miembro)
                        @php
                            $rol = $miembro->pivot->rol ?? 'miembro';
                            $badgeClass = match ($rol) {
                                'admin' => 'border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-700 dark:bg-rose-950/30 dark:text-rose-300',
                                'moderador' => 'border-cyan-300 bg-cyan-50 text-cyan-700 dark:border-cyan-700 dark:bg-cyan-950/30 dark:text-cyan-300',
                                default => 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300',
                            };
                        @endphp

                        <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-900 text-sm font-bold text-white dark:bg-white dark:text-zinc-900">
                                    {{ strtoupper(substr($miembro->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $miembro->name }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.18em] {{ $badgeClass }}">
                                        {{ $rol }}
                                    </span>
                                </div>
                            </div>

                            @if (auth()->user() && auth()->user()->can('update', [$item, $team]))
                                <form method="POST" action="{{ route('comunidad.miembros.rol', [$team->slug, $item, $miembro]) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="rol" class="rounded-lg border-zinc-300 bg-white px-2 py-1 text-sm dark:border-zinc-600 dark:bg-zinc-900">
                                        <option value="admin" @selected($rol === 'admin')>Admin</option>
                                        <option value="moderador" @selected($rol === 'moderador')>Moderador</option>
                                        <option value="miembro" @selected($rol === 'miembro')>Miembro</option>
                                    </select>
                                    <button type="submit" class="rounded-lg bg-amber-500 px-3 py-1.5 text-sm font-semibold text-zinc-950">Guardar</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if (auth()->user() && auth()->user()->can('update', [$item, $team]))
                    <div class="mt-8 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Solicitudes pendientes</h2>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
                                {{ $item->solicitudes->count() }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($item->solicitudes as $solicitud)
                                <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $solicitud->usuario?->name ?? 'Usuario' }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $solicitud->usuario?->email }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('comunidad.solicitudes.accept', [$team->slug, $item, $solicitud]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-semibold text-zinc-950">Aceptar</button>
                                        </form>
                                        <form method="POST" action="{{ route('comunidad.solicitudes.reject', [$team->slug, $item, $solicitud]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-red-300 px-3 py-2 text-xs font-semibold text-red-700 dark:border-red-700 dark:text-red-300">Rechazar</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No hay solicitudes pendientes.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </section>
        @endif

        @if (! ($resource === 'noticias' && $item->es_oficial))
            <div class="flex gap-3">
                <a href="{{ route($resource.'.edit', [$team->slug, $item]) }}" class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-zinc-950">Editar</a>
                <form method="POST" action="{{ route($resource.'.destroy', [$team->slug, $item]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-lg border border-red-300 px-4 py-2 font-semibold text-red-700">Eliminar</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts::app>
