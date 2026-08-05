<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\GuruBk\Dashboard;
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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentWithResult(AcademicYear $year, SchoolClass $class, AssessmentSchedule $schedule, string $nisn, string $category, int $score, ?\DateTimeInterface $completedAt = null): Student
    {
        $user = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => $nisn, 'gender' => Gender::Male]);
        $student->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'status' => 'aktif']);

        AssessmentResult::create([
            'student_id' => $student->id,
            'assessment_schedule_id' => $schedule->id,
            'total_score' => $score,
            'category' => $category,
            'completed_at' => $completedAt ?? now(),
        ]);

        return $student;
    }

    public function test_dashboard_shows_accurate_category_distribution_for_latest_result_per_student(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $studentA = $this->makeStudentWithResult($year, $class, $schedule, '1111111111', 'rendah', 10, now()->subDays(2));

        // Siswa B punya 2 hasil dari jadwal berbeda — hanya yang TERBARU yang harus dihitung.
        $secondSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal B',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);
        $studentB = $this->makeStudentWithResult($year, $class, $schedule, '2222222222', 'sedang', 20, now()->subDays(5));
        AssessmentResult::create([
            'student_id' => $studentB->id,
            'assessment_schedule_id' => $secondSchedule->id,
            'total_score' => 30,
            'category' => 'tinggi',
            'completed_at' => now(),
        ]);

        $component = Livewire::actingAs($guruBk)->test(Dashboard::class);

        $counts = $component->get('categoryCounts');
        $this->assertSame(1, $counts['rendah']);
        $this->assertSame(0, $counts['sedang']); // studentB terhitung 'tinggi' (hasil terbaru), bukan 'sedang'.
        $this->assertSame(1, $counts['tinggi']);

        $highStudents = $component->get('highCategoryStudents');
        $this->assertCount(1, $highStudents);
        $this->assertSame($studentB->id, $highStudents->first()->student_id);
    }

    public function test_class_filter_narrows_results_without_full_reload(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $classA = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classB = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $this->makeStudentWithResult($year, $classA, $schedule, '1111111111', 'tinggi', 30);
        $this->makeStudentWithResult($year, $classB, $schedule, '2222222222', 'tinggi', 32);

        $component = Livewire::actingAs($guruBk)->test(Dashboard::class);
        $this->assertCount(2, $component->get('latestResultsPerStudent'));

        $component->set('classFilter', $classA->id);
        $this->assertCount(1, $component->get('latestResultsPerStudent'));
    }

    public function test_academic_year_filter_isolates_results_to_that_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $oldYear = AcademicYear::create(['name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => false]);
        $newYear = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);

        $oldSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $oldYear->id, 'title' => 'Jadwal Lama',
            'start_at' => now()->subYear(), 'end_at' => now()->subYear()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);
        $newSchedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $newYear->id, 'title' => 'Jadwal Baru',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $this->makeStudentWithResult($oldYear, $class, $oldSchedule, '1111111111', 'tinggi', 30);
        $this->makeStudentWithResult($newYear, $class, $newSchedule, '2222222222', 'rendah', 5);

        $component = Livewire::actingAs($guruBk)->test(Dashboard::class);

        // Default: tahun aktif (baru).
        $this->assertCount(1, $component->get('latestResultsPerStudent'));
        $this->assertSame('rendah', $component->get('latestResultsPerStudent')->first()->category);

        $component->set('academicYearFilter', $oldYear->id);
        $this->assertCount(1, $component->get('latestResultsPerStudent'));
        $this->assertSame('tinggi', $component->get('latestResultsPerStudent')->first()->category);
    }
}
