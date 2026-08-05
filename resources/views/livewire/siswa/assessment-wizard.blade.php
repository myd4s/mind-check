<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $schedule->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if ($justSubmitted)
                {{-- Berhasil submit --}}
                @php
                    $categoryLabel = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'][$submittedResult['category']];
                    $categoryColor = ['rendah' => '#16a34a', 'sedang' => '#d97706', 'tinggi' => '#dc2626'][$submittedResult['category']];
                @endphp
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Asesmen selesai!') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Terima kasih telah menyelesaikan asesmen.') }}</p>

                    <div class="flex flex-col items-center gap-1 mt-4">
                        <span class="text-4xl font-bold text-primary-700">{{ $submittedResult['total_score'] }}</span>
                        <span class="text-sm text-gray-500">{{ __('Skor Total') }}</span>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: {{ $categoryColor }};">
                        {{ __('Kategori') }}: {{ $categoryLabel }}
                    </span>

                    <div class="pt-4">
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            {{ __('Kembali ke Dashboard') }}
                        </a>
                    </div>
                </div>
            @elseif ($this->existingResult)
                {{-- Sudah pernah dikerjakan --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center space-y-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Anda sudah mengerjakan asesmen ini') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Setiap jadwal hanya bisa dikerjakan satu kali.') }}</p>
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-block mt-4 text-primary-600 hover:text-primary-800 text-sm font-medium">
                        {{ __('Kembali ke Dashboard') }}
                    </a>
                </div>
            @elseif (! $schedule->isOpenNow())
                {{-- Di luar jendela waktu --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center space-y-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Jadwal tidak sedang berlangsung') }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ __('Asesmen ini hanya bisa dikerjakan pada') }}
                        {{ $schedule->start_at->translatedFormat('d M Y H:i') }} &ndash; {{ $schedule->end_at->translatedFormat('d M Y H:i') }}.
                    </p>
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-block mt-4 text-primary-600 hover:text-primary-800 text-sm font-medium">
                        {{ __('Kembali ke Dashboard') }}
                    </a>
                </div>
            @else
                {{-- Wizard --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>{{ __('Soal :current dari :total', ['current' => $currentIndex + 1, 'total' => $this->questions->count()]) }}</span>
                            <span>{{ round((($currentIndex + 1) / $this->questions->count()) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all" style="width: {{ (($currentIndex + 1) / $this->questions->count()) * 100 }}%"></div>
                        </div>
                    </div>

                    @if ($this->currentQuestion)
                        <div>
                            <p class="text-base text-gray-900 font-medium">{{ $this->currentQuestion->text }}</p>

                            <div class="mt-6 space-y-2">
                                @foreach ([0 => 'Tidak Pernah', 1 => 'Hampir Tidak Pernah', 2 => 'Kadang-kadang', 3 => 'Cukup Sering', 4 => 'Sangat Sering'] as $value => $label)
                                    <button
                                        type="button"
                                        wire:click="selectAnswer({{ $this->currentQuestion->id }}, {{ $value }})"
                                        wire:key="option-{{ $this->currentQuestion->id }}-{{ $value }}"
                                        class="w-full text-left px-4 py-3 rounded-md border transition
                                            {{ ($answers[$this->currentQuestion->id] ?? null) === $value
                                                ? 'border-primary-500 bg-primary-50 text-primary-800'
                                                : 'border-gray-200 hover:border-gray-300 text-gray-700' }}"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('answers')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-between pt-4">
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
                </div>
            @endif
        </div>
    </div>
</div>
