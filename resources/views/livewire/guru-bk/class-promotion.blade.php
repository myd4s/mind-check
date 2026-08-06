<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Kenaikan Kelas') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('Pindahkan siswa ke kelas berikutnya di tahun ajaran baru, atau tandai lulus/keluar.') }}
        </p>
    </x-slot>

    <div class="space-y-5">
        @if ($result)
            <div class="neu-card flex items-start gap-4 border-l-4 border-mint-500 p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-mint-100 text-mint-600">
                    <x-icon.check-circle class="h-5 w-5" />
                </span>
                <div>
                    <p class="font-display font-semibold text-slate-800">{{ __('Kenaikan kelas selesai diproses.') }}</p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ __(':promoted siswa naik kelas, :graduated ditandai lulus/keluar, :skipped dilewati (sudah punya data di tahun tujuan).', ['promoted' => $result['promoted'], 'graduated' => $result['graduated'], 'skipped' => $result['skipped']]) }}
                    </p>
                </div>
            </div>
        @endif

        <x-neu-card class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="sourceYearId" :value="__('Tahun Ajaran Asal')" />
                    <div class="mt-1.5">
                        <x-select wire:model.live="sourceYearId" id="sourceYearId">
                            <option value="">{{ __('Pilih tahun ajaran') }}</option>
                            @foreach ($this->academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error :messages="$errors->get('sourceYearId')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="targetYearId" :value="__('Tahun Ajaran Tujuan')" />
                    <div class="mt-1.5">
                        <x-select wire:model.live="targetYearId" id="targetYearId">
                            <option value="">{{ __('Pilih tahun ajaran') }}</option>
                            @foreach ($this->academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error :messages="$errors->get('targetYearId')" class="mt-2" />
                </div>
            </div>

            @if ($this->sourceClassSummaries->isEmpty())
                <x-empty-state
                    icon="user-group"
                    :title="__('Tidak ada siswa aktif')"
                    :description="__('Tidak ada siswa aktif pada tahun ajaran asal yang dipilih.')"
                />
            @else
                <div class="neu-scrollbar overflow-x-auto rounded-2xl border border-surface-inset">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-surface-inset">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Kelas Asal') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jumlah Siswa') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Dipetakan ke') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-inset">
                            @foreach ($this->sourceClassSummaries as $summary)
                                <tr wire:key="summary-{{ $summary->school_class_id }}">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-slate-700">{{ $summary->schoolClass->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-semibold text-secondary-700">
                                            <x-icon.users class="h-3.5 w-3.5" />
                                            {{ $summary->student_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-select wire:model="mappings.{{ $summary->school_class_id }}" class="!py-2 text-sm">
                                            <option value="">{{ __('— Lewati —') }}</option>
                                            <option value="lulus">{{ __('Lulus / Keluar') }}</option>
                                            @foreach ($this->schoolClasses as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level }})</option>
                                            @endforeach
                                        </x-select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <x-primary-button type="button" wire:click="confirmPromotion" wire:loading.attr="disabled" wire:target="confirmPromotion">
                        {{ __('Proses Kenaikan Kelas') }}
                    </x-primary-button>
                </div>
            @endif
        </x-neu-card>
    </div>

    @if ($confirmingPromotion)
        <x-modal name="class-promotion-confirm" :show="true" maxWidth="md">
            <div class="p-6">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-accent-100 text-accent-600">
                    <x-icon.exclamation-triangle class="h-5 w-5" />
                </span>

                <h3 class="mt-3 font-display text-lg font-semibold text-slate-800">{{ __('Proses kenaikan kelas sekarang?') }}</h3>
                <p class="mt-1.5 text-sm text-slate-500">
                    {{ __('Total :total siswa akan diproses: :promote naik kelas ke tahun ajaran tujuan, :graduate ditandai lulus/keluar. Tindakan ini akan menambahkan data histori kelas baru dan tidak dapat langsung dibatalkan.', [
                        'total' => $this->promotionPreview['total'],
                        'promote' => $this->promotionPreview['willPromote'],
                        'graduate' => $this->promotionPreview['willGraduate'],
                    ]) }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button wire:click="$set('confirmingPromotion', false)">{{ __('Batal') }}</x-secondary-button>
                    <x-primary-button wire:click="promote" wire:loading.attr="disabled" wire:target="promote">{{ __('Ya, Proses Sekarang') }}</x-primary-button>
                </div>
            </div>
        </x-modal>
    @endif
</div>
