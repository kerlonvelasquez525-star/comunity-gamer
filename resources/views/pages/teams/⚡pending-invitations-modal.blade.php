<?php

use App\Models\TeamInvitation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public bool $showPendingInvitationsModal = true;

    public function mount(): void
    {
        if (session('team-invitation-accepted')) {
            $this->dispatch('toast-show');
        }
    }

    #[Computed]
    public function pendingInvitations(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        return TeamInvitation::query()
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('team')
            ->get()
            ->map(function (TeamInvitation $invitation): array {
                $owner = $invitation->team?->owner();

                return [
                    'id' => $invitation->id,
                    'code' => $invitation->code,
                    'team_name' => $invitation->team->name ?? __('Equipo sin nombre'),
                    'inviter_name' => $owner?->name ?? __('Desconocido'),
                ];
            });
    }

    public function acceptInvitation(string $code): mixed
    {
        $user = Auth::user();
        $invitation = TeamInvitation::query()
            ->where('code', $code)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->email !== $user->email || $invitation->isExpired()) {
            $this->addError('invitation', __('This invitation is no longer available.'));

            return null;
        }

        $invitation->team->members()->syncWithoutDetaching([
            $user->id => ['role' => $invitation->role],
        ]);

        $user->switchTeam($invitation->team);

        $invitation->forceFill(['accepted_at' => Carbon::now()])->save();

        session()->flash('team-invitation-accepted', true);
        $this->dispatch('invitation-accepted');

        if ($this->pendingInvitations->isEmpty()) {
            $this->showPendingInvitationsModal = false;
        }

        return redirect()->route('dashboard');
    }

    public function declineInvitation(string $code): void
    {
        $user = Auth::user();

        TeamInvitation::query()
            ->where('code', $code)
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->firstOrFail()
            ->delete();

        $this->dispatch('invitation-declined');

        if ($this->pendingInvitations->isEmpty()) {
            $this->showPendingInvitationsModal = false;
        }
    }
};
?>

<div>
    @if ($this->pendingInvitations->isNotEmpty())
        <flux:modal name="pending-invitations" wire:model="showPendingInvitationsModal" focusable class="max-w-lg">
            <div data-test="pending-invitations-modal" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Pending team invitations') }}</flux:heading>
                    <flux:subheading>{{ __('Accept or decline the teams you have been invited to join.') }}</flux:subheading>
                </div>

                <div class="grid gap-4">
                    @foreach ($this->pendingInvitations as $invitation)
                        <div data-test="pending-invitation-row" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="space-y-1">
                                <p class="font-medium">{{ $invitation['team_name'] }}</p>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __(':inviter invited you to join this team.', ['inviter' => $invitation['inviter_name']]) }}
                                </flux:text>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <flux:button variant="filled" wire:click="declineInvitation('{{ $invitation['code'] }}')" wire:loading.attr="disabled" data-test="pending-invitation-decline">
                                    {{ __('Decline') }}
                                </flux:button>
                                <flux:button variant="primary" wire:click="acceptInvitation('{{ $invitation['code'] }}')" wire:loading.attr="disabled" data-test="pending-invitation-accept">
                                    {{ __('Accept') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    @endif
</div>