<?php

namespace App\Enums;

enum FollowUpStatus: string
{
    case BelumDitangani = 'belum_ditangani';
    case SedangDitangani = 'sedang_ditangani';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::BelumDitangani => 'Belum Ditangani',
            self::SedangDitangani => 'Sedang Ditangani',
            self::Selesai => 'Selesai',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::BelumDitangani => 'bg-red-50 text-red-700',
            self::SedangDitangani => 'bg-yellow-50 text-yellow-700',
            self::Selesai => 'bg-green-50 text-green-700',
        };
    }
}
