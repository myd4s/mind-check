<?php

namespace App\Enums;

enum Severity: string
{
    case Normal = 'normal';
    case Mild = 'mild';
    case Moderate = 'moderate';
    case Severe = 'severe';
    case ExtremelySevere = 'extremely_severe';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Mild => 'Ringan',
            self::Moderate => 'Sedang',
            self::Severe => 'Parah',
            self::ExtremelySevere => 'Sangat Parah',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Normal => 'green',
            self::Mild => 'yellow',
            self::Moderate => 'orange',
            self::Severe => 'red',
            self::ExtremelySevere => 'rose',
        };
    }

    public function isConcerning(): bool
    {
        return in_array($this, [self::Severe, self::ExtremelySevere], true);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Normal => 'bg-green-50 text-green-700',
            self::Mild => 'bg-yellow-50 text-yellow-700',
            self::Moderate => 'bg-orange-50 text-orange-700',
            self::Severe => 'bg-red-50 text-red-700',
            self::ExtremelySevere => 'bg-rose-100 text-rose-700',
        };
    }

    public function ringColorClass(): string
    {
        return match ($this) {
            self::Normal => 'text-green-500',
            self::Mild => 'text-yellow-500',
            self::Moderate => 'text-orange-500',
            self::Severe => 'text-red-500',
            self::ExtremelySevere => 'text-rose-600',
        };
    }

    public function hexColor(): string
    {
        return match ($this) {
            self::Normal => '#22c55e',
            self::Mild => '#eab308',
            self::Moderate => '#f97316',
            self::Severe => '#ef4444',
            self::ExtremelySevere => '#e11d48',
        };
    }
}
