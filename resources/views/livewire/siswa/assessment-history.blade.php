<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-display text-2xl font-semibold text-slate-800">
                    {{ __('Histori Asesmen') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ __('Rekam jejak semua asesmen stress yang sudah kamu kerjakan.') }}
                </p>
            </div>
            @if (auth()->user()->student)
                <a href="{{ route('siswa.report-pdf', auth()->user()->student) }}" class="neu-pressable inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-neu-sm hover:bg-primary-700">
                    <x-icon.document-text class="h-4 w-4" /> {{ __('Export PDF') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-stat-card icon="clipboard-check" color="mint" :label="__('Total Asesmen Selesai')" :value="$this->summary['total']" />
            <x-stat-card icon="chart-bar" color="primary" :label="__('Skor Rata-rata')" :value="$this->summary['average'] ?? '—'" />

            <div class="neu-card flex items-start gap-4 p-5">
                @if ($this->summary['latestCategory'])
                    <x-mood-character :category="$this->summary['latestCategory']" :gender="$this->student->gender" class="h-12 w-12 shrink-0" />
                @else
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-bubblegum-100 text-bubblegum-600">
                        <x-icon.heart class="h-6 w-6" />
                    </span>
                @endif
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('Kategori Terakhir') }}</p>
                    <div class="mt-1.5">
                        @if ($this->summary['latestCategory'])
                            <x-category-badge :category="$this->summary['latestCategory']" />
                        @else
                            <p class="text-lg text-slate-500">—</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-neu-card padding="p-0">
            <x-table.toolbar placeholder="{{ __('Cari jadwal asesmen...') }}">
            </x-table.toolbar>

            <div class="neu-scrollbar overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-surface-inset">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Jadwal') }}</th>
                            <x-table.th-sort field="completed_at" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Tanggal') }}</x-table.th-sort>
                            <x-table.th-sort field="total_score" :sort-field="$sortField" :sort-direction="$sortDirection">{{ __('Skor') }}</x-table.th-sort>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Kategori') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-inset">
                        @forelse ($this->results as $result)
                            <tr wire:key="result-{{ $result->id }}" class="transition-colors hover:bg-surface">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-slate-700">{{ $result->assessmentSchedule->title }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $result->completed_at->translatedFormat('d M Y H:i') }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $result->total_score }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <x-category-badge :category="$result->category" />
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <a href="{{ route('siswa.result-detail', $result) }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">
                                        {{ __('Lihat Detail') }}
                                        <x-icon.chevron direction="right" class="h-3.5 w-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state icon="clipboard-list" :title="__('Belum ada histori asesmen')" :description="__('Riwayat asesmen kamu akan muncul di sini.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4">
                {{ $this->results->links() }}
            </div>
        </x-neu-card>
    </div>
</div>
