<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentResult extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_schedule_id',
        'total_score',
        'category',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessmentSchedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function note(): HasOne
    {
        return $this->hasOne(ResultNote::class);
    }
}
