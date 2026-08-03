<?php

namespace Database\Seeders;

use App\Models\Recommendation;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $recommendations = [
            'depression' => [
                'normal' => ['Pertahankan Kebiasaan Baik', 'Suasana hati Anda tergolong baik. Tetap jaga interaksi sosial dan aktivitas yang menyenangkan.'],
                'mild' => ['Kenali Perasaan Anda', 'Coba luangkan waktu menuliskan hal-hal yang Anda syukuri setiap hari.'],
                'moderate' => ['Cari Dukungan Terdekat', 'Bicarakan perasaan Anda dengan teman, keluarga, atau guru BK yang Anda percaya.'],
                'severe' => ['Segera Hubungi Guru BK', 'Kondisi Anda menunjukkan tanda yang memerlukan pendampingan lebih lanjut dari konselor sekolah.'],
                'extremely_severe' => ['Butuh Bantuan Profesional', 'Sangat disarankan untuk segera berkonsultasi dengan konselor atau tenaga profesional kesehatan mental.'],
            ],
            'anxiety' => [
                'normal' => ['Tetap Tenang dan Fokus', 'Tingkat kecemasan Anda dalam batas wajar. Lanjutkan rutinitas belajar dengan baik.'],
                'mild' => ['Latihan Pernapasan', 'Coba teknik tarik napas dalam selama beberapa menit saat merasa cemas.'],
                'moderate' => ['Kelola Pemicu Cemas', 'Kenali situasi yang memicu kecemasan dan siapkan strategi untuk menghadapinya.'],
                'severe' => ['Konsultasi dengan Konselor', 'Kecemasan Anda cukup tinggi, sebaiknya didiskusikan dengan guru BK.'],
                'extremely_severe' => ['Segera Cari Bantuan', 'Tingkat kecemasan sangat tinggi, penting untuk mendapat pendampingan profesional segera.'],
            ],
            'stress' => [
                'normal' => ['Jaga Keseimbangan', 'Tingkat stres Anda terkendali, tetap jaga pola istirahat dan belajar yang seimbang.'],
                'mild' => ['Atur Waktu Istirahat', 'Sisipkan waktu istirahat singkat di antara jadwal belajar Anda.'],
                'moderate' => ['Kelola Beban Tugas', 'Coba buat skala prioritas tugas agar tidak merasa kewalahan.'],
                'severe' => ['Bicarakan dengan Orang Terdekat', 'Stres Anda cukup tinggi, penting untuk berbagi cerita dengan orang yang dipercaya.'],
                'extremely_severe' => ['Segera Temui Guru BK', 'Tingkat stres sangat tinggi, sebaiknya segera mendapat pendampingan dari konselor sekolah.'],
            ],
        ];

        foreach ($recommendations as $subscale => $severities) {
            foreach ($severities as $severity => [$title, $description]) {
                Recommendation::updateOrCreate(
                    ['subscale' => $subscale, 'severity' => $severity],
                    ['title' => $title, 'description' => $description]
                );
            }
        }
    }
}
