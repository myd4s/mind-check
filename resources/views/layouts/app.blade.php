<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MindCheck') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div
            x-data="{ mobileOpen: false }"
            class="flex h-screen overflow-hidden bg-gray-50"
        >
            <livewire:layout.navigation mode="sidebar" />

            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Bar -->
                <header class="shrink-0 bg-white border-b border-gray-100 flex items-center justify-between gap-4 px-4 sm:px-6 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            @click="mobileOpen = true" type="button"
                            class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <livewire:notification-bell />
                        <livewire:layout.navigation mode="topbar" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
