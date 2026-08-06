<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Asesmen Tersedia') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('Kerjakan asesmen berikut sebelum jadwalnya berakhir.') }}
        </p>
    </x-slot>

    <div class="space-y-4">
        @forelse ($this->availableSchedules as $schedule)
            @php
                $questionCount = $schedule->assessment->questions->count();
                $endsSoon = now()->diffInHours($schedule->end_at, false) <= 24;
            @endphp
            <div wire:key="schedule-{{ $schedule->id }}" class="neu-card flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-accent-100 text-accent-600">
                        <x-icon.clipboard-list class="h-7 w-7" />
                    </span>
                    <div class="min-w-0">
                        <h3 class="font-display text-lg font-semibold text-slate-800">{{ $schedule->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $schedule->assessment->title }}</p>

                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-semibold text-secondary-700">
                                <x-icon.document-text class="h-3.5 w-3.5" />
                                {{ __(':count Soal', ['count' => $questionCount]) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $endsSoon ? 'bg-stress-tinggi/10 text-stress-tinggi' : 'bg-surface text-slate-500' }}">
                                <x-icon.calendar class="h-3.5 w-3.5" />
                                {{ __('Berakhir') }} {{ $schedule->end_at->translatedFormat('d M Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('siswa.assessment-wizard', $schedule) }}" wire:navigate class="shrink-0 sm:pl-4">
                    <x-primary-button type="button" class="w-full justify-center sm:w-auto">
                        {{ __('Kerjakan') }}
                        <x-icon.chevron direction="right" class="h-4 w-4" />
                    </x-primary-button>
                </a>
            </div>
        @empty
            <x-neu-card>
                <x-empty-state
                    icon="check-circle"
                    :title="__('Tidak ada asesmen yang perlu dikerjakan')"
                    :description="__('Semua asesmen yang dijadwalkan untuk kamu sudah selesai. Kerja bagus!')"
                />
            </x-neu-card>
        @endforelse
    </div>
</div>
