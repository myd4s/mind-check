<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\GuruBk\ClassPromotion;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClassPromotionTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $nisn, AcademicYear $year, SchoolClass $class, string $status = 'aktif'): Student
    {
        $user = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => $nisn, 'gender' => Gender::Male]);
        $student->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $class->id,
            'status' => $status,
        ]);

        return $student;
    }

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.class-promotion'))
            ->assertForbidden();
    }

    public function test_promotes_students_to_mapped_class_in_target_year_without_losing_old_history(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $oldYear = AcademicYear::create([
            'name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => false,
        ]);
        $newYear = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true,
        ]);

        $classX = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classXI = SchoolClass::create(['name' => 'XI IPA 1', 'grade_level' => 'XI']);

        $student = $this->makeStudent('1111111111', $oldYear, $classX);

        Livewire::actingAs($guruBk)
            ->test(ClassPromotion::class)
            ->set('sourceYearId', $oldYear->id)
            ->set('targetYearId', $newYear->id)
            ->set("mappings.{$classX->id}", (string) $classXI->id)
            ->call('promote');

        // Histori lama tetap ada & tidak berubah.
        $this->assertDatabaseHas('student_class_histories', [
            'student_id' => $student->id,
            'academic_year_id' => $oldYear->id,
            'school_class_id' => $classX->id,
        ]);

        // Histori baru dibuat di tahun tujuan dengan kelas yang dipetakan.
        $this->assertDatabaseHas('student_class_histories', [
            'student_id' => $student->id,
            'academic_year_id' => $newYear->id,
            'school_class_id' => $classXI->id,
            'status' => 'aktif',
        ]);

        $this->assertSame(2, $student->classHistories()->count());
    }

    public function test_students_marked_lulus_do_not_get_new_history(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $oldYear = AcademicYear::create([
            'name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => false,
        ]);
        $newYear = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true,
        ]);

        $classXII = SchoolClass::create(['name' => 'XII IPA 1', 'grade_level' => 'XII']);

        $student = $this->makeStudent('2222222222', $oldYear, $classXII);

        Livewire::actingAs($guruBk)
            ->test(ClassPromotion::class)
            ->set('sourceYearId', $oldYear->id)
            ->set('targetYearId', $newYear->id)
            ->set("mappings.{$classXII->id}", 'lulus')
            ->call('promote');

        $this->assertSame(1, $student->classHistories()->count());
        $this->assertDatabaseMissing('student_class_histories', [
            'student_id' => $student->id,
            'academic_year_id' => $newYear->id,
        ]);
    }

    public function test_source_and_target_year_must_differ(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $year = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true,
        ]);

        Livewire::actingAs($guruBk)
            ->test(ClassPromotion::class)
            ->set('sourceYearId', $year->id)
            ->set('targetYearId', $year->id)
            ->call('promote')
            ->assertHasErrors(['targetYearId']);
    }

    public function test_running_promotion_twice_does_not_duplicate_history(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $oldYear = AcademicYear::create([
            'name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => false,
        ]);
        $newYear = AcademicYear::create([
            'name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true,
        ]);

        $classX = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classXI = SchoolClass::create(['name' => 'XI IPA 1', 'grade_level' => 'XI']);

        $student = $this->makeStudent('1111111111', $oldYear, $classX);

        $component = Livewire::actingAs($guruBk)->test(ClassPromotion::class)
            ->set('sourceYearId', $oldYear->id)
            ->set('targetYearId', $newYear->id)
            ->set("mappings.{$classX->id}", (string) $classXI->id);

        $component->call('promote');
        $component->call('promote');

        $this->assertSame(2, $student->classHistories()->count());
    }
}
