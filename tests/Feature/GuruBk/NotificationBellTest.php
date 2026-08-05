<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class NotificationBellTest extends TestCase
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

    public function test_bell_shows_only_high_category_results_ordered_by_most_recent(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $this->makeStudentWithResult($year, $class, $schedule, '1111111111', 'rendah', 10, now()->subDays(3));
        $older = $this->makeStudentWithResult($year, $class, $schedule, '2222222222', 'tinggi', 30, now()->subDays(2));
        $newest = $this->makeStudentWithResult($year, $class, $schedule, '3333333333', 'tinggi', 32, now());

        $component = $this->actingAs($guruBk)
            ->get('/dashboard')
            ->assertOk();

        $component->assertSeeVolt('layout.navigation');

        $volt = Volt::test('layout.navigation');
        $notifications = $volt->get('highCategoryNotifications');

        $this->assertCount(2, $notifications);
        $this->assertSame($newest->id, $notifications->first()->student_id);
        $this->assertSame($older->id, $notifications->last()->student_id);
    }

    public function test_bell_notification_links_to_correct_result_detail(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $this->makeStudentWithResult($year, $class, $schedule, '1111111111', 'tinggi', 30);
        $result = AssessmentResult::where('category', 'tinggi')->firstOrFail();

        $this->actingAs($guruBk)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('siswa.result-detail', $result), false);
    }

    public function test_students_do_not_see_notification_bell_data(): void
    {
        $siswaUser = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $siswaUser->id, 'nisn' => '9999999999', 'gender' => Gender::Male]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $student->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'status' => 'aktif']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);
        AssessmentResult::create([
            'student_id' => $student->id,
            'assessment_schedule_id' => $schedule->id,
            'total_score' => 30,
            'category' => 'tinggi',
            'completed_at' => now(),
        ]);

        $volt = Volt::actingAs($siswaUser)->test('layout.navigation');
        $this->assertCount(0, $volt->get('highCategoryNotifications'));
    }

    public function test_bell_limits_to_eight_most_recent_high_category_results(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        foreach (range(1, 10) as $i) {
            $this->makeStudentWithResult($year, $class, $schedule, str_pad((string) $i, 10, '0', STR_PAD_LEFT), 'tinggi', 30, now()->subMinutes($i));
        }

        $volt = Volt::actingAs($guruBk)->test('layout.navigation');
        $this->assertCount(8, $volt->get('highCategoryNotifications'));
    }
}
