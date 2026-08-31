@php
    $teamSlug = auth()->user()?->currentTeam?->slug;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                @auth
                    <a href="{{ $teamSlug ? route('dashboard', $teamSlug) : route('home') }}"
                       wire:navigate
                       class="flex items-center gap-2.5 px-2 py-1">
                        <img src="{{ asset('nexus.png') }}"
                             alt="Nexus Logo"
                             class="h-8 w-8 object-contain"
                             style="width: 32px; height: 32px; min-width: 32px; max-height: 32px;">
                        <span class="font-bold text-base tracking-wider text-zinc-100">NEXUS</span>
                    </a>

                    <livewire:team-switcher />
                @endauth
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    @if ($teamSlug)
                        <flux:sidebar.item
                            icon="home"
                            :href="route('dashboard', $teamSlug)"
                            :current="request()->routeIs('dashboard')"
                            wire:navigate
                        >
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="newspaper"
                            :href="route('noticias.index', $teamSlug)"
                            :current="request()->routeIs('noticias.*')"
                            wire:navigate
                        >
                            {{ __('Noticias') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="chat-bubble-left-right"
                            :href="route('comentarios.index', $teamSlug)"
                            :current="request()->routeIs('comentarios.*')"
                            wire:navigate
                        >
                            {{ __('Comentarios') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="user-group"
                            :href="route('comunidad.index', $teamSlug)"
                            :current="request()->routeIs('comunidad.*')"
                            wire:navigate
                        >
                            {{ __('Comunidades') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="exclamation-triangle"
                            :href="route('problemas.index', $teamSlug)"
                            :current="request()->routeIs('problemas.*')"
                            wire:navigate
                        >
                            {{ __('Problemas') }}
                        </flux:sidebar.item>
                    @else
                        <flux:sidebar.item icon="home" :href="route('home')" wire:navigate>
                            {{ __('Inicio') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            @auth
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            @endauth

            @guest
                <div class="hidden p-4 lg:block">
                    <flux:button href="{{ route('login') }}" variant="primary" class="w-full">
                        {{ __('Log in') }}
                    </flux:button>
                </div>
            @endguest
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <flux:avatar
                                        :name="auth()->user()->name"
                                        :initials="auth()->user()->initials()"
                                    />

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                        <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                {{ __('Settings') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @endauth

            @guest
                <flux:button href="{{ route('login') }}" variant="subtle" size="sm">
                    {{ __('Log in') }}
                </flux:button>
            @endguest
        </flux:header>

        {{ $slot }}

        @auth
            <livewire:create-team-modal />
        @endauth

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>