<?php

namespace App\Models;

use App\Enums\Severity;
use App\Enums\Subscale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Recommendation extends Model
{
    protected $fillable = [
        'subscale',
        'severity',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'subscale' => Subscale::class,
            'severity' => Severity::class,
        ];
    }

    /**
     * Ambil satu rekomendasi paling relevan untuk tiap subskala pada sebuah assessment.
     */
    public static function forAssessment(Assessment $assessment): Collection
    {
        $severityBySubscale = [
            Subscale::Depression->value => $assessment->depression_severity,
            Subscale::Anxiety->value => $assessment->anxiety_severity,
            Subscale::Stress->value => $assessment->stress_severity,
        ];

        return collect($severityBySubscale)
            ->map(fn ($severity, $subscale) => static::where('subscale', $subscale)
                ->where('severity', $severity->value)
                ->first())
            ->filter()
            ->values();
    }
}
