<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Seed 10 soal inti PSS-10 (Perceived Stress Scale, Cohen et al. 1983)
     * adaptasi Bahasa Indonesia. Item 4, 5, 7, 8 bersifat reverse-scored
     * sesuai metodologi asli (PRD §4) — jangan ubah urutan/flag ini.
     */
    public function run(): void
    {
        $questions = [
            ['order' => 1, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa terganggu karena sesuatu yang terjadi secara tidak terduga?', 'reverse_scored' => false],
            ['order' => 2, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa tidak mampu mengendalikan hal-hal penting dalam hidup Anda?', 'reverse_scored' => false],
            ['order' => 3, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa gugup dan tertekan (stres)?', 'reverse_scored' => false],
            ['order' => 4, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa yakin akan kemampuan Anda mengatasi masalah pribadi?', 'reverse_scored' => true],
            ['order' => 5, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa bahwa segala sesuatu berjalan sesuai keinginan Anda?', 'reverse_scored' => true],
            ['order' => 6, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa tidak dapat mengatasi semua hal yang harus Anda lakukan?', 'reverse_scored' => false],
            ['order' => 7, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda mampu mengendalikan rasa jengkel/kesal dalam hidup Anda?', 'reverse_scored' => true],
            ['order' => 8, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa segala sesuatu berjalan baik dan berada dalam kendali Anda?', 'reverse_scored' => true],
            ['order' => 9, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda marah karena sesuatu yang terjadi di luar kendali Anda?', 'reverse_scored' => false],
            ['order' => 10, 'text' => 'Dalam sebulan terakhir, seberapa sering Anda merasa kesulitan menumpuk begitu tinggi sehingga Anda tidak dapat mengatasinya?', 'reverse_scored' => false],
        ];

        foreach ($questions as $question) {
            Question::updateOrCreate(
                ['order' => $question['order'], 'is_core' => true],
                [
                    'text' => $question['text'],
                    'reverse_scored' => $question['reverse_scored'],
                    'is_active' => true,
                    'is_core' => true,
                ]
            );
        }
    }
}
