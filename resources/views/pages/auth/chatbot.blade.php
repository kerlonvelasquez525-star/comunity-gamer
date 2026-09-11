<x-layouts::app :title="__('Community assistant')">
    <div class="mx-auto w-full max-w-4xl space-y-6 p-4 sm:p-6 lg:p-10">
        <header class="flex items-start gap-4">
            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-2xl border border-cyan-400/70 bg-zinc-900 p-1 shadow-[0_0_18px_rgba(34,211,238,0.25)]"><img src="{{ asset('nexus.png') }}" alt="{{ __('Community assistant logo') }}" class="h-full w-full rounded-xl object-cover"></div>
            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-400">{{ __('Community assistant') }}</p><h1 class="mt-2 text-2xl font-bold text-zinc-100 sm:text-3xl">{{ __('How can we help?') }}</h1><p class="mt-2 max-w-2xl text-sm leading-relaxed text-zinc-400">{{ __('Ask about privacy, security, teams, news, chat or reports. This assistant uses predefined answers and does not store your questions.') }}</p></div>
        </header>

        <section class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4 shadow-2xl shadow-cyan-950/20 sm:p-6" aria-labelledby="assistant-form-title">
            <h2 id="assistant-form-title" class="sr-only">{{ __('Ask the community assistant') }}</h2>
            <div id="assistantStatus" class="mb-5 hidden rounded-xl border border-zinc-700 bg-zinc-900 p-4 text-sm leading-relaxed text-zinc-200" role="status" aria-live="polite"></div>
            <div class="mb-5 flex flex-wrap gap-2"><button type="button" data-question="{{ __('How are my data protected?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Privacy') }}</button><button type="button" data-question="{{ __('How do I report content?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Reports') }}</button><button type="button" data-question="{{ __('How does two factor authentication work?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Security') }}</button><button type="button" data-question="{{ __('How do teams work?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Teams') }}</button></div>
            <form id="assistantForm" class="space-y-3"><label for="assistantQuestion" class="text-sm font-semibold text-zinc-200">{{ __('Your question') }}</label><div class="flex flex-col gap-2 sm:flex-row"><input id="assistantQuestion" name="question" maxlength="500" required class="min-w-0 flex-1 rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30" placeholder="{{ __('Ask something...') }}"><button type="submit" class="rounded-xl bg-cyan-400 px-5 py-3 text-sm font-bold text-zinc-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-zinc-950">{{ __('Ask assistant') }}</button></div></form>
            <p class="mt-5 text-xs leading-relaxed text-zinc-500">{{ __('Privacy note: never enter passwords, recovery codes, payment data or sensitive personal information.') }}</p>
        </section>
    </div>
    <script>
        (() => {
            const form = document.getElementById('assistantForm');
            const input = document.getElementById('assistantQuestion');
            const status = document.getElementById('assistantStatus');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const ask = async (question) => {
                if (!question.trim()) return;
                status.classList.remove('hidden');
                status.textContent = '{{ __('Checking the platform guidance...') }}';
                try {
                    const response = await fetch('{{ route('chat.bot.answer') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ question }) });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || '{{ __('Unable to answer right now.') }}');
                    status.textContent = data.answer;
                    input.value = '';
                } catch (error) {
                    status.textContent = error.message || '{{ __('Unable to answer right now.') }}';
                }
            };
            form.addEventListener('submit', (event) => { event.preventDefault(); ask(input.value); });
            document.querySelectorAll('[data-question]').forEach((button) => button.addEventListener('click', () => { input.value = button.dataset.question; ask(input.value); }));
        })();
    </script>
</x-layouts::app>
