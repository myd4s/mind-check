@props(['placeholder' => 'Cari...', 'showPerPage' => true])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-surface-inset']) }}>
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
        <div class="relative sm:w-64">
            <x-icon.search class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" />
            <input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="{{ $placeholder }}"
                class="w-full neu-inset-sm rounded-2xl border-0 pl-10 pr-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-400"
            />
        </div>

        {{ $filters ?? '' }}
    </div>

    <div class="flex items-center gap-3">
        @if ($showPerPage)
            <label class="flex items-center gap-2 text-xs font-medium text-slate-500 whitespace-nowrap">
                {{ __('Per halaman') }}
                <x-select wire:model.live="perPage" :full="false" class="!w-auto pr-8">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-select>
            </label>
        @endif

        {{ $actions ?? '' }}
    </div>
</div>
