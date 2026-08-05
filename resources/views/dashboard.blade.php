<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php $role = auth()->user()->role; @endphp

    <div class="max-w-7xl mx-auto">
        @if ($role === \App\Enums\UserRole::Siswa)
            <livewire:siswa.dashboard />
        @else
            <livewire:guru-bk.dashboard />
        @endif
    </div>
</x-app-layout>
