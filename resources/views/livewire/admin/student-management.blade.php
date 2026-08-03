<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Siswa
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 flex-1">
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari nama, email, atau NIS..."
                            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:col-span-2"
                        >
                        <select wire:model.live="classFilter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach ($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="genderFilter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Gender</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 shrink-0">
                        <button type="button" wire:click="openImportModal" class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium">
                            Impor Excel
                        </button>
                        <button type="button" wire:click="openCreateModal" class="px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium">
                            + Tambah Siswa
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 mb-4">
                    <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-100">
                                <th class="py-2 pr-4 font-medium cursor-pointer select-none" wire:click="sortBy('name')">
                                    Nama {{ $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                                </th>
                                <th class="py-2 pr-4 font-medium cursor-pointer select-none" wire:click="sortBy('nis')">
                                    NIS {{ $sortField === 'nis' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                                </th>
                                <th class="py-2 pr-4 font-medium">Kelas</th>
                                <th class="py-2 pr-4 font-medium">Gender</th>
                                <th class="py-2 pr-4 font-medium">Status</th>
                                <th class="py-2 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->students as $student)
                                <tr class="border-b border-gray-50 last:border-0">
                                    <td class="py-3 pr-4 text-gray-700">{{ $student->user->name }}</td>
                                    <td class="py-3 pr-4 text-gray-500">{{ $student->nis }}</td>
                                    <td class="py-3 pr-4 text-gray-500">{{ $student->schoolClass->name }}</td>
                                    <td class="py-3 pr-4 text-gray-500">{{ $student->gender->label() }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $student->status->value === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $student->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right space-x-3">
                                        <button type="button" wire:click="openViewModal({{ $student->id }})" class="text-gray-500 hover:text-indigo-600" title="Lihat">
                                            Lihat
                                        </button>
                                        <button type="button" wire:click="openEditModal({{ $student->id }})" class="text-indigo-600 hover:text-indigo-700" title="Edit">
                                            Edit
                                        </button>
                                        <button type="button" wire:click="confirmDelete({{ $student->id }})" class="text-red-500 hover:text-red-600" title="Hapus">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $this->students->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-modal name="student-form" maxWidth="lg">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $form->studentId ? 'Edit Siswa' : 'Tambah Siswa' }}
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-input-label for="form.name" value="Nama Lengkap" />
                    <x-text-input wire:model="form.name" id="form.name" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="form.email" value="Email" />
                    <x-text-input wire:model="form.email" id="form.email" class="block mt-1 w-full" type="email" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.nis" value="NIS" />
                    <x-text-input wire:model="form.nis" id="form.nis" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('form.nis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.class_id" value="Kelas" />
                    <select wire:model="form.class_id" id="form.class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih kelas</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.class_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.gender" value="Jenis Kelamin" />
                    <select wire:model="form.gender" id="form.gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.gender')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.status" value="Status" />
                    <select wire:model="form.status" id="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.birth_date" value="Tanggal Lahir" />
                    <x-text-input wire:model="form.birth_date" id="form.birth_date" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('form.birth_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="form.phone" value="No. Telepon" />
                    <x-text-input wire:model="form.phone" id="form.phone" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('form.phone')" class="mt-2" />
                </div>
            </div>

            @unless ($form->studentId)
                <p class="text-xs text-gray-500">Kata sandi awal akun akan sama dengan NIS. Siswa dapat menggantinya setelah masuk.</p>
            @endunless

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" x-on:click="show = false" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <x-primary-button>
                    Simpan
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="student-view" maxWidth="lg">
        <div class="p-6">
            @if ($this->viewingStudent)
                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $this->viewingStudent->user->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $this->viewingStudent->user->email }}</p>

                <dl class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div>
                        <dt class="text-gray-500">NIS</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->nis }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kelas</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->schoolClass->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jenis Kelamin</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->gender->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->status->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal Lahir</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->birth_date?->translatedFormat('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">No. Telepon</dt>
                        <dd class="font-medium text-gray-900">{{ $this->viewingStudent->phone ?? '-' }}</dd>
                    </div>
                </dl>

                <h4 class="text-sm font-semibold text-gray-700 mb-2">Riwayat Asesmen</h4>
                @if ($this->viewingStudent->assessments->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada asesmen yang diselesaikan.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($this->viewingStudent->assessments as $assessment)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <span class="text-sm text-gray-600">{{ $assessment->completed_at->translatedFormat('d M Y') }}</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->overall_severity->badgeClasses() }}">{{ $assessment->overall_severity->label() }}</span>
                                <a href="{{ route('assessment.show', $assessment) }}" wire:navigate class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Detail</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="flex justify-end pt-6">
                <button type="button" x-on:click="show = false" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>

    <x-modal name="student-delete" maxWidth="sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Siswa</h3>
            <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data siswa ini? Seluruh riwayat asesmen terkait juga akan terhapus.</p>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" wire:click="delete" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </x-modal>

    <x-modal name="student-import" maxWidth="md">
        <form wire:submit="import" class="p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Impor Data Siswa</h3>
            <p class="text-sm text-gray-500">
                Unggah file Excel/CSV sesuai format template.
                <a href="{{ route('admin.students.template') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Unduh template</a>.
            </p>

            <div>
                <input type="file" wire:model="importFile" class="block w-full text-sm text-gray-600 border border-gray-200 rounded-lg cursor-pointer">
                <x-input-error :messages="$errors->get('importFile')" class="mt-2" />
                @if ($importError)
                    <p class="text-sm text-red-600 mt-2">{{ $importError }}</p>
                @endif
                <div wire:loading wire:target="importFile" class="text-xs text-gray-400 mt-1">Mengunggah...</div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" x-on:click="show = false" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <x-primary-button wire:loading.attr="disabled" wire:target="import">
                    Impor
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
