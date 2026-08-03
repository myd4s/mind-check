@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-indigo-700 bg-indigo-50 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
