<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'text',
        'order',
        'reverse_scored',
        'is_active',
        'is_core',
    ];

    protected function casts(): array
    {
        return [
            'reverse_scored' => 'boolean',
            'is_active' => 'boolean',
            'is_core' => 'boolean',
        ];
    }
}
