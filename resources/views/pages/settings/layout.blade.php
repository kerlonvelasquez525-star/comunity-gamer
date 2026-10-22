<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist class="settings-nav-scroll max-md:flex-row max-md:gap-1 max-md:pb-2" aria-label="{{ __('Settings') }}">
            <flux:navlist.item
                :href="route('profile.edit')"
                :current="request()->routeIs('profile.edit')"
                :aria-current="request()->routeIs('profile.edit') ? 'page' : null"
                class="max-md:w-auto max-md:shrink-0"
                wire:navigate
            >{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item
                :href="route('security.edit')"
                :current="request()->routeIs('security.edit')"
                :aria-current="request()->routeIs('security.edit') ? 'page' : null"
                class="max-md:w-auto max-md:shrink-0"
                wire:navigate
            >{{ __('Security') }}</flux:navlist.item>
            <flux:navlist.item
                :href="route('teams.index')"
                :current="request()->routeIs('teams.*')"
                :aria-current="request()->routeIs('teams.*') ? 'page' : null"
                class="max-md:w-auto max-md:shrink-0"
                wire:navigate
            >{{ __('Teams') }}</flux:navlist.item>
            @if (auth()->user()?->currentTeam)
                <flux:navlist.item
                    :href="route('comunidad.index', auth()->user()->currentTeam->slug)"
                    :current="request()->routeIs('comunidad.*')"
                    :aria-current="request()->routeIs('comunidad.*') ? 'page' : null"
                    class="max-md:w-auto max-md:shrink-0"
                    wire:navigate
                >{{ __('Communities') }}</flux:navlist.item>
                <flux:navlist.item
                    :href="route('communities.settings')"
                    :current="request()->routeIs('communities.settings')"
                    :aria-current="request()->routeIs('communities.settings') ? 'page' : null"
                    class="max-md:w-auto max-md:shrink-0"
                    wire:navigate
                >{{ __('Create community') }}</flux:navlist.item>
            @endif
            <flux:navlist.item
                :href="route('appearance.edit')"
                :current="request()->routeIs('appearance.edit')"
                :aria-current="request()->routeIs('appearance.edit') ? 'page' : null"
                class="max-md:w-auto max-md:shrink-0"
                wire:navigate
            >{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
