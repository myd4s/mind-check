@props(['icon' => 'chart-bar', 'label', 'value', 'color' => 'primary', 'hint' => null])

@php
$colorMap = [
    'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600'],
    'secondary' => ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-600'],
    'accent' => ['bg' => 'bg-accent-100', 'text' => 'text-accent-600'],
    'mint' => ['bg' => 'bg-mint-100', 'text' => 'text-mint-600'],
    'sunshine' => ['bg' => 'bg-sunshine-100', 'text' => 'text-sunshine-600'],
    'bubblegum' => ['bg' => 'bg-bubblegum-100', 'text' => 'text-bubblegum-600'],
    'rendah' => ['bg' => 'bg-stress-rendah/10', 'text' => 'text-stress-rendah'],
    'sedang' => ['bg' => 'bg-stress-sedang/10', 'text' => 'text-stress-sedang'],
    'tinggi' => ['bg' => 'bg-stress-tinggi/10', 'text' => 'text-stress-tinggi'],
];
$c = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'neu-card p-5 flex items-start gap-4']) }}>
    <div class="shrink-0 flex items-center justify-center w-12 h-12 rounded-2xl {{ $c['bg'] }} {{ $c['text'] }}">
        <x-dynamic-component :component="'icon.' . $icon" class="w-6 h-6" />
    </div>
    <div class="min-w-0">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <p class="mt-1 text-2xl font-display font-semibold text-slate-800 truncate">{{ $value }}</p>
        @if ($hint)
            <p class="mt-0.5 text-xs text-slate-500">{{ $hint }}</p>
        @endif
    </div>
</div>
