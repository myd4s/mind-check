<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Kelas') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama kelas...') }}">
                <x-slot name="actions">
                    <x-primary-button type="button" wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Kelas') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Nama Kelas') }}</th>
                            <x-table.th-sort field="grade_level" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Tingkat') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->schoolClasses as $class)
                            <tr wire:key="class-{{ $class->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $class->name }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $class->grade_level }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $class->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $class->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <x-empty-state icon="user-group" :title="__('Belum ada data kelas')" :description="__('Tambahkan kelas untuk mulai menempatkan siswa.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->schoolClasses->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="class-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Kelas') : __('Tambah Kelas') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nama Kelas')" />
                    <x-text-input wire:model="name" id="name" type="text" class="mt-1.5 block w-full" placeholder="mis. X IPA 1" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="grade_level" :value="__('Tingkat')" />
                    <x-text-input wire:model="grade_level" id="grade_level" type="text" class="mt-1.5 block w-full" placeholder="mis. X" />
                    <x-input-error :messages="$errors->get('grade_level')" class="mt-2" />
                </div>
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
    <x-modal name="class-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Kelas?') }}</h3>
            @if ($deleteError)
                <p class="mb-4 text-sm text-red-600">{{ $deleteError }}</p>
            @else
                <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            @endif
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
