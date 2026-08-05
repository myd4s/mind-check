<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kenaikan Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($result)
                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4 text-sm text-primary-800">
                    <p class="font-medium mb-1">{{ __('Kenaikan kelas selesai diproses.') }}</p>
                    <p>{{ __(':promoted siswa naik kelas, :graduated ditandai lulus/keluar, :skipped dilewati (sudah punya data di tahun tujuan).', ['promoted' => $result['promoted'], 'graduated' => $result['graduated'], 'skipped' => $result['skipped']]) }}</p>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="sourceYearId" :value="__('Tahun Ajaran Asal')" />
                        <select wire:model.live="sourceYearId" id="sourceYearId" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="">{{ __('Pilih tahun ajaran') }}</option>
                            @foreach ($this->academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sourceYearId')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="targetYearId" :value="__('Tahun Ajaran Tujuan')" />
                        <select wire:model.live="targetYearId" id="targetYearId" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="">{{ __('Pilih tahun ajaran') }}</option>
                            @foreach ($this->academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('targetYearId')" class="mt-2" />
                    </div>
                </div>

                @if ($this->sourceClassSummaries->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Tidak ada siswa aktif pada tahun ajaran asal yang dipilih.') }}</p>
                @else
                    <div class="border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kelas Asal') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jumlah Siswa') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Dipetakan ke') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($this->sourceClassSummaries as $summary)
                                    <tr wire:key="summary-{{ $summary->school_class_id }}">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $summary->schoolClass->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $summary->student_count }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <select wire:model="mappings.{{ $summary->school_class_id }}" class="block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                                <option value="">{{ __('— Lewati —') }}</option>
                                                <option value="lulus">{{ __('Lulus / Keluar') }}</option>
                                                @foreach ($this->schoolClasses as $class)
                                                    <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button type="button" wire:click="promote" wire:loading.attr="disabled" wire:target="promote">
                            {{ __('Proses Kenaikan Kelas') }}
                        </x-primary-button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
