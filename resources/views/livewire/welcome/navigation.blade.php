<nav class="flex flex-1 items-center justify-end gap-3">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            wire:navigate
            class="neu-pressable inline-flex items-center rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-neu-sm hover:bg-primary-700"
        >
            {{ __('Dashboard') }}
        </a>
    @else
        <a
            href="{{ route('login') }}"
            wire:navigate
            class="neu-pressable inline-flex items-center rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-neu-sm hover:bg-primary-700"
        >
            {{ __('Masuk') }}
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                wire:navigate
                class="neu-pressable inline-flex items-center rounded-2xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-primary-600"
            >
                {{ __('Register') }}
            </a>
        @endif
    @endauth
</nav>
