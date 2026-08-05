<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentSchedule extends Model
{
    protected $fillable = [
        'assessment_id',
        'academic_year_id',
        'title',
        'start_at',
        'end_at',
        'target_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function targetClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'assessment_schedule_school_class');
    }

    /**
     * Jadwal dianggap "terbuka" untuk dikerjakan siswa jika aktif dan waktu
     * sekarang berada dalam rentang mulai–selesai (PRD §11 DoD).
     */
    public function isOpenNow(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        return $now->betweenIncluded($this->start_at, $this->end_at);
    }

    public function appliesToClass(int $schoolClassId): bool
    {
        if ($this->target_type === 'all') {
            return true;
        }

        return $this->targetClasses()->where('school_classes.id', $schoolClassId)->exists();
    }
}
