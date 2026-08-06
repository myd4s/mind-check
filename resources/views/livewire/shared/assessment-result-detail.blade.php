<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-semibold text-slate-800">
                {{ __('Detail Hasil Asesmen') }}
            </h2>
            <a href="{{ route('siswa.report-pdf', $result->student) }}" class="neu-pressable inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-neu-sm hover:bg-primary-700">
                <x-icon.document-text class="h-4 w-4" /> {{ __('Export PDF') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-5">
        <x-neu-card class="space-y-6">
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-[auto_1fr]">
                <div class="flex flex-col items-center gap-3 text-center">
                    <x-mood-character :category="$result->category" :gender="$result->student->gender" class="h-40 w-40 shrink-0" />
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <span class="font-display text-5xl font-bold text-primary-700">{{ $result->total_score }}</span>
                        <x-category-badge :category="$result->category" size="lg" />
                    </div>
                </div>

                <div class="border-t border-surface-inset pt-6 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8">
                    <h3 class="font-display text-2xl font-semibold text-slate-800">{{ __('Apa artinya skor ini?') }}</h3>
                    <p class="mt-3 text-base leading-relaxed text-slate-600">{{ $this->categoryDescription }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-surface-inset pt-6 text-sm sm:flex-row">
                @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::GuruBk))
                    <div class="sm:flex-1">
                        <p class="text-slate-500">{{ __('Siswa') }}</p>
                        <p class="mt-0.5 font-semibold text-slate-800">{{ $result->student->user->name }} ({{ $result->student->nisn }})</p>
                    </div>
                @endif
                <div class="sm:flex-1">
                    <p class="text-slate-500">{{ __('Jadwal') }}</p>
                    <p class="mt-0.5 font-semibold text-slate-800">{{ $result->assessmentSchedule->title }}</p>
                </div>
                <div class="sm:flex-1">
                    <p class="text-slate-500">{{ __('Assessment') }}</p>
                    <p class="mt-0.5 font-semibold text-slate-800">{{ $result->assessmentSchedule->assessment->title }}</p>
                </div>
                <div class="sm:flex-1">
                    <p class="text-slate-500">{{ __('Tanggal Selesai') }}</p>
                    <p class="mt-0.5 font-semibold text-slate-800">{{ $result->completed_at->translatedFormat('d M Y H:i') }}</p>
                </div>
            </div>
        </x-neu-card>

        @if ($this->canManageNote || $result->note)
            <x-neu-card>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Catatan Guru BK') }}</h3>
                    @if ($this->canManageNote && ! $editingNote)
                        <button type="button" wire:click="editNote" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                            {{ $result->note ? __('Edit') : __('Tambah Catatan') }}
                        </button>
                    @endif
                </div>

                @if ($editingNote)
                    <form wire:submit="saveNote" class="space-y-3">
                        <x-textarea wire:model="noteContent" rows="4" placeholder="{{ __('Tulis catatan atau masukan untuk siswa ini...') }}" />
                        <x-input-error :messages="$errors->get('noteContent')" />
                        <div class="flex justify-end gap-3">
                            <x-secondary-button type="button" wire:click="cancelEditNote">{{ __('Batal') }}</x-secondary-button>
                            <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>
                @elseif ($result->note)
                    <p class="whitespace-pre-line text-sm text-slate-600">{{ $result->note->content }}</p>
                    <p class="mt-3 text-xs text-slate-500">
                        {{ __('Ditulis oleh :name pada :date', ['name' => $result->note->guruBk->name, 'date' => $result->note->updated_at->translatedFormat('d M Y H:i')]) }}
                    </p>
                @else
                    <p class="text-sm text-slate-500">{{ __('Belum ada catatan.') }}</p>
                @endif
            </x-neu-card>
        @endif

        <x-neu-card padding="p-0">
            <div class="px-6 py-4">
                <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Rincian Jawaban') }}</h3>
            </div>
            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <th class="w-12 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Pertanyaan') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jawaban') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @php
                            $answerLabels = [0 => 'Tidak Pernah', 1 => 'Hampir Tidak Pernah', 2 => 'Kadang-kadang', 3 => 'Cukup Sering', 4 => 'Sangat Sering'];
                        @endphp
                        @foreach ($this->answers as $answer)
                            <tr wire:key="answer-{{ $answer->id }}">
                                <td class="px-6 py-3 text-sm text-slate-500">{{ $answer->question->order }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700">{{ $answer->question->text }}</td>
                                <td class="px-6 py-3 text-sm text-slate-500">{{ $answerLabels[$answer->answer_value] ?? $answer->answer_value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-neu-card>
    </div>
</div>
