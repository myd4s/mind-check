<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Siswa
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Status Terkini</p>
                    @if ($latestCompleted)
                        <span class="inline-block text-sm font-semibold px-3 py-1 rounded-full {{ $latestCompleted->overall_severity->badgeClasses() }}">
                            {{ $latestCompleted->overall_severity->label() }}
                        </span>
                    @else
                        <p class="text-lg font-semibold text-gray-400">Belum Ada Data</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Progres Kuesioner</p>
                    @if ($inProgress)
                        <p class="text-2xl font-bold text-gray-900">{{ $inProgressPercent }}%</p>
                        <p class="text-xs text-gray-400 mt-1">Sedang berjalan</p>
                    @else
                        <p class="text-2xl font-bold text-gray-400">-</p>
                        <p class="text-xs text-gray-400 mt-1">Tidak ada kuesioner berjalan</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Skor Stres Terakhir</p>
                    @if ($latestCompleted)
                        <p class="text-2xl font-bold text-gray-900">{{ $latestCompleted->stress_score }}<span class="text-sm text-gray-400 font-normal"> / 42</span></p>
                    @else
                        <p class="text-2xl font-bold text-gray-400">-</p>
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-cyan-500 rounded-2xl p-6 sm:p-8 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold">
                        {{ $inProgress ? 'Lanjutkan kuesioner yang belum selesai' : 'Sudah waktunya cek kondisimu?' }}
                    </h3>
                    <p class="text-white/80 text-sm mt-1">
                        {{ $inProgress ? 'Kamu sudah menjawab '.$inProgressPercent.'% dari kuesioner.' : 'Isi kuesioner singkat untuk mengetahui tingkat stresmu saat ini.' }}
                    </p>
                </div>
                <a href="{{ route('student.questionnaire') }}" wire:navigate class="shrink-0 text-center px-5 py-2.5 rounded-lg bg-white text-indigo-600 font-medium hover:bg-gray-50">
                    {{ $inProgress ? 'Lanjutkan Kuesioner' : 'Mulai Asesmen' }}
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Asesmen</h3>

                @if ($recentAssessments->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada asesmen yang diselesaikan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-100">
                                    <th class="py-2 pr-4 font-medium">Tanggal</th>
                                    <th class="py-2 pr-4 font-medium">Depresi</th>
                                    <th class="py-2 pr-4 font-medium">Kecemasan</th>
                                    <th class="py-2 pr-4 font-medium">Stres</th>
                                    <th class="py-2 pr-4 font-medium">Status</th>
                                    <th class="py-2 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAssessments as $assessment)
                                    <tr class="border-b border-gray-50 last:border-0">
                                        <td class="py-3 pr-4 text-gray-700">{{ $assessment->completed_at->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->depression_severity->badgeClasses() }}">{{ $assessment->depression_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->anxiety_severity->badgeClasses() }}">{{ $assessment->anxiety_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->stress_severity->badgeClasses() }}">{{ $assessment->stress_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->overall_severity->badgeClasses() }}">{{ $assessment->overall_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('student.result', $assessment) }}" wire:navigate class="text-indigo-600 hover:text-indigo-700 font-medium">
                                                Lihat Hasil
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tips Menjaga Kesehatan Mental</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="font-semibold text-gray-900 mb-1">Tarik Napas Dalam</p>
                        <p class="text-sm text-gray-600">Luangkan 2-3 menit untuk bernapas perlahan saat merasa tertekan.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="font-semibold text-gray-900 mb-1">Istirahat Cukup</p>
                        <p class="text-sm text-gray-600">Tidur yang teratur membantu menjaga suasana hati dan fokus belajar.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="font-semibold text-gray-900 mb-1">Bicara dengan Teman</p>
                        <p class="text-sm text-gray-600">Berbagi cerita dengan orang yang dipercaya dapat meringankan beban pikiran.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="font-semibold text-gray-900 mb-1">Kelola Waktu Belajar</p>
                        <p class="text-sm text-gray-600">Buat jadwal realistis dan sisipkan waktu istirahat di antara belajar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
