@props(['disabled' => false, 'full' => true])

<div class="relative {{ $full ? 'w-full' : 'inline-block' }}">
    <select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full appearance-none neu-inset-sm rounded-2xl border-0 pl-4 pr-9 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-400 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
        {{ $slot }}
    </select>
    <x-icon.chevron class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500" />
</div>
