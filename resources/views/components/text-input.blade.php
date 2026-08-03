@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition disabled:bg-gray-50 disabled:text-gray-500']) }}>
