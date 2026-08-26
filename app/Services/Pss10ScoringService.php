<?php

namespace App\Services;

use App\Models\Question;

class Pss10ScoringService
{
    /**
     * Skor per item: item reverse-scored dibalik (4 - x) sesuai metodologi
     * PSS-10 asli (PRD §4).
     */
    public function scoreItem(Question $question, int $rawAnswer): int
    {
        return $question->reverse_scored ? (4 - $rawAnswer) : $rawAnswer;
    }

    /**
     * Kategori berdasar skor total, proporsional terhadap jumlah soal yang
     * benar-benar dinilai (skala 0-4 per soal). Cut-off PSS-10 standar
     * (0-13 Rendah, 14-26 Sedang, 27-40 Tinggi; PRD §4) berlaku sebagai
     * rasio (32.5% / 65% dari skor maksimum) sehingga tetap valid saat
     * assessment berisi soal pendamping/opsional di luar 10 soal inti.
     */
    public function categorize(int $totalScore, int $questionCount = 10): string
    {
        $maxScore = $questionCount * 4;
        $lowCutoff = (int) round($maxScore * 13 / 40);
        $mediumCutoff = (int) round($maxScore * 26 / 40);

        return match (true) {
            $totalScore <= $lowCutoff => 'rendah',
            $totalScore <= $mediumCutoff => 'sedang',
            default => 'tinggi',
        };
    }
}
