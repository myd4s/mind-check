<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Konfirmasi Kata Sandi</h2>
    <p class="text-sm text-gray-500 mb-6">
        Ini adalah area aman aplikasi. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
    </p>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button>
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</div>
