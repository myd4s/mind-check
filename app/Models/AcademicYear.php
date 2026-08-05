<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    protected static function booted(): void
    {
        // Hanya boleh ada 1 tahun ajaran aktif (PRD §3) — saat satu diaktifkan,
        // yang lain otomatis dinonaktifkan.
        static::saving(function (AcademicYear $academicYear) {
            if ($academicYear->is_active) {
                static::where('id', '!=', $academicYear->id ?? 0)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }
}
