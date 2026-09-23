<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>NEXUS COMUNITY</title>

<link rel="icon" href="/nexus.png" sizes="any">
<link rel="icon" href="/nexus.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{-- Hojas de estilo especificas de cada pagina: @push('styles') desde la vista --}}
@stack('styles')