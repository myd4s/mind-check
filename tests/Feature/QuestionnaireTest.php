<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Severity;
use App\Livewire\Questionnaire\Wizard;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionnaireTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $email = 'siswa-test@mindcheck.test'): Student
    {
        $this->seed(\Database\Seeders\QuestionSeeder::class);
        $this->seed(\Database\Seeders\RecommendationSeeder::class);

        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $user = User::factory()->create(['email' => $email]);

        return Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => fake()->unique()->numerify('##########'),
            'gender' => Gender::Male,
            'status' => 'active',
        ]);
    }

    public function test_student_can_complete_questionnaire_and_see_scored_result(): void
    {
        $student = $this->makeStudent();
        $this->actingAs($student->user);

        $component = Livewire::test(Wizard::class);

        // Depresi = 0 (Normal), Kecemasan = 4 (Extremely Severe), Stres = 2 (Moderate)
        $answerFor = [
            'depression' => 0,
            'anxiety' => 4,
            'stress' => 2,
        ];

        foreach (Question::orderBy('order_number')->get() as $question) {
            $component->call('selectAnswer', $question->id, $answerFor[$question->subscale->value]);
        }

        $component->call('submit');

        $assessment = Assessment::where('student_id', $student->id)->latest()->firstOrFail();

        $component->assertRedirect(route('student.result', $assessment));

        $this->assertSame(0, $assessment->depression_score);
        $this->assertSame(Severity::Normal, $assessment->depression_severity);

        $this->assertSame(42, $assessment->anxiety_score);
        $this->assertSame(Severity::ExtremelySevere, $assessment->anxiety_severity);

        $this->assertSame(21, $assessment->stress_score);
        $this->assertSame(Severity::Moderate, $assessment->stress_severity);

        $this->assertSame(Severity::ExtremelySevere, $assessment->overall_severity);

        $this->get(route('student.result', $assessment))
            ->assertOk()
            ->assertSee('Sangat Parah')
            ->assertSee('Normal')
            ->assertSee('Sedang');
    }

    public function test_submit_fails_when_not_all_questions_answered(): void
    {
        $student = $this->makeStudent();
        $this->actingAs($student->user);

        $component = Livewire::test(Wizard::class);

        $firstQuestion = Question::orderBy('order_number')->first();
        $component->call('selectAnswer', $firstQuestion->id, 2);

        $component->call('submit');

        $component->assertHasErrors('form');

        $assessment = Assessment::where('student_id', $student->id)->firstOrFail();
        $this->assertNull($assessment->completed_at);
    }

    public function test_student_cannot_view_another_students_result(): void
    {
        $studentA = $this->makeStudent('a@mindcheck.test');
        $studentB = $this->makeStudent('b@mindcheck.test');

        $assessment = Assessment::create([
            'student_id' => $studentA->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
            'depression_raw' => 0,
            'anxiety_raw' => 0,
            'stress_raw' => 0,
            'depression_score' => 0,
            'anxiety_score' => 0,
            'stress_score' => 0,
            'depression_severity' => 'normal',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'normal',
            'overall_severity' => 'normal',
        ]);

        $this->actingAs($studentB->user)
            ->get(route('student.result', $assessment))
            ->assertForbidden();
    }
}
