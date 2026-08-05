@props(['variant' => 'raised', 'padding' => 'p-6'])

@php
$variantClasses = match ($variant) {
    'flat' => 'bg-surface-card rounded-3xl',
    'inset' => 'neu-inset rounded-3xl',
    default => 'neu-card',
};
@endphp

<div {{ $attributes->merge(['class' => "$variantClasses $padding"]) }}>
    {{ $slot }}
</div>
