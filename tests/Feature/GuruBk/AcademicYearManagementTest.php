<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\AcademicYearManagement;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AcademicYearManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.academic-years'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_academic_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AcademicYearManagement::class)
            ->call('create')
            ->set('name', '2026/2027')
            ->set('start_date', '2026-07-01')
            ->set('end_date', '2027-06-30')
            ->set('is_active', true)
            ->call('save');

        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'is_active' => true,
        ]);
    }

    public function test_activating_one_year_deactivates_others(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $oldYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        Livewire::actingAs($guruBk)
            ->test(AcademicYearManagement::class)
            ->call('create')
            ->set('name', '2026/2027')
            ->set('start_date', '2026-07-01')
            ->set('end_date', '2027-06-30')
            ->set('is_active', true)
            ->call('save');

        $this->assertFalse($oldYear->fresh()->is_active);
        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'is_active' => true,
        ]);
    }

    public function test_guru_bk_can_update_academic_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => false,
        ]);

        Livewire::actingAs($guruBk)
            ->test(AcademicYearManagement::class)
            ->call('edit', $year->id)
            ->set('name', '2026/2027 (Revisi)')
            ->call('save');

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'name' => '2026/2027 (Revisi)',
        ]);
    }

    public function test_guru_bk_can_delete_academic_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => false,
        ]);

        Livewire::actingAs($guruBk)
            ->test(AcademicYearManagement::class)
            ->call('confirmDelete', $year->id)
            ->call('delete');

        $this->assertDatabaseMissing('academic_years', ['id' => $year->id]);
    }

    public function test_end_date_must_be_after_start_date(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AcademicYearManagement::class)
            ->call('create')
            ->set('name', '2026/2027')
            ->set('start_date', '2026-07-01')
            ->set('end_date', '2026-01-01')
            ->call('save')
            ->assertHasErrors(['end_date']);
    }
}
