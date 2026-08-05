@props(['label', 'icon' => 'home', 'defaultOpen' => false, 'activeSubmenu' => false])

<div
    x-data="{ open: {{ ($defaultOpen || $activeSubmenu) ? 'true' : 'false' }} }"
    class="space-y-1"
>
    <button
        @click="open = !open"
        {{ $attributes->merge(['class' => 'w-full flex items-center justify-between gap-3 rounded-2xl px-3.5 py-3 text-base font-bold transition-colors text-slate-700 hover:bg-surface-card hover:text-primary-600']) }}
    >
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                <x-dynamic-component :component="'icon.' . $icon" class="h-4.5 w-4.5" />
            </span>
            <span class="truncate">{{ $label }}</span>
        </div>

        <span
            x-show="open"
            x-transition:enter="transition-transform duration-200"
            x-transition:leave="transition-transform duration-200"
            class="flex h-5 w-5 shrink-0 items-center justify-center"
        >
            <x-icon.chevron direction="down" class="h-4 w-4" />
        </span>
        <span
            x-show="!open"
            x-transition:enter="transition-transform duration-200"
            x-transition:leave="transition-transform duration-200"
            class="flex h-5 w-5 shrink-0 items-center justify-center"
        >
            <x-icon.chevron direction="right" class="h-4 w-4" />
        </span>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition-all duration-200 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-150 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="space-y-1 pl-2"
    >
        {{ $slot }}
    </div>
</div>
