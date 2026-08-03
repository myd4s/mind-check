<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'nis',
        'gender',
        'birth_date',
        'phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'status' => StudentStatus::class,
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
