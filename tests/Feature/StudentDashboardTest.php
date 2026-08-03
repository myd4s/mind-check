<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\QuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(): Student
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $user = User::factory()->create();

        return Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '2026000002',
            'gender' => Gender::Male,
            'status' => 'active',
        ]);
    }

    public function test_dashboard_shows_empty_state_when_no_assessment_yet(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Belum Ada Data')
            ->assertSee('Mulai Asesmen')
            ->assertSee('Belum ada asesmen yang diselesaikan.');
    }

    public function test_dashboard_shows_latest_completed_assessment(): void
    {
        $student = $this->makeStudent();

        Assessment::create([
            'student_id' => $student->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
            'depression_raw' => 0,
            'anxiety_raw' => 0,
            'stress_raw' => 14,
            'depression_score' => 0,
            'anxiety_score' => 0,
            'stress_score' => 21,
            'depression_severity' => 'normal',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'moderate',
            'overall_severity' => 'moderate',
        ]);

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Sedang')
            ->assertSee('21')
            ->assertDontSee('Belum Ada Data');
    }

    public function test_dashboard_shows_progress_for_in_progress_assessment(): void
    {
        $this->seed(QuestionSeeder::class);

        $student = $this->makeStudent();

        $assessment = Assessment::create([
            'student_id' => $student->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        Question::orderBy('order_number')->take(7)->get()->each(
            fn (Question $question) => AssessmentAnswer::create([
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
                'answer_value' => 2,
            ])
        );

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('33%')
            ->assertSee('Lanjutkan Kuesioner');
    }
}
