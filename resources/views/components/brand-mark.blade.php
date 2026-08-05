@props(['showText' => true, 'size' => 'md'])

@php
$boxSize = match ($size) {
    'lg' => 'h-12 w-12',
    'sm' => 'h-7 w-7',
    default => 'h-9 w-9',
};
$iconSize = match ($size) {
    'lg' => 'h-6 w-6',
    'sm' => 'h-4 w-4',
    default => 'h-5 w-5',
};
$textSize = match ($size) {
    'lg' => 'text-2xl',
    'sm' => 'text-sm',
    default => 'text-lg',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span class="flex {{ $boxSize }} shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-neu-sm">
        <svg viewBox="0 0 24 24" class="{{ $iconSize }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 12h3.5l1.8-4.5L11 17l2.2-9.5L14.7 12H21" />
        </svg>
    </span>
    @if ($showText)
        <span class="font-display {{ $textSize }} font-semibold tracking-tight text-slate-800">Mind<span class="text-primary-600">Check</span></span>
    @endif
</span>
