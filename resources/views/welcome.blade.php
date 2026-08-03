<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MindCheck &mdash; Kenali Kondisi Kesehatan Mentalmu</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans text-gray-900 bg-white">
        <header class="sticky top-0 z-40 bg-white/70 backdrop-blur border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="w-9 h-9" />
                    <span class="text-lg font-bold text-gray-900">MindCheck</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#beranda" class="hover:text-indigo-600">Beranda</a>
                    <a href="#tentang" class="hover:text-indigo-600">Tentang</a>
                    <a href="#cara-kerja" class="hover:text-indigo-600">Cara Kerja</a>
                    <a href="#faq" class="hover:text-indigo-600">FAQ</a>
                </nav>

                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-indigo-200 text-indigo-600 font-medium text-sm hover:bg-indigo-50">
                    Masuk
                </a>
            </div>
        </header>

        <main>
            <section id="beranda" class="relative overflow-hidden bg-gray-50">
                <div class="max-w-7xl mx-auto px-6 py-20 lg:py-28 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-sm font-medium mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" />
                            </svg>
                            Platform Kesehatan Mental Siswa
                        </span>

                        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight text-gray-900 mb-6">
                            Kenali <span class="bg-gradient-to-r from-indigo-600 to-cyan-500 bg-clip-text text-transparent">Kondisi Kesehatan Mentalmu.</span>
                        </h1>

                        <p class="text-lg text-gray-500 mb-8 max-w-lg">
                            MindCheck membantu sekolah melakukan skrining stres siswa secara cepat, aman, dan rahasia
                            &mdash; sehingga tidak ada siswa yang harus berjuang sendirian.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 mb-12">
                            <a href="{{ route('login') }}" class="text-center px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                                Mulai Asesmen
                            </a>
                            <a href="#cara-kerja" class="text-center px-6 py-3 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-100">
                                Pelajari Lebih Lanjut
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-6 max-w-md">
                            <div>
                                <p class="text-2xl font-extrabold text-gray-900">2.400+</p>
                                <p class="text-sm text-gray-500">Siswa Terbantu</p>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-gray-900">98%</p>
                                <p class="text-sm text-gray-500">Tingkat Kepuasan</p>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-gray-900">50+</p>
                                <p class="text-sm text-gray-500">Sekolah Mitra</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -top-10 -right-10 w-64 h-64 rounded-full bg-cyan-100 blur-3xl opacity-70"></div>
                        <div class="absolute -bottom-10 -left-10 w-64 h-64 rounded-full bg-indigo-100 blur-3xl opacity-70"></div>

                        <div class="relative bg-gradient-to-br from-indigo-50 via-white to-cyan-50 rounded-3xl p-10 border border-indigo-100/60 overflow-hidden">
                            <svg viewBox="0 0 320 260" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                                <!-- floating decorations -->
                                <path d="M40 40l3.5 8 8 3.5-8 3.5-3.5 8-3.5-8-8-3.5 8-3.5Z" fill="#818cf8" />
                                <path d="M270 60l3 7 7 3-7 3-3 7-3-7-7-3 7-3Z" fill="#facc15" />
                                <circle cx="52" cy="120" r="16" fill="#ede9fe" />
                                <path d="M52 112v16M44 120h16" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" />
                                <circle cx="280" cy="150" r="18" fill="#cffafe" />
                                <path d="M280 141v18M271 150h18" stroke="#0891b2" stroke-width="3" stroke-linecap="round" />

                                <!-- desk -->
                                <rect x="70" y="205" width="190" height="8" rx="4" fill="#c7d2fe" />
                                <rect x="90" y="213" width="8" height="26" rx="2" fill="#a5b4fc" />
                                <rect x="222" y="213" width="8" height="26" rx="2" fill="#a5b4fc" />

                                <!-- laptop -->
                                <rect x="128" y="150" width="80" height="55" rx="6" fill="#312e81" />
                                <rect x="136" y="158" width="64" height="39" rx="3" fill="#4f46e5" />
                                <rect x="118" y="205" width="100" height="7" rx="3.5" fill="#4338ca" />

                                <!-- person -->
                                <rect x="138" y="120" width="44" height="46" rx="18" fill="#f59e0b" />
                                <circle cx="160" cy="98" r="24" fill="#fbbf24" />
                                <path d="M138 92a22 22 0 0 1 44 0c0-14-10-26-22-26s-22 12-22 26Z" fill="#1e1b4b" />
                                <circle cx="151" cy="98" r="2.5" fill="#1e1b4b" />
                                <circle cx="169" cy="98" r="2.5" fill="#1e1b4b" />
                                <path d="M152 107q8 6 16 0" stroke="#1e1b4b" stroke-width="2.5" stroke-linecap="round" fill="none" />
                                <path d="M138 132c-14 4-22 16-22 30" stroke="#f59e0b" stroke-width="12" stroke-linecap="round" />
                                <path d="M182 132c14 4 22 16 22 30" stroke="#f59e0b" stroke-width="12" stroke-linecap="round" />
                            </svg>

                            <div class="text-center mt-2">
                                <p class="font-semibold text-gray-900">Skrining Mandiri</p>
                                <p class="text-sm text-gray-500">21 pertanyaan singkat, hasil instan, rekomendasi personal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="tentang" class="max-w-7xl mx-auto px-6 py-20">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Mengapa MindCheck?</h2>
                    <p class="text-gray-500">Dirancang khusus untuk kebutuhan sekolah dalam mendeteksi dan mendampingi kesehatan mental siswa.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 1 0-8 0v2h8Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Rahasia</h3>
                        <p class="text-sm text-gray-500">Data dan hasil asesmen siswa terjaga kerahasiaannya, hanya diakses pihak berwenang.</p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Cepat</h3>
                        <p class="text-sm text-gray-500">Kuesioner singkat 21 pertanyaan, dapat diselesaikan hanya dalam beberapa menit.</p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M4 7h16M5 7h14v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Otomatis</h3>
                        <p class="text-sm text-gray-500">Skor dan kategori tingkat stres dihitung otomatis berdasarkan metode yang teruji.</p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m5-3.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-1a4 4 0 0 0 0-8m-13 8a4 4 0 0 1 0-8" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Didampingi Konselor</h3>
                        <p class="text-sm text-gray-500">Guru BK menerima notifikasi otomatis untuk siswa yang perlu perhatian khusus.</p>
                    </div>
                </div>
            </section>

            <section id="cara-kerja" class="bg-gray-50 py-20">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center max-w-2xl mx-auto mb-14">
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Cara Kerja</h2>
                        <p class="text-gray-500">Tiga langkah sederhana untuk mengenali kondisi kesehatan mentalmu.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4">1</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Masuk &amp; Isi Kuesioner</h3>
                            <p class="text-sm text-gray-500">Siswa masuk ke akun dan mengisi 21 pernyataan singkat seputar kondisi seminggu terakhir.</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4">2</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Sistem Menghitung Skor</h3>
                            <p class="text-sm text-gray-500">Hasil dihitung otomatis untuk tiga aspek: depresi, kecemasan, dan stres.</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4">3</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Dapatkan Rekomendasi</h3>
                            <p class="text-sm text-gray-500">Siswa menerima hasil beserta rekomendasi, dan Guru BK didampingi bila diperlukan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="faq" class="max-w-3xl mx-auto px-6 py-20">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-10 text-center">Pertanyaan Umum</h2>

                <div class="space-y-4">
                    <div class="p-5 rounded-xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-2">Apakah hasil asesmen bersifat diagnosis medis?</h3>
                        <p class="text-sm text-gray-500">Tidak. MindCheck adalah alat skrining awal untuk mengenali kecenderungan stres, bukan alat diagnosis klinis. Untuk penanganan lebih lanjut, siswa akan didampingi oleh Guru BK.</p>
                    </div>
                    <div class="p-5 rounded-xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-2">Siapa saja yang bisa melihat hasil asesmen saya?</h3>
                        <p class="text-sm text-gray-500">Hasil dapat dilihat oleh siswa yang bersangkutan, Guru BK, dan admin sekolah untuk keperluan pendampingan.</p>
                    </div>
                    <div class="p-5 rounded-xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-2">Berapa lama waktu yang dibutuhkan untuk mengisi kuesioner?</h3>
                        <p class="text-sm text-gray-500">Kuesioner terdiri dari 21 pernyataan singkat dan umumnya dapat diselesaikan dalam 5-10 menit.</p>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-6 pb-20">
                <div class="rounded-3xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-8 py-14 text-center text-white">
                    <h2 class="text-3xl font-extrabold mb-4">Siap memulai skrining di sekolah Anda?</h2>
                    <p class="text-white/80 max-w-xl mx-auto mb-8">Bergabunglah dengan sekolah-sekolah yang telah mempercayai MindCheck untuk mendampingi kesehatan mental siswa.</p>
                    <a href="{{ route('login') }}" class="inline-block px-8 py-3 rounded-lg bg-white text-indigo-600 font-semibold hover:bg-gray-50">
                        Masuk Sekarang
                    </a>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <x-application-logo class="w-8 h-8" />
                        <span class="font-bold text-gray-900">MindCheck</span>
                    </div>
                    <p class="text-sm text-gray-500">Platform skrining kesehatan mental siswa untuk sekolah.</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-900 mb-3 text-sm">Produk</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#tentang" class="hover:text-indigo-600">Fitur</a></li>
                        <li><a href="#cara-kerja" class="hover:text-indigo-600">Cara Kerja</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-indigo-600">Masuk</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold text-gray-900 mb-3 text-sm">Perusahaan</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#faq" class="hover:text-indigo-600">FAQ</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold text-gray-900 mb-3 text-sm">Ikuti Kami</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-indigo-50 hover:text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 4.01c-.77.35-1.6.58-2.46.69a4.3 4.3 0 0 0 1.88-2.37 8.59 8.59 0 0 1-2.72 1.04 4.28 4.28 0 0 0-7.29 3.9A12.14 12.14 0 0 1 3.11 3.15a4.28 4.28 0 0 0 1.32 5.71 4.25 4.25 0 0 1-1.94-.53v.05a4.28 4.28 0 0 0 3.43 4.2 4.3 4.3 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.97A8.6 8.6 0 0 1 2 17.54a12.1 12.1 0 0 0 6.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.37-.01-.55A8.72 8.72 0 0 0 22 4.01Z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-indigo-50 hover:text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.2c-5.4 0-9.8 4.4-9.8 9.8 0 4.9 3.6 8.9 8.2 9.7v-6.9H8v-2.8h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.3h-1.2c-1.2 0-1.6.8-1.6 1.5v1.8h2.7l-.4 2.8h-2.3v6.9c4.6-.8 8.2-4.8 8.2-9.7 0-5.4-4.4-9.8-9.8-9.8Z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-indigo-50 hover:text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7.8 2A5.8 5.8 0 0 0 2 7.8v8.4A5.8 5.8 0 0 0 7.8 22h8.4a5.8 5.8 0 0 0 5.8-5.8V7.8A5.8 5.8 0 0 0 16.2 2H7.8Zm0 2h8.4A3.8 3.8 0 0 1 20 7.8v8.4a3.8 3.8 0 0 1-3.8 3.8H7.8A3.8 3.8 0 0 1 4 16.2V7.8A3.8 3.8 0 0 1 7.8 4Zm8.6 1.6a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 py-6 text-center text-sm text-gray-400">
                &copy; {{ now()->year }} MindCheck. Alat skrining awal, bukan diagnosis klinis.
            </div>
        </footer>
    </body>
</html>
