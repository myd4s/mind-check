<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $fillable = [
        'assessment_id',
        'counselor_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => FollowUpStatus::class,
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
