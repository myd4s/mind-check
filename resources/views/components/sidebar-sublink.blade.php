@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'flex items-center gap-2.5 rounded-xl px-3.5 py-2 pl-8 text-xs font-medium transition-all ' . ($active ? 'neu-inset-sm text-primary-700 font-semibold' : 'text-slate-500 hover:bg-surface-card hover:text-slate-700')]) }}
>
    @if ($icon)
        <span class="flex h-5 w-5 shrink-0 items-center justify-center">
            <x-dynamic-component :component="'icon.' . $icon" class="h-3.5 w-3.5" />
        </span>
    @else
        <span class="flex h-5 w-5 shrink-0 items-center justify-center">
            <span class="h-0.5 w-0.5 rounded-full bg-current"></span>
        </span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
