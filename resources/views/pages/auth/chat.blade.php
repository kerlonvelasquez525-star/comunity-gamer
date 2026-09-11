<x-layouts::app :title="__('Chat with :user', ['user' => $recipient->name])">
    <div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6 lg:p-10">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-400">{{ __('Private conversation') }}</p>
                <h1 class="mt-2 text-2xl font-bold text-zinc-100 sm:text-3xl">{{ $recipient->name }}</h1>
                <p class="mt-1 text-sm text-zinc-400">{{ __('Encrypted private messages') }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Back') }}</a>
        </header>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="flex min-h-[32rem] flex-col rounded-2xl border border-zinc-800 bg-zinc-950/80 shadow-2xl shadow-cyan-950/20" aria-label="{{ __('Conversation messages') }}">
                <div class="flex items-center justify-between border-b border-zinc-800 px-4 py-3 sm:px-5">
                    <div><p class="text-sm font-semibold text-zinc-100">{{ __('Direct messages') }}</p><p class="text-xs text-zinc-500">{{ __('Only you and :user can see this conversation.', ['user' => $recipient->name]) }}</p></div>
                    <span class="h-2 w-2 rounded-full bg-emerald-400" aria-label="{{ __('Protected') }}"></span>
                </div>
                <div class="flex flex-1 flex-col gap-3 overflow-y-auto p-4 sm:p-5" id="messagesBox">
                    @forelse ($messages as $message)
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-relaxed sm:max-w-[70%] {{ $message->id_emisor === auth()->id() ? 'self-end rounded-br-sm bg-cyan-400 text-zinc-950' : 'self-start rounded-bl-sm bg-zinc-800 text-zinc-100' }}">
                            <p class="whitespace-pre-wrap break-words">{{ $message->contenido }}</p>
                            <time class="mt-1 block text-right text-[11px] opacity-60">{{ $message->fecha_envio?->format('H:i') }}</time>
                        </div>
                    @empty
                        <div class="m-auto max-w-sm text-center"><p class="text-sm font-semibold text-zinc-300">{{ __('No messages yet.') }}</p><p class="mt-2 text-xs text-zinc-500">{{ __('Start a respectful conversation. Never share passwords or security codes.') }}</p></div>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('chat.store', $recipient) }}" class="border-t border-zinc-800 p-3 sm:p-4">
                    @csrf
                    <div class="flex items-end gap-2"><label for="contenido" class="sr-only">{{ __('Message') }}</label><textarea id="contenido" name="contenido" rows="1" maxlength="5000" required autofocus placeholder="{{ __('Write a message...') }}" class="max-h-32 min-h-11 min-w-0 flex-1 resize-y rounded-xl border border-zinc-700 bg-zinc-900 px-3 py-3 text-sm text-zinc-100 placeholder:text-zinc-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30">{{ old('contenido') }}</textarea><button type="submit" class="rounded-xl bg-cyan-400 px-4 py-3 text-sm font-bold text-zinc-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-zinc-950">{{ __('Send') }}</button></div>
                </form>
                @error('contenido')<p role="alert" class="border-t border-red-900/50 px-4 py-2 text-sm text-red-400">{{ $message }}</p>@enderror
            </section>

            <aside class="rounded-2xl border border-zinc-800 bg-zinc-900/80 p-4 sm:p-5" aria-labelledby="assistant-title">
                <div class="flex items-start gap-3"><div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl border border-cyan-400/70 bg-zinc-950 p-1 shadow-[0_0_14px_rgba(34,211,238,0.2)]"><img src="{{ asset('nexus.png') }}" alt="{{ __('Community assistant logo') }}" class="h-full w-full rounded-lg object-cover"></div><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-400">{{ __('Community assistant') }}</p><h2 id="assistant-title" class="mt-1 text-lg font-bold text-zinc-100">{{ __('Quick answers') }}</h2></div></div>
                <p class="mt-4 text-sm leading-relaxed text-zinc-400">{{ __('Ask about privacy, security, teams, news, chat or reports. Answers come from the platform rules and are not stored.') }}</p>
                <div class="mt-5 flex flex-wrap gap-2" id="assistantSuggestions"><button type="button" data-question="{{ __('How are my data protected?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Privacy') }}</button><button type="button" data-question="{{ __('How do I report content?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Reports') }}</button><button type="button" data-question="{{ __('How does two factor authentication work?') }}" class="rounded-full border border-zinc-700 px-3 py-2 text-xs text-zinc-300 transition hover:border-cyan-400 hover:text-cyan-300">{{ __('Security') }}</button></div>
                <form id="assistantForm" class="mt-5 space-y-2"><label for="assistantQuestion" class="text-xs font-semibold text-zinc-300">{{ __('Your question') }}</label><div class="flex gap-2"><input id="assistantQuestion" name="question" maxlength="500" required class="min-w-0 flex-1 rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:border-cyan-400 focus:outline-none" placeholder="{{ __('Ask something...') }}"><button type="submit" class="rounded-lg bg-zinc-100 px-3 py-2 text-sm font-bold text-zinc-950 hover:bg-white">{{ __('Ask') }}</button></div></form>
                <div id="assistantStatus" class="mt-4 hidden rounded-xl border border-zinc-700 bg-zinc-950 p-3 text-sm leading-relaxed text-zinc-300" role="status" aria-live="polite"></div>
                <p class="mt-5 text-[11px] leading-relaxed text-zinc-500">{{ __('Privacy note: this assistant uses predefined local answers. Do not include passwords, recovery codes, payment data or personal information.') }}</p>
            </aside>
        </div>
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
