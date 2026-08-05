<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MindCheck') }} — Pengecekan Tingkat Stress Siswa</title>
        <meta name="description" content="MindCheck membantu Guru BK memantau tingkat stress siswa SMP secara berkala dengan instrumen PSS-10, lengkap dengan dashboard visual, catatan, dan literasi penanganan stress.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:400,500,600,700|nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-surface font-sans text-slate-700 antialiased">

        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-surface-inset bg-surface/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
                <x-brand-mark />
                @if (Route::has('login'))
                    <livewire:welcome.navigation />
                @endif
            </div>
        </header>

        {{-- Hero --}}
        <section class="relative overflow-hidden">
            <svg class="pointer-events-none absolute -left-32 -top-16 h-96 w-96 text-primary-100" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <path d="M45.9,-58.6C58.9,-49.5,68.2,-34.3,71.9,-17.9C75.6,-1.6,73.7,15.9,65.8,30.2C57.9,44.5,44,55.6,28.5,62.6C13,69.6,-4.1,72.5,-20.6,68.9C-37.1,65.3,-53,55.2,-63.1,40.7C-73.2,26.2,-77.5,7.3,-74.3,-9.9C-71.1,-27.1,-60.4,-42.6,-46.4,-51.8C-32.4,-61,-16.2,-63.9,0.7,-64.8C17.6,-65.7,35.2,-64.7,45.9,-58.6Z" transform="translate(100 100)" />
            </svg>
            <svg class="pointer-events-none absolute -right-24 top-24 h-72 w-72 text-sunshine-100" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <path d="M42.4,-54.7C54.5,-45.8,63.4,-32.1,67.4,-17C71.4,-1.8,70.5,14.9,63.4,28.7C56.4,42.6,43.2,53.6,28.4,60.4C13.5,67.2,-3.1,69.7,-19.1,66.2C-35.1,62.7,-50.5,53.2,-60.5,39.6C-70.6,26,-75.2,8.3,-72.9,-8.1C-70.6,-24.6,-61.4,-39.7,-48.5,-48.8C-35.5,-57.9,-17.8,-60.9,-1.1,-59.4C15.6,-57.9,31.2,-51.9,42.4,-54.7Z" transform="translate(100 100)" />
            </svg>

            <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-mint-100 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wide text-mint-700">
                        <x-icon.sparkles class="h-3.5 w-3.5" /> {{ __('Berbasis Instrumen PSS-10') }}
                    </span>

                    <h1 class="mt-5 font-display text-4xl font-semibold leading-[1.1] tracking-tight text-slate-800 sm:text-5xl lg:text-[3.25rem]">
                        {{ __('Pantau kondisi stress siswa,') }}
                        <span class="text-primary-600">{{ __('sebelum jadi masalah besar.') }}</span>
                    </h1>

                    <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-500">
                        {{ __('MindCheck membantu Guru BK mengukur, memantau, dan menindaklanjuti tingkat stress siswa SMP secara berkala — dengan skor yang jelas, riwayat lengkap, dan rekomendasi bacaan untuk siswa.') }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ Route::has('login') ? route('login') : '#' }}" wire:navigate class="neu-pressable inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3.5 text-sm font-semibold text-white shadow-neu hover:bg-primary-700">
                            {{ __('Masuk ke Sistem') }}
                            <x-icon.chevron direction="right" class="h-4 w-4" />
                        </a>
                        <a href="#cara-kerja" class="inline-flex items-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-semibold text-slate-500 hover:text-primary-600">
                            {{ __('Lihat cara kerjanya') }}
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <x-icon.shield-check class="h-5 w-5 text-mint-600" /> {{ __('Data siswa aman & privat') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon.academic-cap class="h-5 w-5 text-accent-600" /> {{ __('Dirancang untuk sekolah') }}
                        </div>
                    </div>
                </div>

                {{-- Dashboard preview mockup --}}
                <div class="relative mx-auto w-full max-w-md lg:mx-0 lg:justify-self-end">
                    <div class="neu-card p-5">
                        <div class="flex items-center justify-between">
                            <p class="font-display text-sm font-semibold text-slate-600">{{ __('Sebaran Tingkat Stress') }}</p>
                            <x-badge color="mint">{{ __('Live') }}</x-badge>
                        </div>

                        <div class="mt-5 grid grid-cols-3 gap-3">
                            <div class="neu-inset-sm rounded-2xl p-3 text-center">
                                <p class="text-2xl font-display font-semibold text-stress-rendah">62%</p>
                                <p class="mt-1 text-[11px] font-medium text-slate-500">{{ __('Rendah') }}</p>
                            </div>
                            <div class="neu-inset-sm rounded-2xl p-3 text-center">
                                <p class="text-2xl font-display font-semibold text-stress-sedang">29%</p>
                                <p class="mt-1 text-[11px] font-medium text-slate-500">{{ __('Sedang') }}</p>
                            </div>
                            <div class="neu-inset-sm rounded-2xl p-3 text-center">
                                <p class="text-2xl font-display font-semibold text-stress-tinggi">9%</p>
                                <p class="mt-1 text-[11px] font-medium text-slate-500">{{ __('Tinggi') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex h-28 items-end gap-2.5 rounded-2xl bg-surface-card p-3">
                            @foreach ([40, 65, 45, 80, 55, 90, 60] as $bar)
                                <div class="flex-1 rounded-full bg-primary-300" style="height: {{ $bar }}%"></div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center gap-3 rounded-2xl bg-mint-50 p-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-mint-100 text-mint-600">
                                <x-icon.check-circle class="h-5 w-5" />
                            </span>
                            <p class="text-xs font-medium text-mint-700">{{ __('Asesmen minggu ini sudah dikerjakan 48 dari 52 siswa.') }}</p>
                        </div>
                    </div>

                    <div class="neu-raised absolute -bottom-6 -left-6 hidden rounded-2xl bg-surface-card p-3.5 sm:flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-bubblegum-100 text-bubblegum-600">
                            <x-icon.heart class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-slate-600">{{ __('Catatan Guru BK') }}</p>
                            <p class="text-[11px] text-slate-500">{{ __('Terhubung ke siswa') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Tentang --}}
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-display text-3xl font-semibold text-slate-800 sm:text-4xl">{{ __('Apa itu MindCheck?') }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-slate-500">
                    {{ __('Selama ini asesmen stress siswa dilakukan manual — sulit melacak histori individu dan sulit menemukan siswa yang butuh perhatian segera. MindCheck menstandardisasi proses itu dengan instrumen psikometri baku, dashboard yang mudah dibaca, dan tindak lanjut yang terdokumentasi rapi.') }}
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="neu-card p-6">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-600">
                        <x-icon.clipboard-check class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display font-semibold text-slate-800">{{ __('Standar Psikometri') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('Skor dihitung otomatis memakai metodologi PSS-10 (Perceived Stress Scale) yang tervalidasi.') }}</p>
                </div>
                <div class="neu-card p-6">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-100 text-secondary-600">
                        <x-icon.chart-bar class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display font-semibold text-slate-800">{{ __('Dashboard Visual') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('Sebaran kategori stress per kelas & sekolah, langsung terlihat tanpa perlu hitung manual.') }}</p>
                </div>
                <div class="neu-card p-6">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bubblegum-100 text-bubblegum-600">
                        <x-icon.heart class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display font-semibold text-slate-800">{{ __('Catatan & Histori') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('Guru BK bisa memberi catatan personal, tersimpan rapi bersama histori asesmen siswa.') }}</p>
                </div>
                <div class="neu-card p-6">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sunshine-100 text-sunshine-700">
                        <x-icon.book-open class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display font-semibold text-slate-800">{{ __('Literasi Stress') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('Artikel & video penanganan stress yang bisa diakses siswa kapan saja.') }}</p>
                </div>
            </div>
        </section>

        {{-- Cara Kerja --}}
        <section id="cara-kerja" class="bg-surface-card/60 py-16 lg:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="font-display text-3xl font-semibold text-slate-800 sm:text-4xl">{{ __('Cara Kerjanya') }}</h2>
                    <p class="mt-4 text-lg text-slate-500">{{ __('Empat langkah sederhana dari sudut pandang siswa.') }}</p>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        // Kelas warna ditulis literal (bukan interpolasi) supaya terdeteksi Tailwind JIT.
                        $stepColors = [
                            'primary' => ['badge' => 'bg-primary-600', 'icon' => 'bg-primary-100 text-primary-600'],
                            'secondary' => ['badge' => 'bg-secondary-600', 'icon' => 'bg-secondary-100 text-secondary-600'],
                            'accent' => ['badge' => 'bg-accent-600', 'icon' => 'bg-accent-100 text-accent-600'],
                            'mint' => ['badge' => 'bg-mint-600', 'icon' => 'bg-mint-100 text-mint-600'],
                        ];

                        $steps = [
                            ['icon' => 'user-circle', 'title' => __('Masuk'), 'desc' => __('Login pakai akun yang sudah dibuatkan Guru BK.'), 'color' => 'primary'],
                            ['icon' => 'clipboard-list', 'title' => __('Kerjakan Asesmen'), 'desc' => __('Isi kuesioner PSS-10 saat jadwal asesmen dibuka.'), 'color' => 'secondary'],
                            ['icon' => 'chart-pie', 'title' => __('Lihat Hasil & Catatan'), 'desc' => __('Skor, kategori, dan catatan dari Guru BK langsung tampil.'), 'color' => 'accent'],
                            ['icon' => 'book-open', 'title' => __('Baca Literasi'), 'desc' => __('Jelajahi artikel & video seputar penanganan stress.'), 'color' => 'mint'],
                        ];
                    @endphp

                    @foreach ($steps as $i => $step)
                        <div class="relative neu-card p-6">
                            <span class="absolute -top-3 -left-1 flex h-7 w-7 items-center justify-center rounded-full {{ $stepColors[$step['color']]['badge'] }} font-display text-xs font-bold text-white shadow-neu-sm">
                                {{ $i + 1 }}
                            </span>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $stepColors[$step['color']]['icon'] }}">
                                <x-dynamic-component :component="'icon.' . $step['icon']" class="h-6 w-6" />
                            </span>
                            <h3 class="mt-4 font-display font-semibold text-slate-800">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Peran --}}
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-display text-3xl font-semibold text-slate-800 sm:text-4xl">{{ __('Satu Sistem, Tiga Peran') }}</h2>
                <p class="mt-4 text-lg text-slate-500">{{ __('Setiap peran punya akses yang relevan dengan tugasnya.') }}</p>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-3">
                <div class="neu-card p-7">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bubblegum-100 text-bubblegum-600">
                        <x-icon.user-circle class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display text-xl font-semibold text-slate-800">{{ __('Siswa') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-bubblegum-500" /> {{ __('Kerjakan asesmen sesuai jadwal') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-bubblegum-500" /> {{ __('Lihat statistik & histori diri sendiri') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-bubblegum-500" /> {{ __('Baca catatan Guru BK & literasi stress') }}</li>
                    </ul>
                </div>

                <div class="neu-card p-7 lg:-translate-y-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-600">
                        <x-icon.users class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display text-xl font-semibold text-slate-800">{{ __('Guru BK') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" /> {{ __('Kelola data siswa, kelas & tahun ajaran') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" /> {{ __('Kelola bank soal, assessment & jadwal') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" /> {{ __('Pantau dashboard & beri catatan hasil') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" /> {{ __('Kelola konten literasi & ekspor laporan') }}</li>
                    </ul>
                </div>

                <div class="neu-card p-7">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sunshine-100 text-sunshine-700">
                        <x-icon.shield-check class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 font-display text-xl font-semibold text-slate-800">{{ __('Admin') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-sunshine-600" /> {{ __('Kelola akun Guru BK') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-sunshine-600" /> {{ __('Mewarisi seluruh akses Guru BK') }}</li>
                        <li class="flex items-start gap-2"><x-icon.check-circle class="mt-0.5 h-4 w-4 shrink-0 text-sunshine-600" /> {{ __('Pengawasan penuh terhadap sistem') }}</li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8 lg:pb-24">
            <div class="relative overflow-hidden rounded-4xl bg-primary-600 px-8 py-14 text-center shadow-neu-lg sm:px-16">
                <svg class="pointer-events-none absolute -right-10 -top-10 h-56 w-56 text-primary-500/60" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                    <circle cx="100" cy="100" r="90" />
                </svg>
                <svg class="pointer-events-none absolute -bottom-14 -left-10 h-48 w-48 text-primary-500/60" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                    <circle cx="100" cy="100" r="90" />
                </svg>

                <h2 class="relative font-display text-3xl font-semibold text-white sm:text-4xl">{{ __('Siap mulai memantau kesejahteraan siswa?') }}</h2>
                <p class="relative mx-auto mt-4 max-w-xl text-primary-100">{{ __('Masuk dengan akun yang sudah disediakan sekolahmu dan mulai lihat gambaran kondisi siswa hari ini.') }}</p>
                <a href="{{ Route::has('login') ? route('login') : '#' }}" wire:navigate class="relative mt-8 inline-flex items-center gap-2 rounded-2xl bg-white px-7 py-3.5 text-sm font-semibold text-primary-700 shadow-neu-sm hover:bg-primary-50">
                    {{ __('Masuk ke Sistem') }}
                    <x-icon.chevron direction="right" class="h-4 w-4" />
                </a>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-surface-inset">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-sm text-slate-500 sm:flex-row sm:px-6 lg:px-8">
                <x-brand-mark size="sm" class="opacity-90" />
                <p>&copy; {{ now()->year }} MindCheck &middot; {{ __('Sistem Pengecekan Tingkat Stress Siswa') }}</p>
            </div>
        </footer>
    </body>
</html>
