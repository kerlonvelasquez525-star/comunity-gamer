<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Enlace al archivo CSS principal (donde pegaste la configuración) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Scripts de Flux/Livewire -->
    @fluxAppearance
</head>
<body class="project-background min-h-screen bg-zinc-950 text-white antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center p-6">
        {{ $slot }}
    </main>
    
    @fluxScripts
</body>
</html>