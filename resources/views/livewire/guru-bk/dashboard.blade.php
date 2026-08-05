<div class="space-y-5">
    <div class="neu-card grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
        <div>
            <x-input-label for="academicYearFilter" :value="__('Tahun Ajaran')" />
            <div class="mt-1.5">
                <x-select wire:model.live="academicYearFilter" id="academicYearFilter">
                    @foreach ($this->academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
        <div>
            <x-input-label for="classFilter" :value="__('Kelas')" />
            <div class="mt-1.5">
                <x-select wire:model.live="classFilter" id="classFilter">
                    <option value="">{{ __('Semua Kelas') }}</option>
                    @foreach ($this->schoolClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card icon="users" color="primary" :label="__('Siswa Sudah Mengerjakan')" :value="$this->latestResultsPerStudent->count()" />
        <x-stat-card icon="exclamation-triangle" color="tinggi" :label="__('Kategori Tinggi')" :value="$this->categoryCounts['tinggi']" />
        <x-stat-card icon="chart-pie" color="sedang" :label="__('Kategori Sedang')" :value="$this->categoryCounts['sedang']" />
    </div>

    <x-neu-card>
        <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Sebaran Kategori Tingkat Stress') }}</h3>

        @if ($this->latestResultsPerStudent->isEmpty())
            <x-empty-state icon="chart-bar" :title="__('Belum ada hasil asesmen')" :description="__('Belum ada hasil asesmen untuk filter yang dipilih.')" />
        @else
            <div
                wire:ignore
                wire:key="category-chart-{{ $academicYearFilter }}-{{ $classFilter }}"
                x-data="categoryDistributionChart(@js($this->categoryCounts))"
                x-init="init()"
                class="mt-4"
            >
                <div x-ref="chart"></div>
            </div>
        @endif
    </x-neu-card>

    <x-neu-card padding="p-0">
        <div class="px-6 py-4">
            <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Siswa Kategori Tinggi Terbaru') }}</h3>
        </div>
        <div class="neu-scrollbar overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-surface-inset">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Siswa') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Kelas') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Skor') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Tanggal') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-inset">
                    @forelse ($this->highCategoryStudents as $result)
                        <tr wire:key="high-{{ $result->id }}">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">{{ $result->student->user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $result->student->currentClassHistory?->schoolClass?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $result->total_score }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $result->completed_at->translatedFormat('d M Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <a href="{{ route('siswa.result-detail', $result) }}" wire:navigate class="font-semibold text-primary-600 hover:text-primary-700">
                                    {{ __('Lihat Detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="check-circle" :title="__('Tidak ada siswa kategori Tinggi')" :description="__('Tidak ada siswa kategori Tinggi untuk filter ini.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-neu-card>
</div>

@once
    @push('scripts')
        <script>
            function categoryDistributionChart(counts) {
                return {
                    chart: null,
                    init() {
                        if (this.chart) {
                            return;
                        }

                        this.chart = new ApexCharts(this.$refs.chart, {
                            chart: {
                                type: 'bar',
                                height: 260,
                                toolbar: { show: false },
                                fontFamily: 'Nunito, sans-serif',
                            },
                            series: [{ name: 'Jumlah Siswa', data: [counts.rendah, counts.sedang, counts.tinggi] }],
                            plotOptions: {
                                bar: { borderRadius: 8, columnWidth: '45%', distributed: true },
                            },
                            xaxis: {
                                categories: ['Rendah', 'Sedang', 'Tinggi'],
                                labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                            },
                            yaxis: {
                                labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                                forceNiceScale: true,
                            },
                            colors: ['#16a34a', '#d97706', '#dc2626'],
                            legend: { show: false },
                            dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
                            grid: { borderColor: '#e4e8f5' },
                            tooltip: { theme: 'light' },
                        });
                        this.chart.render();
                    },
                };
            }
        </script>
    @endpush
@endonce
