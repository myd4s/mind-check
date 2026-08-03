<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Asesmen
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Hasil</h3>
                        <p class="text-sm text-gray-500">
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

                <p class="text-xs text-gray-400 text-center mt-8">
                    MindCheck adalah alat skrining awal untuk mengenali kecenderungan stres, bukan alat diagnosis klinis.
                    Jika Anda memerlukan bantuan lebih lanjut, silakan hubungi Guru BK di sekolah Anda.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rekomendasi Untuk Anda</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($recommendations as $recommendation)
                        <div class="p-5 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="font-semibold text-gray-900 mb-1">{{ $recommendation->title }}</p>
                            <p class="text-sm text-gray-600">{{ $recommendation->description }}</p>
                        </div>
                    @endforeach

                    <div class="p-5 rounded-xl border border-gray-100 bg-gray-50">
                        <p class="font-semibold text-gray-900 mb-1">Jaga Pola Tidur</p>
                        <p class="text-sm text-gray-600">Tidur yang cukup dan teratur membantu tubuh dan pikiran Anda pulih setiap hari.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('student.dashboard') }}" wire:navigate class="text-center px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('student.result.pdf', $assessment) }}" class="text-center px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                    Unduh PDF
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
