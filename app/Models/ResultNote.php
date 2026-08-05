<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultNote extends Model
{
    protected $fillable = [
        'assessment_result_id',
        'guru_bk_id',
        'content',
    ];

    public function assessmentResult(): BelongsTo
    {
        return $this->belongsTo(AssessmentResult::class);
    }

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_bk_id');
    }
}
