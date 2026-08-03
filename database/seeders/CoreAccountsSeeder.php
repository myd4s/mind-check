<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoreAccountsSeeder extends Seeder
{
    /**
     * Seed satu akun awal per role agar sistem bisa langsung dicoba login.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mindcheck.test'],
            [
                'name' => 'Admin MindCheck',
                'password' => 'password',
                'role' => UserRole::Admin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'konselor@mindcheck.test'],
            [
                'name' => 'Bu Sari (Guru BK)',
                'password' => 'password',
                'role' => UserRole::Counselor,
            ]
        );

        // Akun siswa contoh untuk verifikasi alur login; profil lengkap (kelas, NIS)
        // ditambahkan oleh StudentSeeder pada tahap data inti.
        User::updateOrCreate(
            ['email' => 'siswa@mindcheck.test'],
            [
                'name' => 'Siswa Contoh',
                'password' => 'password',
                'role' => UserRole::Student,
            ]
        );
    }
}
