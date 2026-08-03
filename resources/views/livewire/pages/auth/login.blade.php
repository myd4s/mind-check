<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Masuk ke Akun</h2>
    <p class="text-sm text-gray-500 mb-6">Silakan masuk sesuai peran Anda.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Role Toggle -->
        <div class="mb-5">
            <div class="grid grid-cols-3 gap-2 rounded-xl bg-gray-100 p-1">
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <label class="relative">
                        <input type="radio" name="role" wire:model="form.role" value="{{ $role->value }}" class="peer sr-only">
                        <div class="cursor-pointer text-center text-sm font-medium py-2 rounded-lg text-gray-500 peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition">
                            {{ $role->label() }}
                        </div>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('form.role')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    Lupa kata sandi?
                </a>
            @endif

            <x-primary-button>
                Masuk
            </x-primary-button>
        </div>
    </form>
</div>
