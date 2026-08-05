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
     * Kategori berdasar skor total 0-40 (PRD §4):
     * 0-13 Rendah/Normal, 14-26 Sedang, 27-40 Tinggi.
     */
    public function categorize(int $totalScore): string
    {
        return match (true) {
            $totalScore <= 13 => 'rendah',
            $totalScore <= 26 => 'sedang',
            default => 'tinggi',
        };
    }
}
