<?php

namespace Tests\Feature;

use App\Enums\FollowUpStatus;
use App\Enums\Gender;
use App\Models\Assessment;
use App\Models\FollowUp;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounselorAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentWithAssessment(string $overallSeverity = 'severe'): Assessment
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => fake()->unique()->numerify('##########'),
            'gender' => Gender::Male,
            'status' => 'active',
        ]);

        return Assessment::create([
            'student_id' => $student->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
            'depression_raw' => 10, 'anxiety_raw' => 10, 'stress_raw' => 10,
            'depression_score' => 15, 'anxiety_score' => 15, 'stress_score' => 15,
            'depression_severity' => 'moderate', 'anxiety_severity' => 'severe', 'stress_severity' => 'mild',
            'overall_severity' => $overallSeverity,
        ]);
    }

    public function test_admin_dashboard_shows_kpi_counts(): void
    {
        $this->makeStudentWithAssessment('severe');
        $this->makeStudentWithAssessment('normal');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Total Siswa')
            ->assertSee('Siswa Perlu Perhatian');
    }

    public function test_counselor_dashboard_lists_students_needing_attention(): void
    {
        $assessment = $this->makeStudentWithAssessment('extremely_severe');
        $this->makeStudentWithAssessment('normal');

        $counselor = User::factory()->counselor()->create();

        $this->actingAs($counselor)
            ->get(route('counselor.dashboard'))
            ->assertOk()
            ->assertSee($assessment->student->user->name)
            ->assertSee('Belum Ditangani');
    }

    public function test_counselor_can_view_assessment_detail_and_submit_follow_up(): void
    {
        $assessment = $this->makeStudentWithAssessment('severe');
        $counselor = User::factory()->counselor()->create();

        $this->actingAs($counselor)
            ->get(route('assessment.show', $assessment))
            ->assertOk()
            ->assertSee($assessment->student->user->name);

        $this->actingAs($counselor)
            ->post(route('assessment.follow-up.store', $assessment), [
                'status' => 'sedang_ditangani',
                'notes' => 'Sudah dihubungi orang tua.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('follow_ups', [
            'assessment_id' => $assessment->id,
            'counselor_id' => $counselor->id,
            'status' => FollowUpStatus::SedangDitangani->value,
            'notes' => 'Sudah dihubungi orang tua.',
        ]);
    }

    public function test_admin_can_also_view_assessment_detail(): void
    {
        $assessment = $this->makeStudentWithAssessment('severe');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('assessment.show', $assessment))
            ->assertOk();
    }

    public function test_student_cannot_access_assessment_detail_route(): void
    {
        $assessment = $this->makeStudentWithAssessment('severe');
        $otherStudentUser = User::factory()->create();

        $this->actingAs($otherStudentUser)
            ->get(route('assessment.show', $assessment))
            ->assertForbidden();
    }

    public function test_viewing_incomplete_assessment_detail_returns_404(): void
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $student = Student::create([
            'user_id' => User::factory()->create()->id,
            'class_id' => $class->id,
            'nis' => '2026000099',
            'gender' => Gender::Male,
            'status' => 'active',
        ]);

        $assessment = Assessment::create(['student_id' => $student->id, 'status' => 'in_progress', 'started_at' => now()]);

        $counselor = User::factory()->counselor()->create();

        $this->actingAs($counselor)
            ->get(route('assessment.show', $assessment))
            ->assertNotFound();
    }
}
