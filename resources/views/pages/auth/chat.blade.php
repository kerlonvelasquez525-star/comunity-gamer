<x-layouts::app :title="__('Chat con :user', ['user' => $recipient->name])">
    <div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6 lg:p-10">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-400">{{ __('Conversación privada') }}</p>
                <h1 class="mt-2 text-2xl font-bold text-zinc-100 sm:text-3xl">{{ $recipient->name }}</h1>
                <p class="mt-1 text-sm text-zinc-400">{{ __('Mensajes privados cifrados') }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Volver') }}</a>
        </header>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="flex min-h-[32rem] flex-col rounded-2xl border border-zinc-800 bg-zinc-950/80 shadow-2xl shadow-cyan-950/20" aria-label="{{ __('Conversation messages') }}">
                <div class="flex items-center justify-between border-b border-zinc-800 px-4 py-3 sm:px-5">
                    <div><p class="text-sm font-semibold text-zinc-100">{{ __('Mensajes directos') }}</p><p class="text-xs text-zinc-500">{{ __('Solo tú y :user pueden ver esta conversación.', ['user' => $recipient->name]) }}</p></div>
                    <span class="h-2 w-2 rounded-full bg-emerald-400" aria-label="{{ __('Protegida') }}"></span>
                </div>
                <div class="flex flex-1 flex-col gap-3 overflow-y-auto p-4 sm:p-5" id="messagesBox">
                    @forelse ($messages as $message)
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-relaxed sm:max-w-[70%] {{ $message->id_emisor === auth()->id() ? 'self-end rounded-br-sm bg-cyan-400 text-zinc-950' : 'self-start rounded-bl-sm bg-zinc-800 text-zinc-100' }}">
                            <p class="whitespace-pre-wrap break-words">{{ $message->contenido }}</p>
                            <time class="mt-1 block text-right text-[11px] opacity-60">{{ $message->fecha_envio?->format('H:i') }}</time>
                        </div>
                    @empty
                        <div class="m-auto max-w-sm text-center"><p class="text-sm font-semibold text-zinc-300">Todavía no hay mensajes.</p><p class="mt-2 text-xs text-zinc-500">Inicia una conversación respetuosa. Nunca compartas contraseñas ni códigos de seguridad.</p></div>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('chat.store', $recipient) }}" class="border-t border-zinc-800 p-3 sm:p-4">
                    @csrf
                    <div class="flex items-end gap-2"><label for="contenido" class="sr-only">Mensaje</label><textarea id="contenido" name="contenido" rows="1" maxlength="5000" required autofocus placeholder="Escribe un mensaje..." class="max-h-32 min-h-11 min-w-0 flex-1 resize-y rounded-xl border border-zinc-700 bg-zinc-900 px-3 py-3 text-sm text-zinc-100 placeholder:text-zinc-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30">{{ old('contenido') }}</textarea><button type="submit" class="rounded-xl bg-cyan-400 px-4 py-3 text-sm font-bold text-zinc-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-zinc-950">Enviar</button></div>
                </form>
                @error('contenido')<p role="alert" class="border-t border-red-900/50 px-4 py-2 text-sm text-red-400">{{ $message }}</p>@enderror
            </section>

            <aside class="rounded-2xl border border-zinc-800 bg-zinc-900/80 p-4 sm:p-5" aria-labelledby="assistant-title">
                <div class="flex items-start gap-3"><div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl border border-cyan-400/70 bg-zinc-950 p-1 shadow-[0_0_14px_rgba(34,211,238,0.2)]"><img src="{{ asset('nexus.png') }}" alt="Logotipo del asistente de la comunidad" class="h-full w-full rounded-lg object-cover"></div><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-400">Asistente de la comunidad</p><h2 id="assistant-title" class="mt-1 text-lg font-bold text-zinc-100">Respuestas rápidas</h2></div></div>
                <p class="mt-4 text-sm leading-relaxed text-zinc-400">Pregunta sobre privacidad, seguridad, equipos, noticias, chat o reportes. Las respuestas siguen las reglas de la plataforma y no se guardan.</p>
                <div class="mt-5 space-y-3" id="assistantMessages" aria-live="polite"></div>
                <form id="assistantForm" class="mt-5 space-y-2"><label for="assistantQuestion" class="text-xs font-semibold text-zinc-300">Tu pregunta</label><div class="flex gap-2"><input id="assistantQuestion" name="question" maxlength="500" required class="min-w-0 flex-1 rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:border-cyan-400 focus:outline-none" placeholder="Escribe tu pregunta..."><button id="assistantSubmit" type="submit" class="rounded-lg bg-zinc-100 px-3 py-2 text-sm font-bold text-zinc-950 transition hover:bg-white disabled:cursor-wait disabled:opacity-60">Preguntar</button></div><p id="assistantError" class="hidden text-xs text-red-400" role="alert"></p></form>
                <p class="mt-5 text-[11px] leading-relaxed text-zinc-500">Aviso de privacidad: este asistente usa respuestas locales predefinidas. No introduzcas contraseñas, códigos de recuperación ni información personal.</p>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const form = document.getElementById('assistantForm');
            const input = document.getElementById('assistantQuestion');
            const messages = document.getElementById('assistantMessages');
            const submit = document.getElementById('assistantSubmit');
            const error = document.getElementById('assistantError');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const ask = async (question) => {
                if (!question.trim() || submit.disabled) return;
                error.classList.add('hidden');
                submit.disabled = true;
                submit.textContent = '...';
                try {
                    const response = await fetch('{{ route('chat.bot.answer') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ question }) });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'No puedo responder en este momento.');
                    const entry = document.createElement('div');
                    entry.className = 'rounded-xl border border-zinc-800 bg-zinc-950 p-3 text-sm';
                    entry.innerHTML = '<p class="font-semibold text-cyan-300"></p><p class="mt-1 leading-relaxed text-zinc-300"></p><div class="mt-2 flex flex-wrap gap-2"></div>';
                    entry.children[0].textContent = question;
                    entry.children[1].textContent = data.answer;
                    data.suggestions.forEach((suggestion) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'rounded-full border border-zinc-700 px-2.5 py-1 text-[11px] text-zinc-400 transition hover:border-cyan-400 hover:text-cyan-300';
                        button.textContent = suggestion;
                        button.addEventListener('click', () => ask(suggestion));
                        entry.children[2].appendChild(button);
                    });
                    messages.appendChild(entry);
                    input.value = '';
                } catch (exception) {
                    error.textContent = exception.message || 'No puedo responder en este momento.';
                    error.classList.remove('hidden');
                } finally {
                    submit.disabled = false;
                    submit.textContent = 'Preguntar';
                }
            };
            form.addEventListener('submit', (event) => { event.preventDefault(); ask(input.value); });
            input.addEventListener('keydown', (event) => { if (event.key === 'Enter') { event.preventDefault(); form.requestSubmit(); } });
            document.querySelectorAll('[data-question]').forEach((button) => button.addEventListener('click', () => { input.value = button.dataset.question; ask(input.value); }));
        })();
    </script>
</x-layouts::app>
