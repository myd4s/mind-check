<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assessment extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->withPivot('order')
            ->orderBy('assessment_question.order');
    }
}
