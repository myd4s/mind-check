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
    x-data="{ sidebarOpen: false, logoutConfirmOpen: false }"
    x-on:livewire:navigated.window="sidebarOpen = false; logoutConfirmOpen = false"
    x-on:keydown.escape.window="sidebarOpen = false; logoutConfirmOpen = false"
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
        class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full transform flex-col border-r border-surface-inset bg-surface shadow-neu-sm p-4 transition-transform duration-300 ease-out lg:translate-x-0"
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

            @php
                $assessmentMenuActive = request()->routeIs('siswa.available-assessments', 'siswa.history');
            @endphp
            <x-sidebar-menu-group
                label="{{ __('Asesmen Saya') }}"
                icon="clipboard-list"
                :activeSubmenu="$assessmentMenuActive"
            >
                <x-sidebar-sublink
                    :href="route('siswa.available-assessments')"
                    :active="request()->routeIs('siswa.available-assessments')"
                    icon="clipboard-check"
                >
                    {{ __('Asesmen Tersedia') }}
                </x-sidebar-sublink>
                <x-sidebar-sublink
                    :href="route('siswa.history')"
                    :active="request()->routeIs('siswa.history')"
                    icon="chart-bar"
                >
                    {{ __('Riwayat Asesmen') }}
                </x-sidebar-sublink>
            </x-sidebar-menu-group>

            <x-sidebar-link :href="route('siswa.content-library')" icon="book-open" :active="request()->routeIs('siswa.content-library', 'siswa.content-detail')">
                {{ __('Literasi') }}
            </x-sidebar-link>

            @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::GuruBk))
                @php
                    $dataMasterMenuActive = request()->routeIs('guru-bk.academic-years', 'guru-bk.school-classes', 'guru-bk.students', 'guru-bk.class-promotion');
                    $assessmentMenuActive = request()->routeIs('guru-bk.questions', 'guru-bk.assessments', 'guru-bk.assessment-schedules', 'guru-bk.results', 'guru-bk.contents');
                @endphp
                <x-sidebar-menu-group
                    label="{{ __('Data Master') }}"
                    icon="clipboard-list"
                    :activeSubmenu="$dataMasterMenuActive"
                >
                    <x-sidebar-sublink
                        :href="route('guru-bk.academic-years')"
                        :active="request()->routeIs('guru-bk.academic-years')"
                        icon="calendar"
                    >
                        {{ __('Tahun Ajaran') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.school-classes')"
                        :active="request()->routeIs('guru-bk.school-classes')"
                        icon="user-group"
                    >
                        {{ __('Kelas') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.students')"
                        :active="request()->routeIs('guru-bk.students')"
                        icon="users"
                    >
                        {{ __('Siswa') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.class-promotion')"
                        :active="request()->routeIs('guru-bk.class-promotion')"
                        icon="check-circle"
                    >
                        {{ __('Kenaikan Kelas') }}
                    </x-sidebar-sublink>
                </x-sidebar-menu-group>

                <x-sidebar-menu-group
                    label="{{ __('Asesmen') }}"
                    icon="clipboard-check"
                    :activeSubmenu="$assessmentMenuActive"
                >
                    <x-sidebar-sublink
                        :href="route('guru-bk.questions')"
                        :active="request()->routeIs('guru-bk.questions')"
                        icon="document-text"
                    >
                        {{ __('Bank Soal') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.assessments')"
                        :active="request()->routeIs('guru-bk.assessments')"
                        icon="chart-pie"
                    >
                        {{ __('Assessment') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.assessment-schedules')"
                        :active="request()->routeIs('guru-bk.assessment-schedules')"
                        icon="calendar"
                    >
                        {{ __('Jadwal Assessment') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.results')"
                        :active="request()->routeIs('guru-bk.results')"
                        icon="chart-bar"
                    >
                        {{ __('Hasil Assessment') }}
                    </x-sidebar-sublink>
                    <x-sidebar-sublink
                        :href="route('guru-bk.contents')"
                        :active="request()->routeIs('guru-bk.contents')"
                        icon="book-open"
                    >
                        {{ __('Konten Literasi') }}
                    </x-sidebar-sublink>
                </x-sidebar-menu-group>
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

        <x-dropdown align="right" width="w-80">
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
                <!-- User Info Section -->
                <div class="border-b border-surface-inset px-4 py-3.5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                            <x-icon.user-circle class="h-6 w-6" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                            <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-1 p-2">
                    <a
                        href="{{ route('profile') }}"
                        wire:navigate
                        class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors text-slate-600 hover:bg-surface-card hover:text-primary-600"
                    >
                        <x-icon.user-circle class="h-4 w-4 shrink-0" />
                        {{ __('Profil') }}
                    </a>

                    <button
                        @click="logoutConfirmOpen = true"
                        class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors text-stress-tinggi hover:bg-stress-tinggi/10"
                    >
                        <x-icon.logout class="h-4 w-4 shrink-0" />
                        {{ __('Keluar') }}
                    </button>
                </div>
            </x-slot>
        </x-dropdown>
    </header>

    {{-- Logout Confirmation Modal --}}
    <div
        x-show="logoutConfirmOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40"
        style="display: none;"
        x-cloak
        @click.self="logoutConfirmOpen = false"
    >
        <div
            x-show="logoutConfirmOpen"
            x-transition:enter="transition-all ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition-all ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="rounded-3xl bg-surface p-6 sm:w-96"
            @click.stop
        >
            {{-- Icon --}}
            <div class="flex justify-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-stress-tinggi/10 text-stress-tinggi">
                    <x-icon.exclamation-triangle class="h-6 w-6" />
                </span>
            </div>

            {{-- Content --}}
            <div class="mt-4 text-center">
                <h3 class="font-display text-lg font-semibold text-slate-800">
                    {{ __('Yakin ingin keluar?') }}
                </h3>
                <p class="mt-2 text-sm text-slate-500">
                    {{ __('Anda akan keluar dari aplikasi dan perlu login kembali untuk akses kembali.') }}
                </p>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex gap-3">
                <button
                    @click="logoutConfirmOpen = false"
                    class="flex-1 rounded-2xl bg-surface-card px-4 py-2.5 text-sm font-semibold text-slate-600 transition-colors hover:bg-surface-inset"
                >
                    {{ __('Batal') }}
                </button>
                <button
                    wire:click="logout"
                    class="flex-1 rounded-2xl bg-stress-tinggi px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700"
                >
                    {{ __('Keluar') }}
                </button>
            </div>
        </div>
    </div>
</div>
