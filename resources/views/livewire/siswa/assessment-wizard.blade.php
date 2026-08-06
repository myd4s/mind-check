<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ $schedule->title }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">{{ $schedule->assessment->title }}</p>
    </x-slot>

    @if ($justSubmitted)
        {{-- Berhasil submit --}}
        @php
            $categoryLabel = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'][$submittedResult['category']];
        @endphp
        <div class="mx-auto max-w-xl">
            <x-neu-card class="space-y-4 text-center">
                <x-mood-character :category="$submittedResult['category']" :gender="$this->student->gender" class="mx-auto h-36 w-36" />

                <h3 class="font-display text-xl font-semibold text-slate-800">{{ __('Asesmen selesai!') }}</h3>
                <p class="text-sm text-slate-500">{{ __('Terima kasih telah menyelesaikan asesmen.') }}</p>

                <div class="mt-2 flex flex-col items-center gap-1">
                    <span class="font-display text-4xl font-bold text-primary-700">{{ $submittedResult['total_score'] }}</span>
                    <span class="text-sm text-slate-500">{{ __('Skor Total') }}</span>
                </div>

                <x-category-badge :category="$submittedResult['category']" class="text-sm px-3 py-1.5" />

                <div class="pt-2">
                    <a href="{{ route('dashboard') }}" wire:navigate class="neu-pressable inline-flex items-center gap-1.5 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary-600">
                        {{ __('Kembali ke Dashboard') }}
                    </a>
                </div>
            </x-neu-card>
        </div>
    @elseif ($this->existingResult)
        {{-- Sudah pernah dikerjakan --}}
        <div class="mx-auto max-w-xl">
            <x-neu-card>
                <x-empty-state icon="check-circle" :title="__('Anda sudah mengerjakan asesmen ini')" :description="__('Setiap jadwal hanya bisa dikerjakan satu kali.')" />
                <div class="-mt-4 text-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="neu-pressable inline-flex items-center gap-1.5 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary-600">
                        {{ __('Kembali ke Dashboard') }}
                    </a>
                </div>
            </x-neu-card>
        </div>
    @elseif (! $schedule->isOpenNow())
        {{-- Di luar jendela waktu --}}
        <div class="mx-auto max-w-xl">
            <x-neu-card>
                <x-empty-state
                    icon="calendar"
                    :title="__('Jadwal tidak sedang berlangsung')"
                    :description="__('Asesmen ini hanya bisa dikerjakan pada :start &ndash; :end.', ['start' => $schedule->start_at->translatedFormat('d M Y H:i'), 'end' => $schedule->end_at->translatedFormat('d M Y H:i')])"
                />
                <div class="-mt-4 text-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="neu-pressable inline-flex items-center gap-1.5 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary-600">
                        {{ __('Kembali ke Dashboard') }}
                    </a>
                </div>
            </x-neu-card>
        </div>
    @else
        {{-- Wizard --}}
        @php
            $total = $this->questions->count();
            $answeredCount = count($answers);
            $percent = $total ? round((($currentIndex + 1) / $total) * 100) : 0;
        @endphp
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="w-full">
                <x-neu-card class="space-y-6">
                    <div>
                        <div class="mb-1.5 flex justify-between text-xs font-semibold text-slate-500">
                            <span>{{ __('Soal :current dari :total', ['current' => $currentIndex + 1, 'total' => $total]) }}</span>
                            <span>{{ $percent }}%</span>
                        </div>
                        <div class="h-2.5 w-full overflow-hidden rounded-full neu-inset-sm">
                            <div class="h-full rounded-full bg-primary-600 transition-all duration-300" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    @if ($this->currentQuestion)
                        <div>
                            <p class="font-display text-lg font-semibold leading-snug text-slate-800">{{ $this->currentQuestion->text }}</p>

                            <div class="mt-5 space-y-2.5">
                                @foreach ([0 => 'Tidak Pernah', 1 => 'Hampir Tidak Pernah', 2 => 'Kadang-kadang', 3 => 'Cukup Sering', 4 => 'Sangat Sering'] as $value => $label)
                                    @php $isSelected = ($answers[$this->currentQuestion->id] ?? null) === $value; @endphp
                                    <button
                                        type="button"
                                        wire:click="selectAnswer({{ $this->currentQuestion->id }}, {{ $value }})"
                                        wire:key="option-{{ $this->currentQuestion->id }}-{{ $value }}"
                                        class="neu-pressable flex w-full items-center gap-3 rounded-2xl px-4 py-3.5 text-left text-sm font-semibold transition-colors
                                            {{ $isSelected ? 'bg-primary-600 text-white shadow-neu-sm' : 'neu-raised-sm text-slate-600 hover:text-primary-600' }}"
                                    >
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 {{ $isSelected ? 'border-white' : 'border-slate-300' }}">
                                            @if ($isSelected)
                                                <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                            @endif
                                        </span>
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('answers')
                        <p class="text-sm font-medium text-stress-tinggi">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-between pt-2">
                        <x-secondary-button type="button" wire:click="previous" :disabled="$currentIndex === 0">
                            {{ __('Sebelumnya') }}
                        </x-secondary-button>

                        @if ($this->isLastQuestion)
                            <x-primary-button type="button" wire:click="submit">
                                {{ __('Selesai') }}
                            </x-primary-button>
                        @else
                            <x-primary-button type="button" wire:click="next">
                                {{ __('Selanjutnya') }}
                            </x-primary-button>
                        @endif
                    </div>
                </x-neu-card>
            </div>

            <div class="space-y-5">
                <x-neu-card>
                    <h3 class="font-display text-sm font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('Progres') }}
                    </h3>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-full neu-inset-sm">
                            <span class="font-display text-base font-bold text-primary-700">{{ $answeredCount }}/{{ $total }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ __('Soal terjawab') }}</p>
                            <p class="text-xs text-slate-500">{{ __('Jawaban tersimpan otomatis di setiap langkah.') }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-5 gap-2">
                        @foreach ($this->questions as $index => $question)
                            @php
                                $answered = array_key_exists($question->id, $answers);
                                $isCurrent = $index === $currentIndex;
                            @endphp
                            <span
                                wire:key="nav-{{ $question->id }}"
                                class="flex h-9 items-center justify-center rounded-xl text-xs font-semibold
                                    {{ $isCurrent ? 'bg-primary-600 text-white shadow-neu-sm' : ($answered ? 'bg-primary-100 text-primary-700' : 'neu-inset-sm text-slate-400') }}"
                            >
                                {{ $index + 1 }}
                            </span>
                        @endforeach
                    </div>
                </x-neu-card>

                <x-neu-card class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-sunshine-100 text-sunshine-600">
                        <x-icon.lightbulb class="h-5 w-5" />
                    </span>
                    <p class="text-sm text-slate-500">
                        {{ __('Jawab sesuai perasaanmu selama sebulan terakhir. Tidak ada jawaban benar atau salah.') }}
                    </p>
                </x-neu-card>
            </div>
        </div>
    @endif
</div>
