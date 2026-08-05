<?php

use App\Enums\UserRole;
use App\Livewire\Actions\Logout;
use App\Models\AssessmentResult;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    /**
     * Notifikasi bell Guru BK — dihitung dinamis dari assessment_results
     * kategori "Tinggi" terbaru (tanpa tabel notifications terpisah, PRD §7).
     */
    #[Computed]
    public function highCategoryNotifications()
    {
        if (! auth()->user()->hasRoleAtLeast(UserRole::GuruBk)) {
            return collect();
        }

        return AssessmentResult::query()
            ->with(['student.user', 'student.currentClassHistory.schoolClass'])
            ->where('category', 'tinggi')
            ->orderByDesc('completed_at')
            ->limit(8)
            ->get();
    }
}; ?>

<div
    x-data="{ sidebarOpen: false }"
    x-on:livewire:navigated.window="sidebarOpen = false"
    x-on:keydown.escape.window="sidebarOpen = false"
>
    {{-- Mobile backdrop --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"
        style="display: none;"
        x-cloak
        @click="sidebarOpen = false"
    ></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full transform flex-col bg-surface p-4 transition-transform duration-300 ease-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex items-center justify-between px-1.5 py-2">
            <a href="{{ route('dashboard') }}" wire:navigate>
                <x-brand-mark />
            </a>
            <button @click="sidebarOpen = false" aria-label="{{ __('Tutup menu') }}" class="rounded-xl p-2 text-slate-500 hover:bg-surface-card hover:text-slate-600 lg:hidden">
                <x-icon.x-mark class="h-5 w-5" />
            </button>
        </div>

        <nav class="neu-scrollbar mt-3 flex-1 space-y-1 overflow-y-auto pb-4 pr-1">
            <x-sidebar-link :href="route('dashboard')" icon="home" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-sidebar-link>

            <x-sidebar-section label="{{ __('Asesmen Saya') }}" />
            <x-sidebar-sublink :href="route('siswa.available-assessments')" :active="request()->routeIs('siswa.available-assessments')">
                {{ __('Asesmen Tersedia') }}
            </x-sidebar-sublink>
            <x-sidebar-sublink :href="route('siswa.history')" :active="request()->routeIs('siswa.history')">
                {{ __('Riwayat Asesmen') }}
            </x-sidebar-sublink>

            <x-sidebar-link :href="route('siswa.content-library')" icon="book-open" :active="request()->routeIs('siswa.content-library', 'siswa.content-detail')">
                {{ __('Literasi') }}
            </x-sidebar-link>

            @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::GuruBk))
                <x-sidebar-section label="{{ __('Data Master') }}" />
                <x-sidebar-sublink :href="route('guru-bk.academic-years')" :active="request()->routeIs('guru-bk.academic-years')">
                    {{ __('Tahun Ajaran') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.school-classes')" :active="request()->routeIs('guru-bk.school-classes')">
                    {{ __('Kelas') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.students')" :active="request()->routeIs('guru-bk.students')">
                    {{ __('Siswa') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.class-promotion')" :active="request()->routeIs('guru-bk.class-promotion')">
                    {{ __('Kenaikan Kelas') }}
                </x-sidebar-sublink>

                <x-sidebar-section label="{{ __('Asesmen') }}" />
                <x-sidebar-sublink :href="route('guru-bk.questions')" :active="request()->routeIs('guru-bk.questions')">
                    {{ __('Bank Soal') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.assessments')" :active="request()->routeIs('guru-bk.assessments')">
                    {{ __('Assessment') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.assessment-schedules')" :active="request()->routeIs('guru-bk.assessment-schedules')">
                    {{ __('Jadwal Assessment') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.results')" :active="request()->routeIs('guru-bk.results')">
                    {{ __('Hasil Assessment') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink :href="route('guru-bk.contents')" :active="request()->routeIs('guru-bk.contents')">
                    {{ __('Konten Literasi') }}
                </x-sidebar-sublink>
            @endif

            @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::Admin))
                <x-sidebar-section label="{{ __('Admin') }}" />
                <x-sidebar-link :href="route('admin.guru-bk-accounts')" icon="shield-check" :active="request()->routeIs('admin.guru-bk-accounts')">
                    {{ __('Akun Guru BK') }}
                </x-sidebar-link>
            @endif
        </nav>

        <div class="neu-raised-sm rounded-2xl p-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                    <x-icon.user-circle class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-700" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->role->label() }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- Topbar --}}
    <header class="fixed inset-x-0 top-0 z-20 flex h-16 items-center gap-3 border-b border-surface-inset bg-surface/90 px-4 backdrop-blur sm:px-6 lg:left-72 lg:px-8">
        <button @click="sidebarOpen = true" aria-label="{{ __('Buka menu navigasi') }}" :aria-expanded="sidebarOpen" class="rounded-xl p-2 text-slate-500 hover:bg-surface-card hover:text-primary-600 lg:hidden">
            <x-icon.menu class="h-5 w-5" />
        </button>

        <a href="{{ route('dashboard') }}" wire:navigate class="lg:hidden">
            <x-brand-mark :show-text="false" />
        </a>

        <div class="flex-1"></div>

        @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::GuruBk))
            <x-dropdown align="right" width="w-80">
                <x-slot name="trigger">
                    <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 neu-pressable hover:text-primary-600" aria-label="{{ __('Notifikasi') }}">
                        <x-icon.bell class="h-5 w-5" />
                        @if ($this->highCategoryNotifications->isNotEmpty())
                            <span class="absolute right-1.5 top-1.5 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-stress-tinggi px-1 text-[10px] font-semibold leading-none text-white">
                                {{ $this->highCategoryNotifications->count() }}
                            </span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="border-b border-surface-inset px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                        {{ __('Siswa Kategori Tinggi Terbaru') }}
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        @forelse ($this->highCategoryNotifications as $result)
                            <a href="{{ route('siswa.result-detail', $result) }}" wire:navigate wire:key="notification-{{ $result->id }}" class="flex items-start gap-2.5 px-4 py-2.5 text-sm hover:bg-surface">
                                <x-icon.exclamation-triangle class="mt-0.5 h-4 w-4 shrink-0 text-stress-tinggi" />
                                <span>
                                    <span class="block font-medium text-slate-800">{{ $result->student->user->name }}</span>
                                    <span class="block text-xs text-slate-500">
                                        {{ $result->student->currentClassHistory?->schoolClass?->name ?? '—' }} &middot; {{ $result->completed_at->diffForHumans() }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-sm text-slate-500">
                                {{ __('Belum ada siswa kategori Tinggi.') }}
                            </div>
                        @endforelse
                    </div>
                </x-slot>
            </x-dropdown>
        @endif

        <x-dropdown align="right" width="56">
            <x-slot name="trigger">
                <button type="button" class="flex items-center gap-2 rounded-2xl py-1.5 pl-1.5 pr-3 neu-pressable hover:bg-surface-card">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        <x-icon.user-circle class="h-5 w-5" />
                    </span>
                    <span class="hidden text-sm font-semibold text-slate-600 sm:inline" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                    <x-icon.chevron class="hidden h-3.5 w-3.5 text-slate-500 sm:inline" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-dropdown-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </button>
            </x-slot>
        </x-dropdown>
    </header>
</div>
