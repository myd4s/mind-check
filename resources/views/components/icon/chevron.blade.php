@props(['direction' => 'down'])

@php
$rotate = match ($direction) {
    'up' => 'rotate-180',
    'left' => 'rotate-90',
    'right' => '-rotate-90',
    default => '',
};
@endphp

<svg {{ $attributes->merge(['class' => "w-4 h-4 transition-transform $rotate"]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="m6 9 6 6 6-6" />
</svg>
