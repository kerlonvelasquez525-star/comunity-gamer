<x-layouts::app :title="__('Ranking Global // Nexus Gamer')">
    @vite(['resources/css/dashboard.css', 'resources/css/chat.css'])
    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <!-- ENCABEZADO PRINCIPAL -->
        <div class="cyber-hero rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-zinc-400 hover:text-amber-400 transition mb-4">
                &larr; Volver al inicio
            </a>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">Competición & Stats</p>
            <h1 class="mt-2 text-4xl font-bold tracking-tight">// TABLA DE CLASIFICACIÓN</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                Los jugadores y clanes más dominantes de la temporada actual de Nexus Gamer.
            </p>
        </div>

        <!-- PODIO DEL TOP 3 -->
        <div class="grid gap-6 md:grid-cols-3 items-end">
            <!-- 2do Lugar (Plata) -->
            <div class="order-2 md:order-1 flex flex-col items-center justify-center rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 text-center">
                <div class="text-4xl mb-2">🥈</div>
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-200 font-bold text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200 text-xl mb-3 border-2 border-zinc-400">
                    K
                </div>
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white">Jessy Parra</h3>
                <span class="text-xs font-medium text-zinc-400 mt-1">[Clan Dragones]</span>
                <div class="mt-4 rounded-full bg-zinc-100 dark:bg-zinc-800 px-4 py-1.5 text-sm font-bold text-zinc-900 dark:text-white">
                    3,420 PTS
                </div>
            </div>

            <!-- 1er Lugar (Oro - Campeón) -->
            <div class="order-1 md:order-2 flex flex-col items-center justify-center rounded-2xl border-2 border-amber-400 bg-zinc-950 p-8 shadow-2xl text-center relative md:-translate-y-4">
                <div class="absolute -top-3.5 rounded-full bg-amber-400 px-3 py-0.5 text-[10px] font-black uppercase text-zinc-950 tracking-wider">
                    Líder Actual
                </div>
                <div class="text-5xl mb-2">👑</div>
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-400 font-black text-zinc-950 text-2xl mb-3 shadow-lg shadow-amber-400/20">
                    V
                </div>
                <h3 class="font-black text-xl text-amber-400">elthomipro</h3>
                <span class="text-xs font-medium text-zinc-400 mt-1">[Nexus Elite]</span>
                <div class="mt-4 rounded-full bg-amber-400/20 border border-amber-400/40 px-5 py-2 text-base font-black text-amber-400">
                    4,850 PTS
                </div>
            </div>

            <!-- 3er Lugar (Bronce) -->
            <div class="order-3 md:order-3 flex flex-col items-center justify-center rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 text-center">
                <div class="text-4xl mb-2">🥉</div>
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-900/20 font-bold text-amber-700 dark:text-amber-500 text-xl mb-3 border-2 border-amber-700/40">
                    S
                </div>
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white">Alejopaa</h3>
                <span class="text-xs font-medium text-zinc-400 mt-1">[CyberSamurai]</span>
                <div class="mt-4 rounded-full bg-zinc-100 dark:bg-zinc-800 px-4 py-1.5 text-sm font-bold text-zinc-900 dark:text-white">
                    2,980 PTS
                </div>
            </div>
        </div>

        <!-- TABLA DEL TOP 4 AL 10 -->
        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">#</th>
                        <th scope="col" class="px-6 py-4">Jugador</th>
                        <th scope="col" class="px-6 py-4">Juego Principal</th>
                        <th scope="col" class="px-6 py-4">Victorias</th>
                        <th scope="col" class="px-6 py-4">Winrate</th>
                        <th scope="col" class="px-6 py-4">Puntos</th>
                        <th scope="col" class="px-6 py-4">Rango</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                        <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">#4</td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-900 dark:text-white text-xs">A</div>
                            <span class="font-semibold text-zinc-900 dark:text-white">ApexLegend</span>
                        </td>
                        <td class="px-6 py-4">Valorant</td>
                        <td class="px-6 py-4">142</td>
                        <td class="px-6 py-4 font-semibold text-emerald-500">72%</td>
                        <td class="px-6 py-4 font-bold text-amber-500">2,650</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-md bg-purple-500/10 px-2.5 py-1 text-xs font-semibold text-purple-500 border border-purple-500/20">
                                Inmortal
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                        <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">#5</td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-900 dark:text-white text-xs">F</div>
                            <span class="font-semibold text-zinc-900 dark:text-white">FrostBite</span>
                        </td>
                        <td class="px-6 py-4">CS2</td>
                        <td class="px-6 py-4">128</td>
                        <td class="px-6 py-4 font-semibold text-emerald-500">68%</td>
                        <td class="px-6 py-4 font-bold text-amber-500">2,410</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-md bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold text-indigo-500 border border-indigo-500/20">
                                Maestro
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                        <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">#6</td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-900 dark:text-white text-xs">N</div>
                            <span class="font-semibold text-zinc-900 dark:text-white">NovaGhost</span>
                        </td>
                        <td class="px-6 py-4">Warzone</td>
                        <td class="px-6 py-4">115</td>
                        <td class="px-6 py-4 font-semibold text-emerald-500">65%</td>
                        <td class="px-6 py-4 font-bold text-amber-500">2,190</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-md bg-cyan-500/10 px-2.5 py-1 text-xs font-semibold text-cyan-500 border border-cyan-500/20">
                                Diamante
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                        <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">#7</td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-900 dark:text-white text-xs">Z</div>
                            <span class="font-semibold text-zinc-900 dark:text-white">ZeroGravity</span>
                        </td>
                        <td class="px-6 py-4">League of Legends</td>
                        <td class="px-6 py-4">98</td>
                        <td class="px-6 py-4 font-semibold text-emerald-500">61%</td>
                        <td class="px-6 py-4 font-bold text-amber-500">1,950</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-md bg-cyan-500/10 px-2.5 py-1 text-xs font-semibold text-cyan-500 border border-cyan-500/20">
                                Diamante
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- POSICIÓN DEL USUARIO ACTUAL -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-amber-400/30 bg-amber-400/10 p-6 dark:bg-amber-400/5">
            <div class="flex flex-col sm:flex-row gap-6 sm:items-center">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Tu Posición Actual</span>
                    <p class="text-lg font-bold text-zinc-900 dark:text-white">#42 en la Clasificación General</p>
                </div>
                <div class="h-8 w-px bg-zinc-300 dark:bg-zinc-700 hidden sm:block"></div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Puntos de Temporada</span>
                    <p class="text-lg font-bold text-zinc-900 dark:text-white">1,240 PTS</p>
                </div>
            </div>
            <div class="text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-400/10 px-3.5 py-2 rounded-lg border border-amber-400/20">
                ⚡ ¡Sube al Top 10 para ganar un mejor puesto en el ranked mundial!
            </div>
        </div>

    </div>
</x-layouts::app>