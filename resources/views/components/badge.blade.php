@props(['color' => 'primary'])

@php
$colorMap = [
    'primary' => 'bg-primary-100 text-primary-700',
    'secondary' => 'bg-secondary-100 text-secondary-700',
    'accent' => 'bg-accent-100 text-accent-700',
    'mint' => 'bg-mint-100 text-mint-700',
    'sunshine' => 'bg-sunshine-100 text-sunshine-700',
    'bubblegum' => 'bg-bubblegum-100 text-bubblegum-700',
    'gray' => 'bg-slate-100 text-slate-600',
    'success' => 'bg-green-100 text-green-700',
    'warning' => 'bg-amber-100 text-amber-700',
    'danger' => 'bg-red-100 text-red-700',
];
$classes = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap $classes"]) }}>
    {{ $slot }}
</span>
