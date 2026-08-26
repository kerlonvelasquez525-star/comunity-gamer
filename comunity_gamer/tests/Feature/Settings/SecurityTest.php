<?php

namespace App\Livewire\Pages\Settings;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Security extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function getTwoFactorEnabledProperty(): bool
    {
        return !is_null(auth()->user()->two_factor_confirmed_at) || !is_null(auth()->user()->two_factor_secret);
    }

    public function mount(): void
    {
        $user = auth()->user();

        // Limpieza automática si se abandonó el proceso de confirmación de 2FA previamente
        if ($user->two_factor_secret && is_null($user->two_factor_confirmed_at)) {
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
            ])->save();
        }
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('The provided password does not match your current password.')],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.pages.settings.security');
    }
}