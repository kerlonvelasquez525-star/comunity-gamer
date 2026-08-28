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
                                <flux:button
                                    variant="filled"
                                    wire:click="declineInvitation('{{ $invitation['code'] }}')"
                                    wire:loading.attr="disabled"
                                    data-test="pending-invitation-decline"
                                >
                                    {{ __('Decline') }}
                                </flux:button>

                                <flux:button
                                    variant="primary"
                                    wire:click="acceptInvitation('{{ $invitation['code'] }}')"
                                    wire:loading.attr="disabled"
                                    data-test="pending-invitation-accept"
                                >
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