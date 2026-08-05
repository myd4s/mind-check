@props(['icon' => 'pencil', 'color' => 'primary'])

@php
$colorMap = [
    'primary' => 'text-primary-600 hover:bg-primary-50',
    'danger' => 'text-red-600 hover:bg-red-50',
    'secondary' => 'text-secondary-600 hover:bg-secondary-50',
    'slate' => 'text-slate-500 hover:bg-surface',
];
$classes = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => "inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl px-2.5 py-1.5 text-xs font-semibold transition-colors $classes"]) }}>
    <x-dynamic-component :component="'icon.' . $icon" class="h-3.5 w-3.5" />
    {{ $slot }}
</button>
