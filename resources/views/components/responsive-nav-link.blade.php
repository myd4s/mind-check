@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-2 rounded-lg text-start text-base font-semibold text-indigo-700 bg-indigo-50 transition duration-150 ease-in-out'
            : 'block w-full px-4 py-2 rounded-lg text-start text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
