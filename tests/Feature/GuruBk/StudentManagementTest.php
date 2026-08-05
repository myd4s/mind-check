<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\GuruBk\StudentManagement;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function activeAcademicYear(): AcademicYear
    {
        return AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
    }

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.students'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_student_with_auto_provisioned_account(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = $this->activeAcademicYear();
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->call('create')
            ->set('name', 'Budi Santoso')
            ->set('nisn', '1234567890')
            ->set('gender', Gender::Male->value)
            ->set('school_class_id', $class->id)
            ->call('save');

        $this->assertDatabaseHas('students', ['nisn' => '1234567890']);

        $user = User::where('email', '1234567890@mindcheck.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->role === UserRole::Siswa);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('1234567890', $user->password));

        $student = Student::where('nisn', '1234567890')->first();
        $this->assertDatabaseHas('student_class_histories', [
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'school_class_id' => $class->id,
            'status' => 'aktif',
        ]);
    }

    public function test_cannot_create_student_without_active_academic_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->call('create')
            ->set('name', 'Budi Santoso')
            ->set('nisn', '1234567890')
            ->set('gender', Gender::Male->value)
            ->set('school_class_id', $class->id)
            ->call('save')
            ->assertStatus(422);

        $this->assertDatabaseMissing('students', ['nisn' => '1234567890']);
    }

    public function test_nisn_must_be_unique(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $this->activeAcademicYear();
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $existingUser = User::factory()->create(['role' => UserRole::Siswa]);
        Student::create(['user_id' => $existingUser->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->call('create')
            ->set('name', 'Budi Santoso')
            ->set('nisn', '1234567890')
            ->set('gender', Gender::Male->value)
            ->set('school_class_id', $class->id)
            ->call('save')
            ->assertHasErrors(['nisn']);
    }

    public function test_guru_bk_can_edit_student(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = $this->activeAcademicYear();
        $classA = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classB = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);

        $user = User::factory()->create(['name' => 'Budi Santoso', 'role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);
        $student->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $classA->id,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->call('edit', $student->id)
            ->set('name', 'Budi Santoso Updated')
            ->set('school_class_id', $classB->id)
            ->call('save');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Budi Santoso Updated']);
        $this->assertDatabaseHas('student_class_histories', [
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'school_class_id' => $classB->id,
        ]);
    }

    public function test_guru_bk_can_deactivate_student(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = $this->activeAcademicYear();
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $user = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);
        $history = $student->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $class->id,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->call('confirmDeactivate', $student->id)
            ->call('deactivate');

        $this->assertDatabaseHas('student_class_histories', [
            'id' => $history->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_cannot_delete_school_class_with_active_students(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = $this->activeAcademicYear();
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $user = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);
        $student->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $class->id,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($guruBk)
            ->test(\App\Livewire\GuruBk\SchoolClassManagement::class)
            ->call('confirmDelete', $class->id)
            ->call('delete');

        $this->assertDatabaseHas('school_classes', ['id' => $class->id]);
    }
}
