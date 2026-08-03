<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use App\Enums\Severity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'status',
        'started_at',
        'completed_at',
        'depression_raw',
        'anxiety_raw',
        'stress_raw',
        'depression_score',
        'anxiety_score',
        'stress_score',
        'depression_severity',
        'anxiety_severity',
        'stress_severity',
        'overall_severity',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssessmentStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'depression_severity' => Severity::class,
            'anxiety_severity' => Severity::class,
            'stress_severity' => Severity::class,
            'overall_severity' => Severity::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }
}
