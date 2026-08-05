<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\SchoolClassManagement;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolClassManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.school-classes'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_school_class(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(SchoolClassManagement::class)
            ->call('create')
            ->set('name', 'X IPA 1')
            ->set('grade_level', 'X')
            ->call('save');

        $this->assertDatabaseHas('school_classes', [
            'name' => 'X IPA 1',
            'grade_level' => 'X',
        ]);
    }

    public function test_guru_bk_can_update_school_class(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        Livewire::actingAs($guruBk)
            ->test(SchoolClassManagement::class)
            ->call('edit', $class->id)
            ->set('name', 'X IPA 2')
            ->call('save');

        $this->assertDatabaseHas('school_classes', [
            'id' => $class->id,
            'name' => 'X IPA 2',
        ]);
    }

    public function test_guru_bk_can_delete_school_class(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        Livewire::actingAs($guruBk)
            ->test(SchoolClassManagement::class)
            ->call('confirmDelete', $class->id)
            ->call('delete');

        $this->assertDatabaseMissing('school_classes', ['id' => $class->id]);
    }

    public function test_name_and_grade_level_are_required(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(SchoolClassManagement::class)
            ->call('create')
            ->set('name', '')
            ->set('grade_level', '')
            ->call('save')
            ->assertHasErrors(['name', 'grade_level']);
    }
}
