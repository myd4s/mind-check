<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * 21 pernyataan DASS-21 (adaptasi Bahasa Indonesia), dengan pemetaan
     * subskala mengikuti kunci skoring resmi Lovibond & Lovibond.
     */
    public function run(): void
    {
        $questions = [
            1 => ['stress', 'Saya merasa sulit untuk menenangkan diri.'],
            2 => ['anxiety', 'Saya menyadari mulut saya terasa kering.'],
            3 => ['depression', 'Saya sama sekali tidak dapat merasakan perasaan positif.'],
            4 => ['anxiety', 'Saya mengalami kesulitan bernapas (misalnya napas cepat atau sesak, padahal tidak sedang beraktivitas fisik).'],
            5 => ['depression', 'Saya merasa sulit untuk memulai melakukan sesuatu.'],
            6 => ['stress', 'Saya cenderung bereaksi berlebihan terhadap suatu situasi.'],
            7 => ['anxiety', 'Saya mengalami tubuh gemetar (misalnya pada tangan).'],
            8 => ['stress', 'Saya merasa telah menghabiskan banyak energi karena cemas.'],
            9 => ['anxiety', 'Saya khawatir dengan situasi yang mungkin membuat saya panik dan mempermalukan diri sendiri.'],
            10 => ['depression', 'Saya merasa tidak ada lagi yang bisa dinantikan.'],
            11 => ['stress', 'Saya mendapati diri saya mudah gelisah.'],
            12 => ['stress', 'Saya merasa sulit untuk bersantai.'],
            13 => ['depression', 'Saya merasa sedih dan murung.'],
            14 => ['stress', 'Saya tidak dapat mentoleransi hal apa pun yang menghalangi saya menyelesaikan pekerjaan.'],
            15 => ['anxiety', 'Saya merasa hampir panik.'],
            16 => ['depression', 'Saya tidak dapat merasa antusias terhadap hal apa pun.'],
            17 => ['depression', 'Saya merasa diri saya tidak berharga sebagai seseorang.'],
            18 => ['stress', 'Saya merasa mudah tersinggung.'],
            19 => ['anxiety', 'Saya menyadari detak jantung saya meskipun tidak sedang beraktivitas fisik.'],
            20 => ['anxiety', 'Saya merasa takut tanpa alasan yang jelas.'],
            21 => ['depression', 'Saya merasa hidup ini tidak bermakna.'],
        ];

        foreach ($questions as $orderNumber => [$subscale, $text]) {
            Question::updateOrCreate(
                ['order_number' => $orderNumber],
                ['subscale' => $subscale, 'text' => $text]
            );
        }
    }
}
