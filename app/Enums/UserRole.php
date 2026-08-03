<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Counselor = 'counselor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Siswa',
            self::Counselor => 'Guru BK',
            self::Admin => 'Admin',
        };
    }

    public function dashboardRouteName(): string
    {
        return match ($this) {
            self::Student => 'student.dashboard',
            self::Counselor => 'counselor.dashboard',
            self::Admin => 'admin.dashboard',
        };
    }
}
