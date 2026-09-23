<x-layouts::app :title="__('Soporte')">
    <div class="problems-page w-full space-y-8 p-6 lg:p-10">

        <!-- ENCABEZADO SUPERIOR -->
        <header class="problems-hero rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">
                {{ $team->name }} // Canal de convivencia
            </p>
            <h1 class="mt-2 text-4xl font-bold tracking-tight">Reporta una incidencia</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                Ayúdanos a mantener la comunidad limpia, segura y útil para todos los jugadores.
            </p>
        </header>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm font-semibold text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <!-- LAYOUT DE FORMULARIO Y PROTOCOLO -->
        <div class="grid gap-8 lg:grid-cols-3 items-start">
            
            <!-- FORMULARIO PRINCIPAL (OCUPA 2 COLUMNAS) -->
            <form method="POST" action="{{ route('problemas.store', $team->slug) }}" class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
                @csrf

                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">// Enviar reporte</h2>

                <!-- TÍTULO -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300" for="titulo">
                        Título
                    </label>
                    <input id="titulo" name="titulo" type="text" value="{{ old('titulo') }}"
                           required maxlength="160"
                           placeholder="Resumen del problema..."
                           class="w-full rounded-xl border border-zinc-300 bg-zinc-50 px-4 py-2.5 text-sm text-zinc-900 outline-none focus:border-amber-400 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                           @error('titulo') aria-invalid="true" aria-describedby="titulo-error" @enderror>
                    @error('titulo')
                        <p id="titulo-error" class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300" for="descripcion">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" required maxlength="5000" rows="6"
                              placeholder="Detalla lo sucedido con la mayor cantidad de información posible..."
                              class="w-full rounded-xl border border-zinc-300 bg-zinc-50 p-4 text-sm text-zinc-900 outline-none focus:border-amber-400 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                              @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p id="descripcion-error" class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PRIORIDAD Y PLATAFORMA -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300" for="prioridad">
                            Prioridad
                        </label>
                        <select id="prioridad" name="prioridad" required
                                class="w-full rounded-xl border border-zinc-300 bg-zinc-50 px-4 py-2.5 text-sm text-zinc-900 outline-none focus:border-amber-400 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @foreach (['baja', 'media', 'alta', 'critica'] as $priority)
                                <option value="{{ $priority }}" @selected(old('prioridad', 'media') === $priority)>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                        @error('prioridad')
                            <p class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300" for="plataforma">
                            Plataforma
                        </label>
                        <input id="plataforma" name="plataforma" type="text" value="{{ old('plataforma') }}"
                               maxlength="80" placeholder="PC, PS5, Xbox…"
                               class="w-full rounded-xl border border-zinc-300 bg-zinc-50 px-4 py-2.5 text-sm text-zinc-900 outline-none focus:border-amber-400 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        @error('plataforma')
                            <p class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- BOTÓN DE ENVÍO -->
                <button type="submit" 
                        class="w-full rounded-xl bg-amber-400 px-6 py-3 font-bold text-zinc-950 transition hover:bg-amber-300">
                    Enviar reporte
                </button>
            </form>

            <!-- LATERAL PROTOCOLO (OCUPA 1 COLUMNA) -->
            <aside class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">// Protocolo de respuesta</h2>
                
                <ol class="space-y-4 text-sm text-zinc-600 dark:text-zinc-400">
                    <li class="flex items-start gap-3">
                        <span class="rounded-md bg-amber-400/10 px-2 py-0.5 font-mono text-xs font-bold text-amber-500">01</span>
                        <span>Revisamos la información recibida.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="rounded-md bg-amber-400/10 px-2 py-0.5 font-mono text-xs font-bold text-amber-500">02</span>
                        <span>Verificamos el contenido reportado.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="rounded-md bg-amber-400/10 px-2 py-0.5 font-mono text-xs font-bold text-amber-500">03</span>
                        <span>Aplicamos las medidas necesarias.</span>
                    </li>
                </ol>

                <div class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <a href="{{ route('problemas.index', $team->slug) }}" wire:navigate
                       class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-amber-500 hover:underline">
                        Ver reportes del equipo &rarr;
                    </a>
                </div>
            </aside>

        </div>
    </div>
</x-layouts::app>