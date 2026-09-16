<x-layouts::app :title="__('Asistente de la comunidad')">
    <div class="mx-auto w-full max-w-4xl space-y-6 p-4 sm:p-6 lg:p-10">
        <header class="flex items-start gap-4">
            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-2xl border border-cyan-400/70 bg-zinc-900 p-1 shadow-[0_0_18px_rgba(34,211,238,0.25)]"><img src="{{ asset('nexus.png') }}" alt="{{ __('Logotipo del asistente de la comunidad') }}" class="h-full w-full rounded-xl object-cover"></div>
            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-400">{{ __('Asistente de la comunidad') }}</p><h1 class="mt-2 text-2xl font-bold text-zinc-100 sm:text-3xl">{{ __('¿En qué podemos ayudarte?') }}</h1><p class="mt-2 max-w-2xl text-sm leading-relaxed text-zinc-400">{{ __('Pregunta sobre privacidad, seguridad, equipos, noticias, chat o reportes. Este asistente usa respuestas predefinidas y no guarda tus preguntas.') }}</p></div>
        </header>

        <section class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4 shadow-2xl shadow-cyan-950/20 sm:p-6" aria-labelledby="assistant-form-title">
            <h2 id="assistant-form-title" class="sr-only">{{ __('Pregunta al asistente de la comunidad') }}</h2>
            <div id="assistantMessages" class="mb-5 hidden max-h-[28rem] space-y-3 overflow-y-auto" aria-live="polite"></div>
            <div class="mb-5 flex flex-wrap gap-2"><button type="button" data-question="¿Cómo protegen mis datos?" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">Privacidad</button><button type="button" data-question="¿Cómo reporto contenido?" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">Reportes</button><button type="button" data-question="¿Cómo funciona la autenticación de dos factores?" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">Seguridad</button><button type="button" data-question="¿Cómo funcionan los equipos?" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">Equipos</button></div>
            <form id="assistantForm" class="space-y-3"><label for="assistantQuestion" class="text-sm font-semibold text-zinc-200">Tu pregunta</label><div class="flex flex-col gap-2 sm:flex-row"><input id="assistantQuestion" name="question" maxlength="500" required class="min-w-0 flex-1 rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30" placeholder="Escribe tu pregunta..."><button id="assistantSubmit" type="submit" class="rounded-xl bg-cyan-400 px-5 py-3 text-sm font-bold text-zinc-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-zinc-950">Preguntar</button></div><p id="assistantError" class="hidden text-sm text-red-400" role="alert"></p></form>
            <p class="mt-5 text-xs leading-relaxed text-zinc-500">Aviso de privacidad: nunca introduzcas contraseñas, códigos de recuperación, datos de pago ni información personal sensible.</p>
        </section>
    </div>
    <script>
        (() => {
            const form = document.getElementById('assistantForm');
            const input = document.getElementById('assistantQuestion');
            const messages = document.getElementById('assistantMessages');
            const submit = document.getElementById('assistantSubmit');
            const error = document.getElementById('assistantError');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const addMessage = (question, answer) => {
                messages.classList.remove('hidden');
                const entry = document.createElement('div');
                entry.className = 'space-y-2 rounded-xl border border-zinc-800 bg-zinc-900 p-4 text-sm';
                entry.innerHTML = `<p class="font-semibold text-cyan-300"></p><p class="leading-relaxed text-zinc-200"></p><div class="flex flex-wrap gap-2"></div>`;
                entry.children[0].textContent = question;
                entry.children[1].textContent = answer.answer;
                answer.suggestions.forEach((suggestion) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'rounded-full border border-zinc-700 px-3 py-1.5 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300';
                    button.textContent = suggestion;
                    button.addEventListener('click', () => ask(suggestion));
                    entry.children[2].appendChild(button);
                });
                messages.appendChild(entry);
                messages.scrollTop = messages.scrollHeight;
            };
            const ask = async (question) => {
                if (!question.trim()) return;
                error.classList.add('hidden');
                submit.disabled = true;
                submit.textContent = 'Consultando...';
                try {
                    const response = await fetch('{{ route('chat.bot.answer') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ question }) });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'No puedo responder en este momento.');
                    addMessage(question, data);
                    input.value = '';
                } catch (error) {
                    const message = error.message || 'No puedo responder en este momento.';
                    document.getElementById('assistantError').textContent = message;
                    document.getElementById('assistantError').classList.remove('hidden');
                } finally {
                    submit.disabled = false;
                    submit.textContent = 'Preguntar';
                }
            };
            form.addEventListener('submit', (event) => { event.preventDefault(); ask(input.value); });
            input.addEventListener('keydown', (event) => { if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); form.requestSubmit(); } });
            document.querySelectorAll('[data-question]').forEach((button) => button.addEventListener('click', () => { input.value = button.dataset.question; ask(input.value); }));
        })();
    </script>
</x-layouts::app>
