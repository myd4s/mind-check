<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Hasil Asesmen
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $assessment->student->user->name }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $assessment->student->schoolClass->name }} &middot; NIS {{ $assessment->student->nis }}
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Diselesaikan pada {{ $assessment->completed_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    <span class="self-start text-xs font-semibold px-3 py-1 rounded-full {{ $assessment->overall_severity->badgeClasses() }}">
                        Status Keseluruhan: {{ $assessment->overall_severity->label() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 justify-items-center">
                    <x-severity-ring label="Depresi" :score="$assessment->depression_score" :severity="$assessment->depression_severity" />
                    <x-severity-ring label="Kecemasan" :score="$assessment->anxiety_score" :severity="$assessment->anxiety_severity" />
                    <x-severity-ring label="Stres" :score="$assessment->stress_score" :severity="$assessment->stress_severity" />
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rekomendasi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($recommendations as $recommendation)
                        <div class="p-5 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="font-semibold text-gray-900 mb-1">{{ $recommendation->title }}</p>
                            <p class="text-sm text-gray-600">{{ $recommendation->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tindak Lanjut</h3>

                <form method="POST" action="{{ route('assessment.follow-up.store', $assessment) }}" class="space-y-4 mb-8">
                    @csrf
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (\App\Enums\FollowUpStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Catatan" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan catatan tindak lanjut (opsional)"></textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <x-primary-button>
                        Simpan Catatan
                    </x-primary-button>
                </form>

                <h4 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Catatan</h4>

                @if ($assessment->followUps->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada catatan tindak lanjut.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($assessment->followUps->sortByDesc('id') as $followUp)
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $followUp->status->badgeClasses() }}">{{ $followUp->status->label() }}</span>
                                    <span class="text-xs text-gray-400">{{ $followUp->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-1">oleh {{ $followUp->counselor->name }}</p>
                                @if ($followUp->notes)
                                    <p class="text-sm text-gray-700">{{ $followUp->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('assessment.pdf', $assessment) }}" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                    Unduh PDF
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
