<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RecommendationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentPdfTest extends TestCase
{
    use RefreshDatabase;

    private function makeAssessment(): Assessment
    {
        $this->seed(RecommendationSeeder::class);

        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '2026099999',
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
            'depression_severity' => 'moderate', 'anxiety_severity' => 'mild', 'stress_severity' => 'mild',
            'overall_severity' => 'moderate',
        ]);
    }

    public function test_student_can_download_own_result_pdf(): void
    {
        $assessment = $this->makeAssessment();

        $response = $this->actingAs($assessment->student->user)
            ->get(route('student.result.pdf', $assessment));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }

    public function test_student_cannot_download_another_students_pdf(): void
    {
        $assessment = $this->makeAssessment();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('student.result.pdf', $assessment))
            ->assertForbidden();
    }

    public function test_counselor_can_download_assessment_pdf(): void
    {
        $assessment = $this->makeAssessment();
        $counselor = User::factory()->counselor()->create();

        $response = $this->actingAs($counselor)
            ->get(route('assessment.pdf', $assessment));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }
}
