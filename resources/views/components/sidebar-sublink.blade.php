@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'block rounded-xl px-3.5 py-2 text-sm font-medium transition-colors ' . ($active ? 'bg-primary-50 text-primary-600' : 'text-slate-500 hover:bg-surface-card hover:text-primary-600')]) }}
>
    {{ $slot }}
</a>
