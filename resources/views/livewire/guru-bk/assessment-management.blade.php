<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Assessment') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari judul assessment...') }}">
                <x-slot name="actions">
                    <x-primary-button type="button" wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Assessment') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="title" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Judul') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Deskripsi') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jumlah Soal') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->assessments as $assessment)
                            <tr wire:key="assessment-{{ $assessment->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $assessment->title }}</td>
                                <td class="max-w-md truncate px-5 py-3.5 text-sm text-slate-500">{{ $assessment->description }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $assessment->questions_count }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $assessment->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $assessment->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state icon="clipboard-check" :title="__('Belum ada paket assessment')" :description="__('Buat paket assessment dari kombinasi soal di bank soal.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->assessments->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="assessment-form" :show="true" maxWidth="2xl" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Assessment') : __('Tambah Assessment') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Judul')" />
                    <x-text-input wire:model="title" id="title" type="text" class="mt-1.5 block w-full" placeholder="mis. Asesmen Stress Semester Ganjil" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                    <x-textarea wire:model="description" id="description" rows="2" class="mt-1.5" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label :value="__('Pilih Soal')" />
                    <x-input-error :messages="$errors->get('selectedQuestionIds')" class="mt-1" />
                    <div class="neu-inset-sm mt-2 max-h-72 divide-y divide-surface-inset overflow-y-auto rounded-2xl">
                        @foreach ($this->allQuestions as $question)
                            <label class="flex cursor-pointer items-start gap-3 px-4 py-2.5 hover:bg-surface-card">
                                <input type="checkbox" wire:model="selectedQuestionIds.{{ $question->id }}" class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                                <span class="text-sm text-slate-700">
                                    {{ $question->text }}
                                    @if ($question->is_core)
                                        <x-badge color="secondary" class="ms-1">PSS-10</x-badge>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
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
    <x-modal name="assessment-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Assessment?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
