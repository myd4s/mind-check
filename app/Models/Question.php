<?php

namespace App\Models;

use App\Enums\Subscale;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'order_number',
        'subscale',
        'text',
    ];

    protected function casts(): array
    {
        return [
            'subscale' => Subscale::class,
        ];
    }
}
