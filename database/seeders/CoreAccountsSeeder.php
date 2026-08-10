<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoreAccountsSeeder extends Seeder
{
    /**
     * Seed satu akun dummy per role untuk kebutuhan testing manual.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mindcare.com'],
            [
                'name' => 'Admin MindCheck',
                'password' => 'password',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'gurubk@mindcare.com'],
            [
                'name' => 'Guru BK Dummy',
                'password' => 'password',
                'role' => UserRole::GuruBk,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'siswa@mindcare.com'],
            [
                'name' => 'Siswa Dummy',
                'password' => 'password',
                'role' => UserRole::Siswa,
                'email_verified_at' => now(),
            ]
        );
    }
}
