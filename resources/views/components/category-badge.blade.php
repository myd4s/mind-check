@props(['category', 'size' => 'md'])

@php
$labels = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'];
$styles = [
    'rendah' => 'bg-stress-rendah/10 text-stress-rendah',
    'sedang' => 'bg-stress-sedang/10 text-stress-sedang',
    'tinggi' => 'bg-stress-tinggi/10 text-stress-tinggi',
];
$icons = [
    'rendah' => 'check-circle',
    'sedang' => 'exclamation-triangle',
    'tinggi' => 'exclamation-triangle',
];
$style = $styles[$category] ?? 'bg-slate-100 text-slate-600';
$icon = $icons[$category] ?? 'check-circle';

$sizeClasses = [
    'md' => 'gap-1.5 px-2.5 py-1 text-xs',
    'lg' => 'gap-2 px-4 py-1.5 text-xl',
][$size] ?? 'gap-1.5 px-2.5 py-1 text-xs';

$iconSizeClasses = [
    'md' => 'w-3.5 h-3.5',
    'lg' => 'w-5 h-5',
][$size] ?? 'w-3.5 h-3.5';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-semibold whitespace-nowrap $sizeClasses $style"]) }}>
    <x-dynamic-component :component="'icon.' . $icon" class="{{ $iconSizeClasses }} shrink-0" />
    {{ $labels[$category] ?? $category }}
</span>
