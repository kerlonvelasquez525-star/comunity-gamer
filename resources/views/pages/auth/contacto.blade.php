<x-layouts::app :title="__('Contacto // Nexus Gamer')">
    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <!-- ENCABEZADO PRINCIPAL -->
        <div class="rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-zinc-400 hover:text-amber-400 transition mb-4">
                &larr; Volver al inicio
            </a>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">Soporte & Comunidad</p>
            <h1 class="mt-2 text-4xl font-bold tracking-tight">// CANALES DE CONTACTO</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                ¿Tienes dudas, sugerencias o quieres organizar un torneo? Conéctate con nosotros en nuestras plataformas oficiales.
            </p>
        </div>

        <!-- TARJETAS DE REDES SOCIALES -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Discord -->
            <a href="https://discord.gg" target="_blank" rel="noopener noreferrer"
               class="group flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <div class="text-3xl">🎮</div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500 transition">Discord Oficial</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        El lobby principal para hablar con moderadores y encontrar equipo.
                    </p>
                </div>
                <div class="mt-4">
                    <span class="inline-block rounded-md bg-amber-400/10 px-2.5 py-1 font-mono text-xs font-medium text-amber-600 dark:text-amber-400">
                        discord.gg/nexusgamer
                    </span>
                </div>
            </a>

            <!-- Twitch -->
            <a href="https://twitch.tv" target="_blank" rel="noopener noreferrer"
               class="group flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <div class="text-3xl">🟣</div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500 transition">Canal de Twitch</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Transmisiones en vivo de los torneos y partidas de la comunidad.
                    </p>
                </div>
                <div class="mt-4">
                    <span class="inline-block rounded-md bg-amber-400/10 px-2.5 py-1 font-mono text-xs font-medium text-amber-600 dark:text-amber-400">
                        twitch.tv/nexus_oficial
                    </span>
                </div>
            </a>

            <!-- Twitter / X -->
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer"
               class="group flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <div class="text-3xl">🐦</div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500 transition">Twitter / X</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Noticias de última hora, anuncios de parches y eventos especiales.
                    </p>
                </div>
                <div class="mt-4">
                    <span class="inline-block rounded-md bg-amber-400/10 px-2.5 py-1 font-mono text-xs font-medium text-amber-600 dark:text-amber-400">
                        @NexusGamerGG
                    </span>
                </div>
            </a>

            <!-- TikTok & Instagram -->
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
               class="group flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <div class="text-3xl">📱</div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500 transition">TikTok & Instagram</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Los mejores clips de las partidas, jugadas destacadas y memes.
                    </p>
                </div>
                <div class="mt-4">
                    <span class="inline-block rounded-md bg-amber-400/10 px-2.5 py-1 font-mono text-xs font-medium text-amber-600 dark:text-amber-400">
                        @nexusgamer.zone
                    </span>
                </div>
            </a>
        </div>

        <!-- BANNER DE SOPORTE DIRECTO -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">✉️ ¿Asuntos comerciales o soporte técnico?</h4>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Para alianzas de patrocinio, incidencias con cuentas o reportes de bugs, escríbenos directamente a:
            </p>
            <div class="mt-4">
                <span class="inline-block rounded-lg bg-zinc-100 px-4 py-2 font-mono text-sm font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-amber-400">
                    soporte@nexusgamer.gg
                </span>
            </div>
        </div>

    </div>
</x-layouts::app>