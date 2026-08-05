<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Akun Guru BK') }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-4">
        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama atau email...') }}">
                <x-slot name="actions">
                    <x-primary-button type="button" wire:click="create">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Akun Guru BK') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="name" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Nama') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Email') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->accounts as $account)
                            <tr wire:key="account-{{ $account->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $account->name }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $account->email }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    @if ($account->is_active)
                                        <x-badge color="primary">{{ __('Aktif') }}</x-badge>
                                    @else
                                        <x-badge color="gray">{{ __('Nonaktif') }}</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $account->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action :icon="$account->is_active ? 'x-mark' : 'check-circle'" :color="$account->is_active ? 'danger' : 'primary'" wire:click="toggleActive({{ $account->id }})">
                                            {{ $account->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                        </x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state icon="shield-check" :title="__('Belum ada akun Guru BK')" :description="__('Tambahkan akun agar Guru BK bisa mulai mengelola sistem.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->accounts->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="account-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Akun Guru BK') : __('Tambah Akun Guru BK') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input wire:model="name" id="name" type="text" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" type="email" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input wire:model="password" id="password" type="password" class="mt-1.5 block w-full" placeholder="{{ $editingId ? __('Kosongkan jika tidak diubah') : '' }}" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="closeModal">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
    @endif
</div>
