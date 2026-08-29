<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Siswa') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">

        @if ($lastCreatedCredentials)
            <div class="neu-card flex items-start justify-between gap-4 border-l-4 border-primary-500 p-4">
                <div class="text-sm text-slate-700">
                    <p class="mb-1 font-semibold text-primary-700">{{ __('Akun siswa berhasil dibuat.') }}</p>
                    <p>{{ __('Email login') }}: <span class="font-mono">{{ $lastCreatedCredentials['email'] }}</span></p>
                    <p>{{ __('Password default') }}: <span class="font-mono">{{ $lastCreatedCredentials['password'] }}</span></p>
                </div>
                <button wire:click="$set('lastCreatedCredentials', null)" class="shrink-0 text-sm font-semibold text-primary-600 hover:text-primary-800">
                    {{ __('Tutup') }}
                </button>
            </div>
        @endif

        @if ($resetPasswordResult)
            <div class="neu-card flex items-start justify-between gap-4 border-l-4 border-primary-500 p-4">
                <div class="text-sm text-slate-700">
                    <p class="mb-1 font-semibold text-primary-700">{{ __('Password :name berhasil direset.', ['name' => $resetPasswordResult['name']]) }}</p>
                    <p>{{ __('Email login') }}: <span class="font-mono">{{ $resetPasswordResult['email'] }}</span></p>
                    <p>{{ __('Password baru') }}: <span class="font-mono">{{ $resetPasswordResult['password'] }}</span></p>
                </div>
                <button wire:click="$set('resetPasswordResult', null)" class="shrink-0 text-sm font-semibold text-primary-600 hover:text-primary-800">
                    {{ __('Tutup') }}
                </button>
            </div>
        @endif

        @if (! $this->activeAcademicYear)
            <div class="neu-card flex items-center gap-3 border-l-4 border-sunshine-500 p-4 text-sm text-slate-700">
                <x-icon.exclamation-triangle class="h-5 w-5 shrink-0 text-sunshine-600" />
                {{ __('Belum ada tahun ajaran aktif. Aktifkan salah satu tahun ajaran terlebih dahulu sebelum menambahkan siswa.') }}
            </div>
        @endif

        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari nama atau NISN...') }}">
                <x-slot name="filters">
                    <x-select wire:model.live="classFilter" :full="false" class="min-w-[9rem]">
                        <option value="">{{ __('Semua Kelas') }}</option>
                        @foreach ($this->schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </x-select>
                </x-slot>
                <x-slot name="actions">
                    <x-secondary-button type="button" wire:click="openImportModal" :disabled="! $this->activeAcademicYear">
                        {{ __('Import Excel') }}
                    </x-secondary-button>
                    <x-primary-button type="button" wire:click="create" :disabled="! $this->activeAcademicYear">
                        <x-icon.plus class="h-4 w-4" /> {{ __('Tambah Siswa') }}
                    </x-primary-button>
                </x-slot>
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <x-table.th-sort field="users.name" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Nama') }}</x-table.th-sort>
                            <x-table.th-sort field="students.nisn" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('NISN') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jenis Kelamin') }}</th>
                            <x-table.th-sort field="school_classes.grade_level" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Kelas') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->students as $student)
                            <tr wire:key="student-{{ $student->id }}">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $student->user->name }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $student->nisn }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $student->gender->label() }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">
                                    {{ $student->currentClassHistory?->schoolClass?->name ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    @if ($student->currentClassHistory?->status === 'aktif')
                                        <x-badge color="primary">{{ __('Aktif') }}</x-badge>
                                    @else
                                        <x-badge color="gray">{{ __('Nonaktif') }}</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-row-action icon="pencil" color="primary" wire:click="edit({{ $student->id }})">{{ __('Edit') }}</x-row-action>
                                        <x-row-action icon="key" color="secondary" wire:click="confirmResetPassword({{ $student->id }})">{{ __('Reset Password') }}</x-row-action>
                                        @if ($student->currentClassHistory?->status === 'aktif')
                                            <x-row-action icon="x-mark" color="danger" wire:click="confirmDeactivate({{ $student->id }})">{{ __('Nonaktifkan') }}</x-row-action>
                                        @endif
                                        <x-row-action icon="trash" color="danger" wire:click="confirmDelete({{ $student->id }})">{{ __('Hapus') }}</x-row-action>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state icon="users" :title="__('Belum ada data siswa')" :description="__('Tambahkan siswa secara manual atau import lewat Excel.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->students->links() }}
            </div>
        </x-neu-card>
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
    <x-modal name="student-form" :show="true" maxWidth="lg" focusable>
        <form wire:submit="save" class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">
                {{ $editingId ? __('Edit Siswa') : __('Tambah Siswa') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input wire:model="name" id="name" type="text" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nisn" :value="__('NISN')" />
                    <x-text-input wire:model="nisn" id="nisn" type="text" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('nisn')" class="mt-2" />
                    @unless ($editingId)
                        <p class="mt-1 text-xs text-slate-500">{{ __('Akan menjadi password default & bagian dari email login siswa.') }}</p>
                    @else
                        <p class="mt-1 text-xs text-slate-500">{{ __('Mengubah NISN turut memperbarui email login siswa (password tidak berubah).') }}</p>
                    @endunless
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                    <div class="mt-1.5">
                        <x-select wire:model="gender" id="gender">
                            <option value="">{{ __('Pilih jenis kelamin') }}</option>
                            @foreach (\App\Enums\Gender::cases() as $genderOption)
                                <option value="{{ $genderOption->value }}">{{ $genderOption->label() }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="school_class_id" :value="__('Kelas')" />
                    <div class="mt-1.5">
                        <x-select wire:model="school_class_id" id="school_class_id">
                            <option value="">{{ __('Pilih kelas') }}</option>
                            @foreach ($this->schoolClasses as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level }})</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="closeModal">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal Konfirmasi Nonaktifkan --}}
    @if ($deactivatingId !== null)
    <x-modal name="student-deactivate" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Nonaktifkan Siswa?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Data & histori asesmen siswa tetap tersimpan. Siswa tidak akan muncul lagi di kelas aktif.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deactivatingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="deactivate">{{ __('Nonaktifkan') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif

    {{-- Modal Konfirmasi Reset Password --}}
    @if ($resettingPasswordId !== null)
    <x-modal name="student-reset-password" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Reset Password Siswa?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Password siswa akan dikembalikan ke NISN, dan siswa akan diminta mengganti password saat login berikutnya.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('resettingPasswordId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="resetPassword">{{ __('Reset Password') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($deletingId !== null)
    <x-modal name="student-delete" :show="true" maxWidth="md">
        <div class="p-6">
            <h3 class="mb-2 font-display text-lg font-semibold text-slate-800">{{ __('Hapus Siswa?') }}</h3>
            <p class="mb-6 text-sm text-slate-500">{{ __('Akun login, histori kelas, dan seluruh hasil asesmen siswa ini akan ikut terhapus permanen. Tindakan ini tidak bisa dibatalkan.') }}</p>
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deletingId', null)">{{ __('Batal') }}</x-secondary-button>
                <x-danger-button wire:click="delete">{{ __('Hapus') }}</x-danger-button>
            </div>
        </div>
    </x-modal>
    @endif

    {{-- Modal Import Excel --}}
    @if ($showImportModal)
    <x-modal name="student-import" :show="true" maxWidth="lg">
        <div class="p-6">
            <h3 class="mb-4 font-display text-lg font-semibold text-slate-800">{{ __('Import Siswa via Excel') }}</h3>

            <p class="mb-4 text-sm text-slate-500">
                {{ __('Kolom yang dibutuhkan: NISN, Nama, Jenis Kelamin, Kelas.') }}
                <button type="button" wire:click="downloadTemplate" class="font-semibold text-primary-600 underline hover:text-primary-800">
                    {{ __('Unduh template') }}
                </button>
            </p>

            @if ($importResults)
                <div class="mb-4 rounded-2xl border-l-4 {{ $importResults['error'] > 0 ? 'border-sunshine-500 bg-sunshine-50' : 'border-primary-500 bg-primary-50' }} p-4">
                    <p class="mb-2 text-sm font-semibold {{ $importResults['error'] > 0 ? 'text-sunshine-800' : 'text-primary-800' }}">
                        {{ __(':success berhasil, :error gagal.', ['success' => $importResults['success'], 'error' => $importResults['error']]) }}
                    </p>
                    @if ($importResults['error'] > 0)
                        <ul class="max-h-40 space-y-1 overflow-y-auto text-xs text-sunshine-800">
                            @foreach ($importResults['rows'] as $row)
                                @if ($row['status'] === 'error')
                                    <li>{{ __('Baris :row (NISN :nisn): :message', ['row' => $row['row'], 'nisn' => $row['nisn'] ?: '-', 'message' => $row['message']]) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div>
                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100" />
                <x-input-error :messages="$errors->get('importFile')" class="mt-2" />
                <div wire:loading wire:target="importFile" class="mt-1 text-xs text-slate-500">{{ __('Mengunggah...') }}</div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="closeImportModal">{{ __('Tutup') }}</x-secondary-button>
                <x-primary-button type="button" wire:click="import" wire:loading.attr="disabled" wire:target="import">
                    {{ __('Proses Import') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
