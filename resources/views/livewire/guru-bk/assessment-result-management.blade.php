<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Hasil Assessment') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama siswa atau jadwal...') }}">
                <x-slot name="filters">
                    <x-select wire:model.live="classFilter" :full="false" class="min-w-[9rem]">
                        <option value="">{{ __('Semua Kelas') }}</option>
                        @foreach ($this->schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </x-select>
                    <x-select wire:model.live="scheduleFilter" :full="false" class="min-w-[9rem]">
                        <option value="">{{ __('Semua Jadwal') }}</option>
                        @foreach ($this->schedules as $schedule)
                            <option value="{{ $schedule->id }}">{{ $schedule->title }}</option>
                        @endforeach
                    </x-select>
                    <x-select wire:model.live="categoryFilter" :full="false" class="min-w-[9rem]">
                        <option value="">{{ __('Semua Kategori') }}</option>
                        <option value="rendah">{{ __('Rendah') }}</option>
                        <option value="sedang">{{ __('Sedang') }}</option>
                        <option value="tinggi">{{ __('Tinggi') }}</option>
                    </x-select>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jadwal') }}</th>
                            <x-table.th-sort field="total_score" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Skor') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Kategori') }}</th>
                            <x-table.th-sort field="completed_at" :sort-field="$sortField" :sort-direction="$sortDirection" align="right">{{ __('Aksi') }}</x-table.th-sort>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->results as $result)
                            <tr wire:key="result-{{ $result->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $result->student->user->name }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $result->student->currentClassHistory?->schoolClass?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $result->assessmentSchedule->title }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $result->total_score }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <x-category-badge :category="$result->category" />
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <a href="{{ route('siswa.result-detail', $result) }}" wire:navigate class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                                        {{ __('Lihat Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state icon="chart-bar" :title="__('Belum ada hasil asesmen')" :description="__('Hasil akan muncul di sini setelah siswa menyelesaikan asesmen.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->results->links() }}
            </div>
        </x-neu-card>
    </div>
</div>
