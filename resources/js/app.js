import './bootstrap';

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

// Intersection Observer untuk scroll animations
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        threshold: 0.05,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Hapus class inactive untuk trigger animasi
                entry.target.classList.remove('scroll-animate-inactive');
            }
        });
    }, observerOptions);

    // Observe semua elemen dengan class scroll-animate*
    const elements = document.querySelectorAll('.scroll-animate, .scroll-animate-left, .scroll-animate-right');
    elements.forEach(el => {
        // Set initial state sebagai inactive (opacity 0, tidak animated)
        el.classList.add('scroll-animate-inactive');
        observer.observe(el);

        // Cek apakah elemen sudah visible saat page load
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            el.classList.remove('scroll-animate-inactive');
        }
    });
});
