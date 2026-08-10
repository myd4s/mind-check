<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MindCare') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:400,500,600,700|nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-700 antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-surface px-4 py-10">
            {{-- Blob dekoratif — SVG statis, tanpa gradient --}}
            <svg class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 text-primary-100" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <path d="M45.9,-58.6C58.9,-49.5,68.2,-34.3,71.9,-17.9C75.6,-1.6,73.7,15.9,65.8,30.2C57.9,44.5,44,55.6,28.5,62.6C13,69.6,-4.1,72.5,-20.6,68.9C-37.1,65.3,-53,55.2,-63.1,40.7C-73.2,26.2,-77.5,7.3,-74.3,-9.9C-71.1,-27.1,-60.4,-42.6,-46.4,-51.8C-32.4,-61,-16.2,-63.9,0.7,-64.8C17.6,-65.7,35.2,-64.7,45.9,-58.6Z" transform="translate(100 100)" />
            </svg>
            <svg class="pointer-events-none absolute -bottom-24 -right-16 h-72 w-72 text-accent-100" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <path d="M42.4,-54.7C54.5,-45.8,63.4,-32.1,67.4,-17C71.4,-1.8,70.5,14.9,63.4,28.7C56.4,42.6,43.2,53.6,28.4,60.4C13.5,67.2,-3.1,69.7,-19.1,66.2C-35.1,62.7,-50.5,53.2,-60.5,39.6C-70.6,26,-75.2,8.3,-72.9,-8.1C-70.6,-24.6,-61.4,-39.7,-48.5,-48.8C-35.5,-57.9,-17.8,-60.9,-1.1,-59.4C15.6,-57.9,31.2,-51.9,42.4,-54.7Z" transform="translate(100 100)" />
            </svg>
            <svg class="pointer-events-none absolute right-1/3 top-8 h-24 w-24 text-mint-100 lg:block hidden" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <circle cx="100" cy="100" r="90" />
            </svg>

            <a href="/" wire:navigate class="relative z-10 mb-8">
                <x-brand-mark size="lg" />
            </a>

            <div class="relative z-10 w-full sm:max-w-md">
                <div class="neu-card p-8">
                    {{ $slot }}
                </div>
            </div>

            <p class="relative z-10 mt-8 text-center text-xs text-slate-500">
                &copy; {{ now()->year }} MindCare &middot; {{ __('Sistem Pengecekan Tingkat Stress Siswa') }}
            </p>
        </div>
    </body>
</html>
