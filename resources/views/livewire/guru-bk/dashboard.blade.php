<div class="space-y-5">
    <div class="neu-card grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
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
            <x-input-label for="scheduleFilter" :value="__('Jadwal Asesmen')" />
            <div class="mt-1.5">
                <x-select wire:model.live="scheduleFilter" id="scheduleFilter">
                    @forelse ($this->schedules as $schedule)
                        <option value="{{ $schedule->id }}">{{ $schedule->title }}</option>
                    @empty
                        <option value="">{{ __('Belum ada jadwal') }}</option>
                    @endforelse
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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card
            icon="clipboard-check"
            color="mint"
            :label="__('Sudah Mengerjakan')"
            :value="$this->participation['sudah']"
            :hint="__(':done dari :total siswa', ['done' => $this->participation['sudah'], 'total' => $this->participation['total']])"
        />
        <x-stat-card
            icon="clipboard-list"
            color="sunshine"
            :label="__('Belum Mengerjakan')"
            :value="$this->participation['belum']"
            :hint="__('Untuk jadwal asesmen terpilih')"
        />
        <x-stat-card icon="exclamation-triangle" color="tinggi" :label="__('Kategori Tinggi')" :value="$this->categoryCounts['tinggi']" />
        <x-stat-card icon="chart-pie" color="sedang" :label="__('Kategori Sedang')" :value="$this->categoryCounts['sedang']" />
    </div>

    <div class="flex justify-end">
        <a
            href="{{ route('guru-bk.assessment-participation', ['scheduleFilter' => $scheduleFilter, 'classFilter' => $classFilter, 'academicYearFilter' => $academicYearFilter]) }}"
            wire:navigate
            class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700"
        >
            {{ __('Lihat rincian siswa sudah/belum mengerjakan') }}
            <x-icon.chevron class="h-3.5 w-3.5 -rotate-90" />
        </a>
    </div>

    <x-neu-card>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Sebaran Kategori Tingkat Stress') }}</h3>
            <span class="text-xs font-medium text-slate-500">{{ __('Hasil terbaru per siswa') }}</span>
        </div>

        @if ($this->latestResultsPerStudent->isEmpty())
            <x-empty-state icon="chart-bar" :title="__('Belum ada hasil asesmen')" :description="__('Belum ada hasil asesmen untuk filter yang dipilih.')" />
        @else
            @php
                $catTotal = $this->latestResultsPerStudent->count();
                $catRows = [
                    ['key' => 'rendah', 'label' => __('Rendah'), 'class' => 'text-stress-rendah', 'dot' => 'bg-stress-rendah'],
                    ['key' => 'sedang', 'label' => __('Sedang'), 'class' => 'text-stress-sedang', 'dot' => 'bg-stress-sedang'],
                    ['key' => 'tinggi', 'label' => __('Tinggi'), 'class' => 'text-stress-tinggi', 'dot' => 'bg-stress-tinggi'],
                ];
            @endphp

            <div class="mt-4 grid gap-6 lg:grid-cols-5">
                <div class="lg:col-span-3">
                    <div
                        wire:ignore
                        wire:key="category-chart-{{ $academicYearFilter }}-{{ $classFilter }}-{{ $catTotal }}"
                        x-data="categoryDistributionChart(@js($this->categoryCounts))"
                        x-init="init()"
                    >
                        <div x-ref="chart"></div>
                    </div>
                </div>

                <div class="flex flex-col justify-center gap-3 lg:col-span-2">
                    @foreach ($catRows as $row)
                        @php
                            $count = $this->categoryCounts[$row['key']];
                            $pct = $catTotal > 0 ? round($count / $catTotal * 100) : 0;
                        @endphp
                        <div class="neu-inset-sm rounded-2xl px-4 py-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 font-medium text-slate-600">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $row['dot'] }}"></span>
                                    {{ $row['label'] }}
                                </span>
                                <span class="font-display font-semibold {{ $row['class'] }}">{{ $count }} <span class="text-xs text-slate-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-surface-inset">
                                <div class="h-full rounded-full {{ $row['dot'] }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
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

                        const data = [counts.rendah, counts.sedang, counts.tinggi];
                        const total = data.reduce((sum, n) => sum + n, 0);

                        this.chart = new ApexCharts(this.$refs.chart, {
                            chart: {
                                type: 'donut',
                                height: 300,
                                fontFamily: 'Nunito, sans-serif',
                            },
                            series: data,
                            labels: ['Rendah', 'Sedang', 'Tinggi'],
                            colors: ['#16a34a', '#d97706', '#dc2626'],
                            stroke: { width: 3, colors: ['#eef1f8'] },
                            legend: { show: false },
                            dataLabels: {
                                enabled: true,
                                formatter: (val) => (val >= 8 ? Math.round(val) + '%' : ''),
                                style: { fontSize: '12px', fontWeight: 700, colors: ['#fff'] },
                                dropShadow: { enabled: false },
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%',
                                        labels: {
                                            show: true,
                                            name: { fontSize: '13px', color: '#64748b' },
                                            value: { fontSize: '26px', fontWeight: 700, color: '#1e293b' },
                                            total: {
                                                show: true,
                                                label: 'Total Siswa',
                                                color: '#64748b',
                                                fontSize: '13px',
                                                formatter: () => total,
                                            },
                                        },
                                    },
                                },
                            },
                            tooltip: {
                                theme: 'light',
                                y: { formatter: (val) => val + ' siswa' },
                            },
                            responsive: [{ breakpoint: 640, options: { chart: { height: 260 } } }],
                        });
                        this.chart.render();
                    },
                };
            }
        </script>
    @endpush
@endonce
