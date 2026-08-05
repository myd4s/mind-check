<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Jadwal Assessment') }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari judul jadwal...') }}">
                <x-slot name="actions">
                    <x-primary-button type="button" wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Jadwal') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="title" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Judul') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Assessment') }}</th>
                            <x-table.th-sort field="start_at" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Periode') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Target') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->schedules as $schedule)
                            <tr wire:key="schedule-{{ $schedule->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $schedule->title }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $schedule->assessment->title }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">
                                    {{ $schedule->start_at->translatedFormat('d M Y H:i') }} &ndash; {{ $schedule->end_at->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">
                                    {{ $schedule->target_type === 'all' ? __('Semua Kelas') : __('Kelas Tertentu') }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    @if (! $schedule->is_active)
                                        <x-badge color="gray">{{ __('Nonaktif') }}</x-badge>
                                    @elseif ($schedule->isOpenNow())
                                        <x-badge color="primary">{{ __('Berlangsung') }}</x-badge>
                                    @elseif (now()->lt($schedule->start_at))
                                        <x-badge color="warning">{{ __('Belum Dimulai') }}</x-badge>
                                    @else
                                        <x-badge color="gray">{{ __('Berakhir') }}</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="document-text" color="secondary" wire:click="downloadResultsExport({{ $schedule->id }})">{{ __('Export') }}</x-row-action>
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $schedule->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $schedule->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state icon="calendar" :title="__('Belum ada jadwal assessment')" :description="__('Buat jadwal agar siswa bisa mulai mengerjakan asesmen.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->schedules->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="schedule-form" :show="true" maxWidth="2xl" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Jadwal') : __('Tambah Jadwal') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Judul Jadwal')" />
                    <x-text-input wire:model="title" id="title" type="text" class="mt-1.5 block w-full" placeholder="mis. Asesmen Stress Semester Ganjil 2026" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="assessment_id" :value="__('Assessment')" />
                        <div class="mt-1.5">
                            <x-select wire:model="assessment_id" id="assessment_id">
                                <option value="">{{ __('Pilih assessment') }}</option>
                                @foreach ($this->assessments as $assessment)
                                    <option value="{{ $assessment->id }}">{{ $assessment->title }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <x-input-error :messages="$errors->get('assessment_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="academic_year_id" :value="__('Tahun Ajaran')" />
                        <div class="mt-1.5">
                            <x-select wire:model="academic_year_id" id="academic_year_id">
                                <option value="">{{ __('Pilih tahun ajaran') }}</option>
                                @foreach ($this->academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_at" :value="__('Mulai')" />
                        <x-text-input wire:model="start_at" id="start_at" type="datetime-local" class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_at" :value="__('Selesai')" />
                        <x-text-input wire:model="end_at" id="end_at" type="datetime-local" class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('Target Siswa')" />
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2">
                            <input wire:model.live="target_type" type="radio" value="all" class="text-primary-600 focus:ring-primary-400">
                            <span class="text-sm text-slate-600">{{ __('Semua Kelas') }}</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input wire:model.live="target_type" type="radio" value="specific" class="text-primary-600 focus:ring-primary-400">
                            <span class="text-sm text-slate-600">{{ __('Kelas Tertentu') }}</span>
                        </label>
                    </div>

                    @if ($target_type === 'specific')
                        <x-input-error :messages="$errors->get('target_class_ids')" class="mt-1" />
                        <div class="neu-inset-sm mt-2 max-h-48 divide-y divide-surface-inset overflow-y-auto rounded-2xl">
                            @foreach ($this->schoolClasses as $class)
                                <label class="flex cursor-pointer items-center gap-2 px-4 py-2.5 hover:bg-surface-card">
                                    <input type="checkbox" wire:model="target_class_ids.{{ $class->id }}" class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                                    <span class="text-sm text-slate-700">{{ $class->name }} ({{ $class->grade_level }})</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <label class="flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                    <span class="text-sm text-slate-600">{{ __('Aktif') }}</span>
                </label>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="closeModal">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($deletingId !== null)
    <x-modal name="schedule-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Jadwal?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
