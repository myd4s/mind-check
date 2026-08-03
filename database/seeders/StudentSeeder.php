<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Lengkapi profil siswa untuk akun contoh yang dibuat di CoreAccountsSeeder,
     * supaya akun tersebut langsung bisa dipakai menjalankan alur kuesioner.
     */
    public function run(): void
    {
        $user = User::where('email', 'siswa@mindcheck.test')->first();
        $class = SchoolClass::where('name', 'X IPA 1')->first();

        if (! $user || ! $class) {
            return;
        }

        Student::updateOrCreate(
            ['user_id' => $user->id],
            [
                'class_id' => $class->id,
                'nis' => '2026010001',
                'gender' => Gender::Male,
                'status' => 'active',
            ]
        );
    }
}
