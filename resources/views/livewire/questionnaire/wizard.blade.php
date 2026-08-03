<div class="max-w-2xl mx-auto py-6 px-4 sm:py-10">
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
            <span>Pertanyaan {{ $this->currentIndex + 1 }} dari {{ $this->questions->count() }}</span>
            <span wire:loading class="text-indigo-600">Menyimpan...</span>
        </div>
        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-indigo-600 to-cyan-500 transition-all duration-300" style="width: {{ $this->progressPercent }}%"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" wire:key="question-{{ $this->currentQuestion->id }}">
        <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide mb-2">
            Selama seminggu terakhir
        </p>
        <h2 class="text-xl font-semibold text-gray-900 mb-8">{{ $this->currentQuestion->text }}</h2>

        <div class="space-y-3">
            @foreach (\App\Livewire\Questionnaire\Wizard::LIKERT_OPTIONS as $value => $label)
                @php $isSelected = ($this->answers[$this->currentQuestion->id] ?? null) === $value; @endphp
                <button
                    type="button"
                    wire:click="selectAnswer({{ $this->currentQuestion->id }}, {{ $value }})"
                    class="w-full flex items-center justify-between px-5 py-4 rounded-xl border-2 transition text-left {{ $isSelected ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}"
                >
                    <span class="font-medium text-gray-700">{{ $label }}</span>
                    <span class="w-6 h-6 shrink-0 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }}">
                        @if ($isSelected)
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </span>
                </button>
            @endforeach
        </div>

        @error('form')
            <p class="text-sm text-red-600 mt-4">{{ $message }}</p>
        @enderror

        <div class="flex items-center justify-between mt-8">
            <button wire:click="previous" type="button" @disabled($this->currentIndex === 0) class="px-5 py-2.5 rounded-lg text-gray-600 border border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed">
                Sebelumnya
            </button>

            @if ($this->currentIndex === $this->questions->count() - 1)
                <button wire:click="submit" type="button" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                    Selesai &amp; Lihat Hasil
                </button>
            @else
                <button wire:click="next" type="button" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                    Selanjutnya
                </button>
            @endif
        </div>
    </div>

    <div class="flex justify-center flex-wrap gap-1.5 mt-6">
        @foreach ($this->questions as $index => $question)
            <button
                wire:click="goTo({{ $index }})"
                type="button"
                aria-label="Ke pertanyaan {{ $index + 1 }}"
                class="h-2.5 rounded-full transition-all {{ $index === $this->currentIndex ? 'bg-indigo-600 w-6' : (isset($this->answers[$question->id]) ? 'bg-indigo-300 w-2.5' : 'bg-gray-200 w-2.5') }}"
            ></button>
        @endforeach
    </div>
</div>
