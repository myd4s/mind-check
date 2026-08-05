<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama tahun ajaran...') }}">
                <x-slot name="actions">
                    <x-primary-button wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Tahun Ajaran') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="name" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Nama') }}</x-table.th-sort>
                            <x-table.th-sort field="start_date" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Periode') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->academicYears as $year)
                            <tr wire:key="year-{{ $year->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $year->name }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">
                                    {{ $year->start_date->translatedFormat('d M Y') }} &ndash; {{ $year->end_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    @if ($year->is_active)
                                        <x-badge color="primary">{{ __('Aktif') }}</x-badge>
                                    @else
                                        <x-badge color="gray">{{ __('Nonaktif') }}</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $year->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $year->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state icon="calendar" :title="__('Belum ada data tahun ajaran')" :description="__('Tambahkan tahun ajaran untuk mulai mengelola kelas dan siswa.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->academicYears->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="year-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Tahun Ajaran') : __('Tambah Tahun Ajaran') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nama Tahun Ajaran')" />
                    <x-text-input wire:model="name" id="name" type="text" class="mt-1.5 block w-full" placeholder="mis. 2026/2027" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                        <x-text-input wire:model="start_date" id="start_date" type="date" class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                        <x-text-input wire:model="end_date" id="end_date" type="date" class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <label class="flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                    <span class="text-sm text-slate-600">{{ __('Jadikan tahun ajaran aktif') }}</span>
                </label>
                @if ($is_active)
                    <p class="text-xs text-slate-500">{{ __('Tahun ajaran aktif lainnya akan otomatis dinonaktifkan.') }}</p>
                @endif
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
    <x-modal name="year-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Tahun Ajaran?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
