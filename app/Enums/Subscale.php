<?php

namespace App\Enums;

enum Subscale: string
{
    case Depression = 'depression';
    case Anxiety = 'anxiety';
    case Stress = 'stress';

    public function label(): string
    {
        return match ($this) {
            self::Depression => 'Depresi',
            self::Anxiety => 'Kecemasan',
            self::Stress => 'Stres',
        };
    }
}
