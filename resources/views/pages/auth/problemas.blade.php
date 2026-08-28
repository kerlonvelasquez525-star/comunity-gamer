<x-layouts::app :title="__('Problemas')">
    <div class="problems-page">

        <header class="problems-header">
            <p class="problems-eyebrow">{{ $team->name }} // canal de convivencia</p>
            <h1 class="problems-title">Reporta una incidencia</h1>
            <p class="problems-intro">
                Ayúdanos a mantener la comunidad limpia, segura y útil para todos los jugadores.
            </p>
        </header>

        @if (session('status'))
            <p role="status" class="problem-error" style="color:#059669;">{{ session('status') }}</p>
        @endif

        <div class="problems-layout">
            <form method="POST" action="{{ route('problemas.store', $team->slug) }}" class="problem-form">
                @csrf

                <h2 class="problem-form-title">Enviar reporte</h2>

                <label class="problem-field" for="titulo">
                    Título
                    <input id="titulo" name="titulo" type="text" value="{{ old('titulo') }}"
                           required maxlength="160" class="problem-input"
                           @error('titulo') aria-invalid="true" aria-describedby="titulo-error" @enderror>
                </label>
                @error('titulo')
                    <p id="titulo-error" class="problem-error">{{ $message }}</p>
                @enderror

                <label class="problem-field" for="descripcion">
                    Descripción
                    <textarea id="descripcion" name="descripcion" required maxlength="5000" rows="7"
                              class="problem-textarea"
                              @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion') }}</textarea>
                </label>
                @error('descripcion')
                    <p id="descripcion-error" class="problem-error">{{ $message }}</p>
                @enderror

                <div class="problem-options">
                    <label class="problem-field" for="prioridad">
                        Prioridad
                        <select id="prioridad" name="prioridad" required class="problem-select">
                            @foreach (['baja', 'media', 'alta', 'critica'] as $priority)
                                <option value="{{ $priority }}" @selected(old('prioridad', 'media') === $priority)>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="problem-field" for="plataforma">
                        Plataforma
                        <input id="plataforma" name="plataforma" type="text" value="{{ old('plataforma') }}"
                               maxlength="80" class="problem-input" placeholder="PC, PS5, Xbox…">
                    </label>
                </div>
                @error('prioridad')
                    <p class="problem-error">{{ $message }}</p>
                @enderror
                @error('plataforma')
                    <p class="problem-error">{{ $message }}</p>
                @enderror

                <button type="submit" class="problem-submit">Enviar reporte</button>
            </form>

            <aside class="problem-protocol">
                <h2 class="problem-protocol-title">Protocolo de respuesta</h2>
                <ol class="problem-steps">
                    <li><b>01</b> Revisamos la información recibida.</li>
                    <li><b>02</b> Verificamos el contenido reportado.</li>
                    <li><b>03</b> Aplicamos las medidas necesarias.</li>
                </ol>

                <p class="problem-steps" style="margin-top:1.5rem;">
                    <a href="{{ route('problemas.index', $team->slug) }}" wire:navigate
                       style="color:#d97706;font-weight:700;">
                        Ver reportes del equipo &rarr;
                    </a>
                </p>
            </aside>
        </div>
    </div>
</x-layouts::app>