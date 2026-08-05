<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Hasil Asesmen') }}
            </h2>
            <a href="{{ route('siswa.report-pdf', $result->student) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none transition ease-in-out duration-150">
                {{ __('Export PDF') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    @if (auth()->user()->hasRoleAtLeast(\App\Enums\UserRole::GuruBk))
                        <div>
                            <p class="text-gray-500">{{ __('Siswa') }}</p>
                            <p class="font-medium text-gray-900">{{ $result->student->user->name }} ({{ $result->student->nisn }})</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-500">{{ __('Jadwal') }}</p>
                        <p class="font-medium text-gray-900">{{ $result->assessmentSchedule->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">{{ __('Assessment') }}</p>
                        <p class="font-medium text-gray-900">{{ $result->assessmentSchedule->assessment->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">{{ __('Tanggal Selesai') }}</p>
                        <p class="font-medium text-gray-900">{{ $result->completed_at->translatedFormat('d M Y H:i') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-6 border-t border-gray-100 pt-6">
                    <div>
                        <p class="text-3xl font-bold text-primary-700">{{ $result->total_score }}</p>
                        <p class="text-xs text-gray-500">{{ __('Skor Total (0-40)') }}</p>
                    </div>
                    <x-category-badge :category="$result->category" class="text-sm px-3 py-1" />
                </div>
            </div>

            @if ($this->canManageNote || $result->note)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-900">{{ __('Catatan Guru BK') }}</h3>
                        @if ($this->canManageNote && ! $editingNote)
                            <button type="button" wire:click="editNote" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                {{ $result->note ? __('Edit') : __('Tambah Catatan') }}
                            </button>
                        @endif
                    </div>

                    @if ($editingNote)
                        <form wire:submit="saveNote" class="space-y-3">
                            <textarea wire:model="noteContent" rows="4" class="block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm" placeholder="{{ __('Tulis catatan atau masukan untuk siswa ini...') }}"></textarea>
                            <x-input-error :messages="$errors->get('noteContent')" />
                            <div class="flex justify-end gap-3">
                                <x-secondary-button type="button" wire:click="cancelEditNote">{{ __('Batal') }}</x-secondary-button>
                                <x-primary-button type="submit">{{ __('Simpan') }}</x-primary-button>
                            </div>
                        </form>
                    @elseif ($result->note)
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $result->note->content }}</p>
                        <p class="text-xs text-gray-500 mt-3">
                            {{ __('Ditulis oleh :name pada :date', ['name' => $result->note->guruBk->name, 'date' => $result->note->updated_at->translatedFormat('d M Y H:i')]) }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500">{{ __('Belum ada catatan.') }}</p>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-medium text-gray-900">{{ __('Rincian Jawaban') }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pertanyaan') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jawaban') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $answerLabels = [0 => 'Tidak Pernah', 1 => 'Hampir Tidak Pernah', 2 => 'Kadang-kadang', 3 => 'Cukup Sering', 4 => 'Sangat Sering'];
                        @endphp
                        @foreach ($this->answers as $answer)
                            <tr wire:key="answer-{{ $answer->id }}">
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $answer->question->order }}</td>
                                <td class="px-6 py-3 text-sm text-gray-800">{{ $answer->question->text }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $answerLabels[$answer->answer_value] ?? $answer->answer_value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
