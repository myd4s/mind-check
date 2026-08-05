<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        <p class="font-medium text-gray-800 mb-1">{{ __('Ganti Password Anda') }}</p>
        <p>{{ __('Untuk keamanan akun, Anda wajib mengganti password default sebelum melanjutkan.') }}</p>
    </div>

    <form wire:submit="updatePassword">
        <div>
            <x-input-label for="current_password" :value="__('Password Saat Ini')" />
            <x-text-input wire:model="current_password" id="current_password" class="block mt-1 w-full" type="password" autocomplete="current-password" autofocus />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <button type="button" wire:click="logout" class="text-sm text-gray-500 hover:text-gray-700 underline">
                {{ __('Keluar') }}
            </button>

            <x-primary-button>
                {{ __('Simpan Password') }}
            </x-primary-button>
        </div>
    </form>
</div>
