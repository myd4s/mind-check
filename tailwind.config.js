import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Nunito = body/data (mudah dibaca di tabel guru BK), Fredoka = heading/display (playful, ramah SMP).
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                display: ['Fredoka', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand utama — indigo/violet playful (tombol, link aktif, sidebar highlight).
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
                // Biru langit — variasi/hover, kartu statistik.
                secondary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                // Oranye energik — CTA sekunder/aksen.
                accent: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                // Hue playful tambahan untuk variasi icon/kartu statistik (bukan warna aksi utama).
                mint: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                sunshine: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                bubblegum: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#ec4899',
                    600: '#db2777',
                    700: '#be185d',
                    800: '#9d174d',
                    900: '#831843',
                },
                // Semantik kategori tingkat stress (PRD §4) — dipakai konsisten di badge, chart, dan alert.
                // TIDAK diubah oleh redesain — sudah tervalidasi AA-contrast & psikometri.
                stress: {
                    rendah: '#16a34a',
                    sedang: '#d97706',
                    tinggi: '#dc2626',
                },
                // Dasar permukaan neumorphism — lavender pucat, bukan putih/abu polos.
                surface: {
                    DEFAULT: '#eef1fa',
                    card: '#f7f8fc',
                    inset: '#e4e8f5',
                },
            },
            boxShadow: {
                // Dual-shadow neumorphism (terang atas-kiri, gelap bawah-kanan) di atas warna solid — tanpa gradient.
                'neu-sm': '3px 3px 7px rgba(163,177,198,0.45), -3px -3px 7px rgba(255,255,255,0.85)',
                neu: '6px 6px 14px rgba(163,177,198,0.5), -6px -6px 14px rgba(255,255,255,0.9)',
                'neu-lg': '10px 10px 24px rgba(163,177,198,0.5), -10px -10px 24px rgba(255,255,255,0.9)',
                'neu-inset-sm': 'inset 2px 2px 5px rgba(163,177,198,0.45), inset -2px -2px 5px rgba(255,255,255,0.8)',
                'neu-inset': 'inset 4px 4px 9px rgba(163,177,198,0.5), inset -4px -4px 9px rgba(255,255,255,0.85)',
            },
            borderRadius: {
                '4xl': '2rem',
            },
        },
    },

    // Gradient dilarang di seluruh UI (PRD §7) — dinonaktifkan di level core plugin
    // supaya bg-gradient-to-*, from-*, via-*, to-* tidak bisa dipakai sama sekali.
    // Neumorphism tidak butuh gradient (efek depth datang dari dual box-shadow di atas warna solid).
    corePlugins: {
        backgroundImage: false,
        gradientColorStops: false,
    },

    plugins: [forms],
};
