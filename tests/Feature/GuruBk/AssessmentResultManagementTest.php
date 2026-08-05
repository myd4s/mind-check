<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\GuruBk\AssessmentResultManagement;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentResultManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.results'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_filter_results_by_class_schedule_and_category(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $classA = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classB = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);

        $scheduleA = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);
        $scheduleB = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal B',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $studentA = Student::create(['user_id' => User::factory()->create(['role' => UserRole::Siswa])->id, 'nisn' => '1111111111', 'gender' => Gender::Male]);
        $studentA->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $classA->id, 'status' => 'aktif']);

        $studentB = Student::create(['user_id' => User::factory()->create(['role' => UserRole::Siswa])->id, 'nisn' => '2222222222', 'gender' => Gender::Female]);
        $studentB->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $classB->id, 'status' => 'aktif']);

        AssessmentResult::create(['student_id' => $studentA->id, 'assessment_schedule_id' => $scheduleA->id, 'total_score' => 10, 'category' => 'rendah', 'completed_at' => now()]);
        AssessmentResult::create(['student_id' => $studentB->id, 'assessment_schedule_id' => $scheduleB->id, 'total_score' => 30, 'category' => 'tinggi', 'completed_at' => now()]);

        $component = Livewire::actingAs($guruBk)->test(AssessmentResultManagement::class);
        $this->assertCount(2, $component->get('results'));

        $component->set('classFilter', $classA->id);
        $this->assertCount(1, $component->get('results'));
        $this->assertSame($studentA->id, $component->get('results')->first()->student_id);

        $component->set('classFilter', '');
        $component->set('categoryFilter', 'tinggi');
        $this->assertCount(1, $component->get('results'));
        $this->assertSame('tinggi', $component->get('results')->first()->category);

        $component->set('categoryFilter', '');
        $component->set('scheduleFilter', $scheduleA->id);
        $this->assertCount(1, $component->get('results'));
        $this->assertSame($scheduleA->id, $component->get('results')->first()->assessment_schedule_id);
    }
}
