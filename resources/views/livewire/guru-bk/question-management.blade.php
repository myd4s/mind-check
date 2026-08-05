<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Bank Soal') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Soal Inti PSS-10 --}}
        <x-neu-card padding="p-0">
            <div class="px-6 py-4">
                <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Soal Inti PSS-10') }}</h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ __('10 soal standar Perceived Stress Scale — redaksi & skema reverse-scoring bersifat baku dan tidak dapat diubah agar hasil pengukuran tetap valid secara psikometri.') }}
                </p>
            </div>
            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <th class="w-12 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Pertanyaan') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reverse-scored') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @foreach ($this->coreQuestions as $question)
                            <tr wire:key="core-{{ $question->id }}">
                                <td class="px-6 py-3 text-sm text-slate-500">{{ $question->order }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700">{{ $question->text }}</td>
                                <td class="px-6 py-3 text-sm">
                                    @if ($question->reverse_scored)
                                        <x-badge color="secondary">{{ __('Ya') }}</x-badge>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-neu-card>

        {{-- Soal Tambahan --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Soal Tambahan') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Soal opsional pendamping PSS-10, dapat dikelola bebas.') }}</p>
                </div>
            </div>

            <x-neu-card padding="p-0">
                <x-table.toolbar placeholder="{{ __('Cari pertanyaan...') }}">
                    <x-slot name="actions">
                        <x-primary-button type="button" wire:click="create">
                            <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Soal') }}
                        </x-primary-button>
                    </x-slot>
                </x-table.toolbar>

                <div class="neu-scrollbar overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y border-surface-inset">
                                <x-table.th-sort field="text" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Pertanyaan') }}</x-table.th-sort>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reverse-scored') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-inset">
                            @forelse ($this->customQuestions as $question)
                                <tr wire:key="custom-{{ $question->id }}">
                                    <td class="px-5 py-3.5 text-sm text-slate-700">{{ $question->text }}</td>
                                    <td class="px-5 py-3.5 text-sm">
                                        @if ($question->reverse_scored)
                                            <x-badge color="secondary">{{ __('Ya') }}</x-badge>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-sm">
                                        @if ($question->is_active)
                                            <x-badge color="primary">{{ __('Aktif') }}</x-badge>
                                        @else
                                            <x-badge color="gray">{{ __('Nonaktif') }}</x-badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <x-row-action icon="pencil" color="primary" wire:click="edit({{ $question->id }})">{{ __('Edit') }}</x-row-action>
                                            <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $question->id }})">{{ __('Hapus') }}</x-row-action>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="clipboard-list" :title="__('Belum ada soal tambahan')" :description="__('Tambahkan soal pendamping untuk melengkapi bank soal PSS-10.')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4">
                    {{ $this->customQuestions->links() }}
                </div>
            </x-neu-card>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="question-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Soal') : __('Tambah Soal') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="text" :value="__('Pertanyaan')" />
                    <x-textarea wire:model="text" id="text" rows="3" class="mt-1.5" />
                    <x-input-error :messages="$errors->get('text')" class="mt-2" />
                </div>

                <label class="flex items-center gap-2">
                    <input wire:model="reverse_scored" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                    <span class="text-sm text-slate-600">{{ __('Reverse-scored (nilai jawaban dibalik saat skoring)') }}</span>
                </label>

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
    <x-modal name="question-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Soal?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
