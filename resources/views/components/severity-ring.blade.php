@props(['label', 'score', 'severity', 'maxScore' => 42])

@php
    $percent = max(0, min(100, round(($score / $maxScore) * 100)));
    $radius = 42;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference - ($percent / 100) * $circumference;
@endphp

<div class="flex flex-col items-center">
    <div class="relative w-28 h-28">
        <svg class="w-28 h-28 -rotate-90" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="currentColor" stroke-width="10" class="text-gray-100" />
            <circle
                cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round"
                class="{{ $severity->ringColorClass() }} transition-all duration-700 ease-out"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
            />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-2xl font-bold text-gray-900">{{ $score }}</span>
            <span class="text-[10px] text-gray-400">/ {{ $maxScore }}</span>
        </div>
    </div>
    <p class="mt-3 font-medium text-gray-700">{{ $label }}</p>
    <span class="mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $severity->badgeClasses() }}">
        {{ $severity->label() }}
    </span>
</div>
