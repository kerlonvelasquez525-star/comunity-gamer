<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-900 text-white">
    {{ $slot }}

    @fluxScripts
</body>
</html>