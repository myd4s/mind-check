<?php

namespace Tests\Feature\Admin;

use App\Enums\Gender;
use App\Livewire\Admin\StudentManagement;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $name, SchoolClass $class, string $gender = 'L'): Student
    {
        $user = User::factory()->create(['name' => $name]);

        return Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => fake()->unique()->numerify('##########'),
            'gender' => $gender,
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_student_management(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('admin.students'))
            ->assertForbidden();
    }

    public function test_admin_can_view_student_list(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $this->makeStudent('Ani Lestari', $class);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.students'))
            ->assertOk()
            ->assertSee('Ani Lestari');
    }

    public function test_search_filters_students_by_name(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $this->makeStudent('Ani Lestari', $class);
        $this->makeStudent('Budi Santoso', $class);

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->set('search', 'Budi')
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ani Lestari');
    }

    public function test_gender_filter_narrows_results(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $this->makeStudent('Ani Lestari', $class, 'P');
        $this->makeStudent('Budi Santoso', $class, 'L');

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->set('genderFilter', 'P')
            ->assertSee('Ani Lestari')
            ->assertDontSee('Budi Santoso');
    }

    public function test_admin_can_create_student(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->call('openCreateModal')
            ->set('form.name', 'Citra Dewi')
            ->set('form.email', 'citra@mindcheck.test')
            ->set('form.nis', '2026010010')
            ->set('form.class_id', $class->id)
            ->set('form.gender', 'P')
            ->set('form.status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'citra@mindcheck.test', 'role' => 'student']);
        $student = Student::where('nis', '2026010010')->firstOrFail();
        $this->assertTrue(Hash::check('2026010010', $student->user->password));
    }

    public function test_admin_can_edit_student(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $otherClass = SchoolClass::create(['name' => 'X IPA 2']);
        $student = $this->makeStudent('Dedi Kurniawan', $class);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->call('openEditModal', $student->id)
            ->set('form.class_id', $otherClass->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($otherClass->id, $student->fresh()->class_id);
    }

    public function test_admin_can_delete_student(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $student = $this->makeStudent('Eka Putri', $class);
        $userId = $student->user_id;
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->call('confirmDelete', $student->id)
            ->call('delete');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_creating_student_with_duplicate_nis_fails_validation(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $existing = $this->makeStudent('Existing Student', $class);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(StudentManagement::class)
            ->call('openCreateModal')
            ->set('form.name', 'New Student')
            ->set('form.email', 'new@mindcheck.test')
            ->set('form.nis', $existing->nis)
            ->set('form.class_id', $class->id)
            ->set('form.gender', 'L')
            ->set('form.status', 'active')
            ->call('save')
            ->assertHasErrors('form.nis');
    }
}
