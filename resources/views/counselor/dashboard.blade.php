<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Guru BK
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Belum Ditangani</p>
                    <p class="text-2xl font-bold text-red-600">{{ $belumDitangani }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Sedang Ditangani</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $sedangDitangani }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-2">Selesai Ditangani</p>
                    <p class="text-2xl font-bold text-green-600">{{ $selesai }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Siswa Perlu Perhatian</h3>
                <p class="text-sm text-gray-500 mb-4">Berdasarkan hasil asesmen terakhir dengan tingkat Parah atau Sangat Parah.</p>

                @if ($needsAttention->isEmpty())
                    <p class="text-sm text-gray-500">Tidak ada siswa yang perlu perhatian khusus saat ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-100">
                                    <th class="py-2 pr-4 font-medium">Siswa</th>
                                    <th class="py-2 pr-4 font-medium">Kelas</th>
                                    <th class="py-2 pr-4 font-medium">Tanggal</th>
                                    <th class="py-2 pr-4 font-medium">Status Hasil</th>
                                    <th class="py-2 pr-4 font-medium">Tindak Lanjut</th>
                                    <th class="py-2 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($needsAttention as $assessment)
                                    <tr class="border-b border-gray-50 last:border-0">
                                        <td class="py-3 pr-4 text-gray-700">{{ $assessment->student->user->name }}</td>
                                        <td class="py-3 pr-4 text-gray-500">{{ $assessment->student->schoolClass->name }}</td>
                                        <td class="py-3 pr-4 text-gray-500">{{ $assessment->completed_at->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->overall_severity->badgeClasses() }}">{{ $assessment->overall_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->currentFollowUpStatus->badgeClasses() }}">{{ $assessment->currentFollowUpStatus->label() }}</span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('assessment.show', $assessment) }}" wire:navigate class="text-indigo-600 hover:text-indigo-700 font-medium">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
