<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public string $mode = 'sidebar';

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
    @if ($mode === 'sidebar')
        <!-- Mobile backdrop -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/50 sm:hidden"
            style="display: none;"
        ></div>

        <aside
            :class="mobileOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
            class="fixed sm:relative inset-y-0 left-0 z-50 w-64 shrink-0 flex flex-col bg-white border-r border-gray-100 transition-transform duration-200 ease-in-out"
        >
            <!-- Logo -->
            <div class="h-16 shrink-0 flex items-center gap-2 px-4 border-b border-gray-100 overflow-hidden">
                <a href="{{ route('dashboard') }}" wire:navigate @click="mobileOpen = false" class="flex items-center gap-2 min-w-0">
                    <x-application-logo class="w-9 h-9 shrink-0" />
                    <span class="font-bold text-gray-900 text-lg truncate">MindCheck</span>
                </a>
            </div>

            <!-- Panel label -->
            <div class="px-4 pt-4 pb-1 overflow-hidden">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                    Panel {{ auth()->user()->role->label() }}
                </p>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1">
                <a
                    href="{{ route('dashboard') }}" wire:navigate @click="mobileOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                    <span class="truncate">Dashboard</span>
                </a>

                @if (auth()->user()->isAdmin())
                    <a
                        href="{{ route('admin.students') }}" wire:navigate @click="mobileOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.students') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <span class="truncate">Manajemen Siswa</span>
                    </a>
                @endif
            </nav>

            <!-- Logout -->
            <div class="p-3 border-t border-gray-100 shrink-0">
                <button
                    wire:click="logout" type="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M9 12h12m0 0l-3.75-3.75M21 12l-3.75 3.75" />
                    </svg>
                    <span class="truncate">Keluar</span>
                </button>
            </div>
        </aside>
    @else
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button type="button" class="inline-flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold shrink-0">
                        {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                    </span>
                    <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    <svg class="hidden sm:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile')" wire:navigate>
                    Profil
                </x-dropdown-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        Keluar
                    </x-dropdown-link>
                </button>
            </x-slot>
        </x-dropdown>
    @endif
</div>
