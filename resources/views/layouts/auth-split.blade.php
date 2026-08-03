<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MindCheck') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-indigo-600 to-cyan-500 text-white p-12 relative overflow-hidden">
                <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full bg-white/10"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full bg-white/10 -mb-20 -ml-20"></div>

                <a href="/" wire:navigate class="flex items-center gap-2 relative z-10">
                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" />
                        </svg>
                    </span>
                    <span class="text-xl font-bold">MindCheck</span>
                </a>

                <div class="relative z-10">
                    <h1 class="text-4xl font-bold leading-tight mb-4">Kenali Kondisi<br>Kesehatan Mentalmu.</h1>
                    <p class="text-white/80 max-w-sm">
                        MindCheck membantu sekolah melakukan skrining stres siswa secara cepat, aman, dan rahasia.
                    </p>
                </div>

                <p class="relative z-10 text-sm text-white/60">&copy; {{ now()->year }} MindCheck. Alat skrining awal, bukan diagnosis klinis.</p>
            </div>

            <div class="flex flex-col justify-center items-center p-6 sm:p-12 bg-gray-50">
                <div class="w-full sm:max-w-md">
                    <div class="lg:hidden flex items-center gap-2 mb-8 justify-center">
                        <x-application-logo class="w-10 h-10" />
                        <span class="text-xl font-bold text-gray-900">MindCheck</span>
                    </div>

                    <div class="bg-white shadow-sm rounded-2xl p-8 border border-gray-100">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
