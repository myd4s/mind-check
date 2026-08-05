@props(['icon' => 'clipboard-list', 'title' => 'Belum ada data', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-14 px-4']) }}>
    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-50 text-primary-400 mb-4">
        <x-dynamic-component :component="'icon.' . $icon" class="w-7 h-7" />
    </div>
    <p class="font-display font-semibold text-slate-600">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-sm text-slate-500 max-w-sm">{{ $description }}</p>
    @endif
</div>
