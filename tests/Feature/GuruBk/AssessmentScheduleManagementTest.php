<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\AssessmentScheduleManagement;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.assessment-schedules'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_schedule_for_all_classes(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('create')
            ->set('title', 'Jadwal Ganjil')
            ->set('assessment_id', $assessment->id)
            ->set('academic_year_id', $year->id)
            ->set('start_at', '2026-08-01T08:00')
            ->set('end_at', '2026-08-15T23:59')
            ->set('target_type', 'all')
            ->call('save');

        $this->assertDatabaseHas('assessment_schedules', [
            'title' => 'Jadwal Ganjil',
            'target_type' => 'all',
        ]);
    }

    public function test_guru_bk_can_create_schedule_for_specific_classes(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $classA = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classB = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('create')
            ->set('title', 'Jadwal Kelas X')
            ->set('assessment_id', $assessment->id)
            ->set('academic_year_id', $year->id)
            ->set('start_at', '2026-08-01T08:00')
            ->set('end_at', '2026-08-15T23:59')
            ->set('target_type', 'specific')
            ->set("target_class_ids.{$classA->id}", true)
            ->call('save');

        $schedule = AssessmentSchedule::where('title', 'Jadwal Kelas X')->first();
        $this->assertNotNull($schedule);
        $this->assertTrue($schedule->appliesToClass($classA->id));
        $this->assertFalse($schedule->appliesToClass($classB->id));
    }

    public function test_specific_target_requires_at_least_one_class(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('create')
            ->set('title', 'Jadwal Tanpa Kelas')
            ->set('assessment_id', $assessment->id)
            ->set('academic_year_id', $year->id)
            ->set('start_at', '2026-08-01T08:00')
            ->set('end_at', '2026-08-15T23:59')
            ->set('target_type', 'specific')
            ->call('save')
            ->assertHasErrors(['target_class_ids']);

        $this->assertDatabaseMissing('assessment_schedules', ['title' => 'Jadwal Tanpa Kelas']);
    }

    public function test_end_at_must_be_after_start_at(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('create')
            ->set('title', 'Jadwal Salah')
            ->set('assessment_id', $assessment->id)
            ->set('academic_year_id', $year->id)
            ->set('start_at', '2026-08-15T08:00')
            ->set('end_at', '2026-08-01T08:00')
            ->set('target_type', 'all')
            ->call('save')
            ->assertHasErrors(['end_at']);
    }

    public function test_schedule_outside_time_window_is_not_open(): void
    {
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);

        $futureSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal Masa Depan',
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(10),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        $pastSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal Lampau',
            'start_at' => now()->subDays(10),
            'end_at' => now()->subDays(5),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        $openSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal Berlangsung',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDay(),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        $this->assertFalse($futureSchedule->isOpenNow());
        $this->assertFalse($pastSchedule->isOpenNow());
        $this->assertTrue($openSchedule->isOpenNow());
    }

    public function test_guru_bk_can_delete_schedule(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);

        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal Dihapus',
            'start_at' => now(),
            'end_at' => now()->addDay(),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('confirmDelete', $schedule->id)
            ->call('delete');

        $this->assertDatabaseMissing('assessment_schedules', ['id' => $schedule->id]);
    }
}
