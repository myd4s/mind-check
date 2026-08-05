@props(['field', 'sortField' => null, 'sortDirection' => 'asc', 'align' => 'left'])

@php
$isActive = $sortField === $field;
$alignClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
$justifyClass = $align === 'right' ? 'justify-end' : ($align === 'center' ? 'justify-center' : 'justify-start');
@endphp

<th {{ $attributes->merge(['class' => "px-5 py-3 $alignClass text-xs font-semibold text-slate-500 uppercase tracking-wider select-none"]) }}>
    <button type="button" wire:click="sortBy('{{ $field }}')" class="inline-flex w-full items-center gap-1.5 {{ $justifyClass }} cursor-pointer transition-colors hover:text-primary-600">
        <span>{{ $slot }}</span>
        <span class="{{ $isActive ? 'text-primary-600' : 'text-slate-500' }}">
            @if ($isActive && $sortDirection === 'desc')
                <x-icon.chevron direction="up" class="w-3.5 h-3.5" />
            @elseif ($isActive)
                <x-icon.chevron direction="down" class="w-3.5 h-3.5" />
            @else
                <x-icon.sort />
            @endif
        </span>
    </button>
</th>
