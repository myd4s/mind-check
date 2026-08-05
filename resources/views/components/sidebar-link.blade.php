@props(['href', 'icon' => 'home', 'active' => false])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-2xl px-3.5 py-3 text-base font-bold transition-all ' . ($active ? 'neu-inset-sm text-primary-700' : 'text-slate-700 hover:bg-surface-card hover:text-primary-600')]) }}
>
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600 transition-colors">
        <x-dynamic-component :component="'icon.' . $icon" class="h-4.5 w-4.5" />
    </span>
    <span class="truncate">{{ $slot }}</span>
</a>
