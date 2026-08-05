@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-slate-600']) }}>
    {{ $value ?? $slot }}
</label>
