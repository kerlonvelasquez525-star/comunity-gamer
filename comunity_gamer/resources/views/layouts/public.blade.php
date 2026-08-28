{{-- Layout público: sin sidebar ni team-switcher. --}}

@php
    $teamSlug = auth()->user()?->currentTeam?->slug;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    <livewire:styles />
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">

    {{-- Cabecera pública (sin sidebar) --}}
    <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2" wire:navigate>
                <x-app-logo class="h-8 w-8" />
                <span class="text-lg font-bold">{{ config('app.name') }}</span>
            </a>

            <nav class="flex items-center gap-4">
                <a href="{{ $teamSlug ? route('noticias.index', $teamSlug) : route('login') }}" wire:navigate
                   class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                    Noticias
                </a>
                <a href="{{ $teamSlug ? route('comunidad.index', $teamSlug) : route('login') }}" wire:navigate
                   class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                    Comunidades
                </a>

                @auth
                    <a href="{{ $teamSlug ? route('dashboard', $teamSlug) : route('teams.index') }}" wire:navigate
                       class="rounded-lg bg-amber-400 px-3 py-1.5 text-sm font-semibold text-zinc-900 hover:bg-amber-500">
                        Mi centro
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                       class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" wire:navigate
                       class="rounded-lg bg-amber-400 px-3 py-1.5 text-sm font-semibold text-zinc-900 hover:bg-amber-500">
                        Crear cuenta
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Contenido de la página --}}
    <main class="mx-auto w-full max-w-7xl px-4 py-8 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-zinc-200 py-6 text-center text-sm text-zinc-500 dark:border-zinc-800">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
    </footer>

    @fluxScripts
    @livewireScripts
</body>
</html>