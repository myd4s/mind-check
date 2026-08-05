<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
    ];

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }
}
