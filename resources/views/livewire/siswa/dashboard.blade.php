<div class="space-y-5">
    @if ($this->availableCount > 0)
        <div class="neu-card flex flex-col items-start justify-between gap-4 p-5 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3.5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-accent-100 text-accent-600">
                    <x-icon.clipboard-list class="h-5 w-5" />
                </span>
                <p class="text-sm font-semibold text-slate-700">
                    {{ __(':count asesmen menunggu untuk dikerjakan.', ['count' => $this->availableCount]) }}
                </p>
            </div>
            <a href="{{ route('siswa.available-assessments') }}" wire:navigate class="shrink-0">
                <x-primary-button type="button">{{ __('Kerjakan Sekarang') }}</x-primary-button>
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card icon="chart-bar" color="primary" :label="__('Skor Terakhir')" :value="$this->latestResult?->total_score ?? '—'" />

        <div class="neu-card flex items-start gap-4 p-5">
            @if ($this->latestResult)
                <x-mood-character :category="$this->latestResult->category" :gender="$this->student->gender" class="h-14 w-14 shrink-0" />
            @else
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-bubblegum-100 text-bubblegum-600">
                    <x-icon.heart class="h-6 w-6" />
                </span>
            @endif
            <div>
                <p class="text-sm font-medium text-slate-500">{{ __('Kategori Terakhir') }}</p>
                <div class="mt-1.5">
                    @if ($this->latestResult)
                        <x-category-badge :category="$this->latestResult->category" />
                    @else
                        <p class="text-lg text-slate-500">—</p>
                    @endif
                </div>
            </div>
        </div>

        <x-stat-card icon="clipboard-check" color="mint" :label="__('Total Asesmen Selesai')" :value="$this->results->count()" />
    </div>

    <x-neu-card>
        <h3 class="font-display text-base font-semibold text-slate-800">{{ __('Tren Skor Stress') }}</h3>

        @if ($this->results->isEmpty())
            <x-empty-state icon="chart-bar" :title="__('Belum ada data asesmen')" :description="__('Grafik tren skor akan muncul di sini setelah kamu menyelesaikan asesmen pertama.')" />
        @else
            <div
                wire:ignore
                x-data="stressTrendChart(@js($this->chartData))"
                x-init="init()"
                class="mt-4 w-full"
            >
                <div x-ref="chart"></div>
            </div>
        @endif
    </x-neu-card>
</div>

@once
    @push('scripts')
        <script>
            function stressTrendChart(data) {
                return {
                    chart: null,
                    init() {
                        if (this.chart) {
                            return;
                        }

                        this.chart = new ApexCharts(this.$refs.chart, {
                            chart: {
                                type: 'line',
                                height: 300,
                                toolbar: { show: false },
                                fontFamily: 'Nunito, sans-serif',
                            },
                            series: [{ name: 'Skor', data: data.scores }],
                            xaxis: {
                                categories: data.labels,
                                labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                            },
                            yaxis: {
                                min: 0,
                                max: 40,
                                tickAmount: 4,
                                labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                            },
                            stroke: { curve: 'smooth', width: 3, colors: ['#4f46e5'] },
                            markers: { size: 5, colors: ['#4f46e5'], strokeColors: '#fff', strokeWidth: 2 },
                            colors: ['#4f46e5'],
                            grid: { borderColor: '#e4e8f5' },
                            tooltip: { theme: 'light' },
                            annotations: {
                                yaxis: [
                                    { y: 0, y2: 13, fillColor: '#16a34a', opacity: 0.08 },
                                    { y: 14, y2: 26, fillColor: '#d97706', opacity: 0.08 },
                                    { y: 27, y2: 40, fillColor: '#dc2626', opacity: 0.08 },
                                ],
                            },
                        });
                        this.chart.render();
                    },
                };
            }
        </script>
    @endpush
@endonce
