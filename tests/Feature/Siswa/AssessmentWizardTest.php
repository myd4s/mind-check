<?php

namespace Tests\Feature\Siswa;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\Siswa\AssessmentWizard;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentWizardTest extends TestCase
{
    use RefreshDatabase;

    private array $reverseFlags = [false, false, false, true, true, false, true, true, false, false];

    private function makeSchedule(): array
    {
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $assessment = Assessment::create(['title' => 'Paket PSS-10']);
        $questions = [];
        foreach ($this->reverseFlags as $i => $reverse) {
            $q = Question::create([
                'text' => 'Soal '.($i + 1), 'order' => $i + 1, 'reverse_scored' => $reverse, 'is_active' => true, 'is_core' => true,
            ]);
            $assessment->questions()->attach($q->id, ['order' => $q->order]);
            $questions[] = $q;
        }

        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal Uji Coba',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDay(),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        $userSiswa = User::factory()->create(['role' => UserRole::Siswa]);
        $student = Student::create(['user_id' => $userSiswa->id, 'nisn' => '1234567890', 'gender' => Gender::Male]);
        $student->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $class->id,
            'status' => 'aktif',
        ]);

        return compact('schedule', 'questions', 'userSiswa', 'student', 'class');
    }

    public function test_student_can_complete_wizard_and_gets_correct_score(): void
    {
        ['schedule' => $schedule, 'questions' => $questions, 'userSiswa' => $userSiswa] = $this->makeSchedule();

        $component = Livewire::actingAs($userSiswa)->test(AssessmentWizard::class, ['schedule' => $schedule]);

        // Jawab semua soal dengan nilai 2 -> total seharusnya 20 (sedang), sesuai kalkulasi manual di Pss10ScoringServiceTest.
        foreach ($questions as $question) {
            $component->call('selectAnswer', $question->id, 2);
            $component->call('next');
        }

        $component->call('submit');

        $this->assertDatabaseHas('assessment_results', [
            'student_id' => Student::first()->id,
            'assessment_schedule_id' => $schedule->id,
            'total_score' => 20,
            'category' => 'sedang',
        ]);

        $result = AssessmentResult::first();
        $this->assertSame(10, $result->answers()->count());
    }

    public function test_cannot_submit_twice_for_the_same_schedule(): void
    {
        ['schedule' => $schedule, 'questions' => $questions, 'userSiswa' => $userSiswa, 'student' => $student] = $this->makeSchedule();

        AssessmentResult::create([
            'student_id' => $student->id,
            'assessment_schedule_id' => $schedule->id,
            'total_score' => 20,
            'category' => 'sedang',
            'completed_at' => now(),
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($userSiswa)
            ->test(AssessmentWizard::class, ['schedule' => $schedule])
            ->call('submit');
    }

    public function test_cannot_submit_outside_time_window(): void
    {
        ['schedule' => $schedule, 'userSiswa' => $userSiswa] = $this->makeSchedule();

        $schedule->update(['start_at' => now()->subDays(10), 'end_at' => now()->subDays(5)]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($userSiswa)
            ->test(AssessmentWizard::class, ['schedule' => $schedule])
            ->call('submit');
    }

    public function test_submit_fails_if_not_all_questions_answered(): void
    {
        ['schedule' => $schedule, 'questions' => $questions, 'userSiswa' => $userSiswa] = $this->makeSchedule();

        $component = Livewire::actingAs($userSiswa)->test(AssessmentWizard::class, ['schedule' => $schedule]);
        $component->call('selectAnswer', $questions[0]->id, 2);
        $component->call('submit');

        $this->assertDatabaseMissing('assessment_results', ['assessment_schedule_id' => $schedule->id]);
    }

    public function test_student_from_other_class_cannot_access_specific_schedule(): void
    {
        ['schedule' => $schedule, 'class' => $class] = $this->makeSchedule();

        $schedule->update(['target_type' => 'specific']);
        $otherClass = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);
        $schedule->targetClasses()->sync([$class->id]);

        $year = AcademicYear::where('is_active', true)->first();
        $userOther = User::factory()->create(['role' => UserRole::Siswa]);
        $otherStudent = Student::create(['user_id' => $userOther->id, 'nisn' => '9999999999', 'gender' => Gender::Female]);
        $otherStudent->classHistories()->create([
            'academic_year_id' => $year->id,
            'school_class_id' => $otherClass->id,
            'status' => 'aktif',
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($userOther)->test(AssessmentWizard::class, ['schedule' => $schedule]);
    }
}
