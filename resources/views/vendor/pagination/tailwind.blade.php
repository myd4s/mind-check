@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 rounded-2xl neu-inset-sm px-4 py-2 text-sm font-medium text-slate-300 cursor-not-allowed">
                    <x-icon.chevron direction="left" class="w-4 h-4" /> {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="neu-pressable inline-flex items-center gap-1 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-medium text-slate-600 hover:text-primary-600">
                    <x-icon.chevron direction="left" class="w-4 h-4" /> {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="neu-pressable inline-flex items-center gap-1 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-medium text-slate-600 hover:text-primary-600">
                    {!! __('pagination.next') !!} <x-icon.chevron direction="right" class="w-4 h-4" />
                </a>
            @else
                <span class="inline-flex items-center gap-1 rounded-2xl neu-inset-sm px-4 py-2 text-sm font-medium text-slate-300 cursor-not-allowed">
                    {!! __('pagination.next') !!} <x-icon.chevron direction="right" class="w-4 h-4" />
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-slate-500">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rtl:flex-row-reverse">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl neu-inset-sm text-slate-300 cursor-not-allowed" aria-hidden="true">
                                <x-icon.chevron direction="left" class="w-4 h-4" />
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="neu-pressable flex h-9 w-9 items-center justify-center rounded-xl neu-raised-sm text-slate-500 hover:text-primary-600" aria-label="{{ __('pagination.previous') }}">
                            <x-icon.chevron direction="left" class="w-4 h-4" />
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="flex h-9 w-9 items-center justify-center text-sm font-medium text-slate-500">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl neu-inset-sm text-sm font-semibold text-primary-600">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="neu-pressable flex h-9 w-9 items-center justify-center rounded-xl text-sm font-medium text-slate-500 hover:bg-surface-card hover:text-primary-600" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="neu-pressable flex h-9 w-9 items-center justify-center rounded-xl neu-raised-sm text-slate-500 hover:text-primary-600" aria-label="{{ __('pagination.next') }}">
                            <x-icon.chevron direction="right" class="w-4 h-4" />
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl neu-inset-sm text-slate-300 cursor-not-allowed" aria-hidden="true">
                                <x-icon.chevron direction="right" class="w-4 h-4" />
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
