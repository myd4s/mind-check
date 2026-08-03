<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Verifikasi Email</h2>
    <p class="text-sm text-gray-500 mb-6">
        Terima kasih telah mendaftar! Sebelum melanjutkan, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima emailnya, kami akan dengan senang hati mengirimkan yang baru.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 text-sm font-medium text-emerald-600">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            Kirim Ulang Email Verifikasi
        </x-primary-button>

        <button wire:click="logout" type="submit" class="text-sm text-gray-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Log Out') }}
        </button>
    </div>
</div>
