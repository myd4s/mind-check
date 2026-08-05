@props(['category'])

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
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap $style"]) }}>
    <x-dynamic-component :component="'icon.' . $icon" class="w-3.5 h-3.5 shrink-0" />
    {{ $labels[$category] ?? $category }}
</span>
