<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Konten Literasi') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari judul konten...') }}">
                <x-slot name="actions">
                    <x-primary-button type="button" wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Konten') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="title" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Judul') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Tipe') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Penulis') }}</th>
                            <x-table.th-sort field="published_at" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Tanggal Publish') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->contents as $content)
                            <tr wire:key="content-{{ $content->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $content->title }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm capitalize text-slate-500">
                                    <x-badge :color="$content->type === 'video' ? 'bubblegum' : 'mint'">{{ $content->type }}</x-badge>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $content->author ?: '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $content->published_at->translatedFormat('d M Y') }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $content->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $content->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state icon="book-open" :title="__('Belum ada konten literasi')" :description="__('Tambahkan artikel atau video penanganan stress untuk siswa.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->contents->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="content-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Konten') : __('Tambah Konten') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Judul')" />
                    <x-text-input wire:model="title" id="title" type="text" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" :value="__('Tipe')" />
                    <div class="mt-1.5">
                        <x-select wire:model.live="type" id="type">
                            <option value="artikel">{{ __('Artikel') }}</option>
                            <option value="video">{{ __('Video') }}</option>
                        </x-select>
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                @if ($type === 'video')
                    <div>
                        <x-input-label for="video_url" :value="__('URL Video')" />
                        <x-text-input wire:model="video_url" id="video_url" type="url" class="mt-1.5 block w-full" placeholder="https://youtube.com/..." />
                        <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                    </div>
                @endif

                <div>
                    <x-input-label for="description" :value="__('Isi / Deskripsi')" />
                    <x-textarea wire:model="description" id="description" rows="5" class="mt-1.5" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="author" :value="__('Penulis')" />
                    <x-text-input wire:model="author" id="author" type="text" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('author')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="published_at" :value="__('Tanggal Publish')" />
                    <x-text-input wire:model="published_at" id="published_at" type="date" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
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
    <x-modal name="content-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Konten?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
