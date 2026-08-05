<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asesmen Tersedia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($this->availableSchedules as $schedule)
                <div wire:key="schedule-{{ $schedule->id }}" class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-medium text-gray-900">{{ $schedule->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $schedule->assessment->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('Berakhir') }} {{ $schedule->end_at->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                    <a href="{{ route('siswa.assessment-wizard', $schedule) }}" wire:navigate>
                        <x-primary-button type="button">{{ __('Kerjakan') }}</x-primary-button>
                    </a>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center text-sm text-gray-500">
                    {{ __('Tidak ada asesmen yang perlu dikerjakan saat ini.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
