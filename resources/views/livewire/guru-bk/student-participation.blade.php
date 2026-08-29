<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Partisipasi Asesmen') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        {{-- Filter --}}
        <div class="neu-card grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <x-input-label for="academicYearFilter" :value="__('Tahun Ajaran')" />
                <div class="mt-1.5">
                    <x-select wire:model.live="academicYearFilter" id="academicYearFilter">
                        @foreach ($this->academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
            <div>
                <x-input-label for="scheduleFilter" :value="__('Jadwal Asesmen')" />
                <div class="mt-1.5">
                    <x-select wire:model.live="scheduleFilter" id="scheduleFilter">
                        @forelse ($this->schedules as $schedule)
                            <option value="{{ $schedule->id }}">{{ $schedule->title }}</option>
                        @empty
                            <option value="">{{ __('Belum ada jadwal') }}</option>
                        @endforelse
                    </x-select>
                </div>
            </div>
            <div>
                <x-input-label for="classFilter" :value="__('Kelas')" />
                <div class="mt-1.5">
                    <x-select wire:model.live="classFilter" id="classFilter">
                        <option value="">{{ __('Semua Kelas') }}</option>
                        @foreach ($this->schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
            <div>
                <x-input-label for="statusFilter" :value="__('Status')" />
                <div class="mt-1.5">
                    <x-select wire:model.live="statusFilter" id="statusFilter">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="sudah">{{ __('Sudah Mengerjakan') }}</option>
                        <option value="belum">{{ __('Belum Mengerjakan') }}</option>
                    </x-select>
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-stat-card icon="users" color="primary" :label="__('Total Siswa Sasaran')" :value="$this->summary['total']" />
            <x-stat-card icon="clipboard-check" color="mint" :label="__('Sudah Mengerjakan')" :value="$this->summary['sudah']" />
            <x-stat-card icon="clipboard-list" color="sunshine" :label="__('Belum Mengerjakan')" :value="$this->summary['belum']" />
        </div>

        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama atau NISN siswa...') }}" />

            @if (! $this->currentSchedule)
                <x-empty-state
                    icon="calendar"
                    :title="__('Pilih jadwal asesmen')"
                    :description="__('Belum ada jadwal asesmen pada tahun ajaran ini, atau jadwal belum dipilih.')"
                />
            @else
                <div class="neu-scrollbar overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y border-surface-inset">
                                <x-table.th-sort field="name" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Siswa') }}</x-table.th-sort>
                                <x-table.th-sort field="class" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Kelas') }}</x-table.th-sort>
                                <x-table.th-sort field="status" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Status') }}</x-table.th-sort>
                                <x-table.th-sort field="category" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Kategori') }}</x-table.th-sort>
                                <x-table.th-sort field="completed_at" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Tanggal Kerjakan') }}</x-table.th-sort>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-inset">
                            @forelse ($this->rows as $row)
                                <tr wire:key="participation-{{ $row['student_id'] }}">
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <p class="text-sm font-semibold text-slate-700">{{ $row['name'] }}</p>
                                        <p class="text-xs text-slate-500">{{ __('NISN') }}: {{ $row['nisn'] ?: '—' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $row['class'] }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        @if ($row['done'])
                                            <x-badge color="success">{{ __('Sudah') }}</x-badge>
                                        @else
                                            <x-badge color="warning">{{ __('Belum') }}</x-badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        @if ($row['category'])
                                            <x-category-badge :category="$row['category']" />
                                        @else
                                            <span class="text-sm text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">
                                        {{ $row['completed_at']?->translatedFormat('d M Y H:i') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm">
                                        @if ($row['result_id'])
                                            <a href="{{ route('siswa.result-detail', $row['result_id']) }}" wire:navigate class="font-semibold text-primary-600 hover:text-primary-700">
                                                {{ __('Lihat Detail') }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-empty-state
                                            icon="users"
                                            :title="__('Tidak ada siswa')"
                                            :description="__('Tidak ada siswa yang cocok dengan filter yang dipilih.')"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4">
                    {{ $this->rows->links() }}
                </div>
            @endif
        </x-neu-card>
    </div>
</div>
