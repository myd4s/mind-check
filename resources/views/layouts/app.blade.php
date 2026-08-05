<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MindCheck') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:400,500,600,700|nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-surface font-sans text-slate-700 antialiased">
        <livewire:layout.navigation />

        <div class="lg:pl-72">
            <main class="min-h-screen px-4 pb-10 pt-24 sm:px-6 lg:px-8 lg:pt-28">
                @if (isset($header))
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
