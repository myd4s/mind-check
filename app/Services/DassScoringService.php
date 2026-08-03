<?php

namespace App\Services;

use App\Enums\AssessmentStatus;
use App\Enums\Severity;
use App\Enums\Subscale;
use App\Models\Assessment;

class DassScoringService
{
    /**
     * Setiap subskala terdiri dari 7 soal berskala 0-4 (raw sum 0-28). Skala DASS-21
     * baku menjumlahkan 7 soal berskala 0-3 lalu dikali 2 (raw sum 0-21 -> skor 0-42).
     * Faktor 1.5 menyamakan rentang 0-28 kita ke rentang skor baku 0-42 agar cutoff
     * severity resmi DASS-21 tetap berlaku.
     */
    private const SCALE_FACTOR = 1.5;

    private const CUTOFFS = [
        'depression' => [
            'normal' => [0, 9],
            'mild' => [10, 13],
            'moderate' => [14, 20],
            'severe' => [21, 27],
            'extremely_severe' => [28, PHP_INT_MAX],
        ],
        'anxiety' => [
            'normal' => [0, 7],
            'mild' => [8, 9],
            'moderate' => [10, 14],
            'severe' => [15, 19],
            'extremely_severe' => [20, PHP_INT_MAX],
        ],
        'stress' => [
            'normal' => [0, 14],
            'mild' => [15, 18],
            'moderate' => [19, 25],
            'severe' => [26, 33],
            'extremely_severe' => [34, PHP_INT_MAX],
        ],
    ];

    public function score(int $rawSum): int
    {
        return (int) round($rawSum * self::SCALE_FACTOR);
    }

    public function severityFor(Subscale $subscale, int $score): Severity
    {
        foreach (self::CUTOFFS[$subscale->value] as $severity => [$min, $max]) {
            if ($score >= $min && $score <= $max) {
                return Severity::from($severity);
            }
        }

        return Severity::ExtremelySevere;
    }

    /**
     * Hitung skor & severity per subskala dari jawaban yang tersimpan, lalu
     * simpan hasilnya dan tandai assessment sebagai selesai.
     */
    public function scoreAssessment(Assessment $assessment): Assessment
    {
        $rawSums = $assessment->answers()
            ->join('questions', 'questions.id', '=', 'assessment_answers.question_id')
            ->selectRaw('questions.subscale as subscale, sum(assessment_answers.answer_value) as total')
            ->groupBy('questions.subscale')
            ->pluck('total', 'subscale');

        $result = [];

        foreach (Subscale::cases() as $subscale) {
            $raw = (int) ($rawSums[$subscale->value] ?? 0);
            $score = $this->score($raw);

            $result["{$subscale->value}_raw"] = $raw;
            $result["{$subscale->value}_score"] = $score;
            $result["{$subscale->value}_severity"] = $this->severityFor($subscale, $score);
        }

        $result['overall_severity'] = collect([
            $result['depression_severity'],
            $result['anxiety_severity'],
            $result['stress_severity'],
        ])->sortByDesc(fn (Severity $severity) => array_search($severity, Severity::cases(), true))->first();

        $assessment->fill($result);
        $assessment->status = AssessmentStatus::Completed;
        $assessment->completed_at = now();
        $assessment->save();

        return $assessment;
    }
}
