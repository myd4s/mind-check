<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresi: currentClassHistory() sebelumnya pakai latestOfMany(), yang
     * menghitung MAX(id) SEBELUM filter tahun ajaran aktif diterapkan. Kalau
     * baris ID tertinggi (mis. hasil kenaikan kelas ke tahun ajaran depan
     * yang belum aktif) tidak lolos filter, seluruh relasi mengembalikan
     * null — walau ada baris valid di tahun ajaran yang sedang aktif.
     */
    public function test_current_class_history_resolves_active_year_even_when_a_newer_row_exists_for_a_future_year(): void
    {
        $activeYear = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true,
        ]);
        $futureYear = AcademicYear::create([
            'name' => '2027/2028', 'start_date' => '2027-07-01', 'end_date' => '2028-06-30', 'is_active' => false,
        ]);

        $classNow = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classFuture = SchoolClass::create(['name' => 'XI IPA 1', 'grade_level' => 'XI']);

        $user = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);

        // Baris tahun aktif dibuat LEBIH DULU (id lebih kecil).
        $student->classHistories()->create([
            'academic_year_id' => $activeYear->id, 'school_class_id' => $classNow->id, 'status' => 'aktif',
        ]);
        // Baris kenaikan kelas ke tahun depan (belum aktif) dibuat BELAKANGAN (id lebih besar).
        $student->classHistories()->create([
            'academic_year_id' => $futureYear->id, 'school_class_id' => $classFuture->id, 'status' => 'aktif',
        ]);

        $current = $student->fresh()->currentClassHistory;

        $this->assertNotNull($current);
        $this->assertSame($classNow->id, $current->school_class_id);
        $this->assertSame($activeYear->id, $current->academic_year_id);
    }
}
