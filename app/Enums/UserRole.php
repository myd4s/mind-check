<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case GuruBk = 'guru_bk';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::GuruBk => 'Guru BK',
            self::Siswa => 'Siswa',
        };
    }

    /**
     * Level hierarki akses: Admin ⊇ Guru BK ⊇ Siswa (PRD §2).
     */
    public function level(): int
    {
        return match ($this) {
            self::Admin => 3,
            self::GuruBk => 2,
            self::Siswa => 1,
        };
    }

    public function hasAccessTo(self $required): bool
    {
        return $this->level() >= $required->level();
    }
}
