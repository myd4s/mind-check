@props(['category' => 'rendah', 'gender' => null])

@php
$genderValue = $gender instanceof \App\Enums\Gender ? $gender->value : $gender;
$genderFolder = $genderValue === 'L' ? 'laki' : 'perempuan';
$categoryKey = in_array($category, ['rendah', 'sedang', 'tinggi'], true) ? $category : 'rendah';
$src = asset("images/mood/{$categoryKey}-{$genderFolder}.png");
@endphp

<img
    src="{{ $src }}"
    alt="Karakter ekspresi tingkat stress {{ $categoryKey }}"
    loading="lazy"
    {{ $attributes->merge(['class' => 'object-contain']) }}
/>
