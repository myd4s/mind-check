<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4 transition-shadow duration-200 hover:shadow-md motion-reduce:transition-none">
                    <span class="shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 mb-1">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $totalStudents }}</p>
                    </div>
                </div>

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4 transition-shadow duration-200 hover:shadow-md motion-reduce:transition-none">
                    <span class="shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 mb-1">Asesmen Selesai</p>
                        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $totalCompleted }}</p>
                    </div>
                </div>

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4 transition-shadow duration-200 hover:shadow-md motion-reduce:transition-none">
                    <span class="shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-rose-50 text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 mb-1">Siswa Perlu Perhatian</p>
                        <p class="text-2xl font-bold text-rose-600 tabular-nums">{{ $concerningStudents }}</p>
                    </div>
                </div>

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4 transition-shadow duration-200 hover:shadow-md motion-reduce:transition-none">
                    <span class="shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 mb-1">Rata-rata Skor Stres</p>
                        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $averageStressScore }}<span class="text-sm text-gray-400 font-normal"> / 42</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Distribusi Tingkat Stres</h3>
                    @if ($totalCompleted > 0)
                        <div class="relative h-56">
                            <canvas id="severityPieChart" role="img" aria-label="Diagram lingkaran distribusi tingkat stres siswa dari hasil asesmen yang selesai"></canvas>
                        </div>
                    @else
                        <div class="h-56 flex flex-col items-center justify-center text-center gap-2 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                            </svg>
                            <p class="text-sm">Belum ada data asesmen</p>
                        </div>
                    @endif
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Jumlah Siswa per Kelas</h3>
                    @if ($studentsPerClass->sum() > 0)
                        <div class="relative h-56">
                            <canvas id="classBarChart" role="img" aria-label="Diagram batang jumlah siswa per kelas"></canvas>
                        </div>
                    @else
                        <div class="h-56 flex flex-col items-center justify-center text-center gap-2 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <p class="text-sm">Belum ada data kelas</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Tren Asesmen Selesai (6 Bulan Terakhir)</h3>
                @if ($monthlyTrend->sum() > 0)
                    <div class="relative h-64">
                        <canvas id="trendLineChart" role="img" aria-label="Diagram garis tren jumlah asesmen selesai dalam 6 bulan terakhir"></canvas>
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center gap-2 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l6-6 4 4 8-8M21 3v6h-6" />
                        </svg>
                        <p class="text-sm">Belum ada tren untuk ditampilkan</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>

                @if ($latestActivities->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center gap-2 py-10 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm">Belum ada aktivitas.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-100">
                                    <th class="py-2 pr-4 font-medium">Siswa</th>
                                    <th class="py-2 pr-4 font-medium">Kelas</th>
                                    <th class="py-2 pr-4 font-medium">Tanggal</th>
                                    <th class="py-2 pr-4 font-medium">Status</th>
                                    <th class="py-2 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestActivities as $assessment)
                                    <tr class="border-b border-gray-50 last:border-0">
                                        <td class="py-3 pr-4 text-gray-700">
                                            <div class="flex items-center gap-3">
                                                <span class="shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold flex items-center justify-center">
                                                    {{ Str::of($assessment->student->user->name)->substr(0, 1)->upper() }}
                                                </span>
                                                <span class="truncate">{{ $assessment->student->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-gray-500 whitespace-nowrap">{{ $assessment->student->schoolClass->name }}</td>
                                        <td class="py-3 pr-4 text-gray-500 whitespace-nowrap">{{ $assessment->completed_at->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assessment->overall_severity->badgeClasses() }}">{{ $assessment->overall_severity->label() }}</span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <a
                                                href="{{ route('assessment.show', $assessment) }}" wire:navigate
                                                class="text-indigo-600 hover:text-indigo-700 font-medium rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                            >
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

    <script>
        (function () {
            function initCharts() {
            const severityLabels = @json($severityLabels->values());
            const severityKeys = @json($severityLabels->keys());
            const severityData = @json($severityDistribution);
            const severityColors = { normal: '#22c55e', mild: '#eab308', moderate: '#f97316', severe: '#ef4444', extremely_severe: '#e11d48' };

            if (document.getElementById('severityPieChart')) {
                new Chart(document.getElementById('severityPieChart'), {
                    type: 'pie',
                    data: {
                        labels: severityLabels,
                        datasets: [{
                            data: severityKeys.map(key => severityData[key] ?? 0),
                            backgroundColor: severityKeys.map(key => severityColors[key]),
                        }],
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
                });
            }

            const classLabels = @json($studentsPerClass->keys());
            const classData = @json($studentsPerClass->values());

            if (document.getElementById('classBarChart')) {
                new Chart(document.getElementById('classBarChart'), {
                    type: 'bar',
                    data: {
                        labels: classLabels,
                        datasets: [{ label: 'Siswa', data: classData, backgroundColor: '#4f46e5', borderRadius: 6 }],
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                });
            }

            const trendLabels = @json($monthlyTrend->keys());
            const trendData = @json($monthlyTrend->values());

            if (document.getElementById('trendLineChart')) {
                new Chart(document.getElementById('trendLineChart'), {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Asesmen Selesai',
                            data: trendData,
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            fill: true,
                            tension: 0.3,
                        }],
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                });
            }
            }

            if (window.Chart) {
                initCharts();
            } else {
                window.addEventListener('chartjs:ready', initCharts, { once: true });
            }
        })();
    </script>
</x-app-layout>
