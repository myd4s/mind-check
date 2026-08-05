<?php

namespace Tests\Feature\Siswa;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\Shared\AssessmentResultDetail;
use App\Livewire\Siswa\AssessmentHistory;
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

class AssessmentResultDetailTest extends TestCase
{
    use RefreshDatabase;

    private function makeResult(): array
    {
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $question = Question::create(['text' => 'Soal 1', 'order' => 1, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true]);
        $assessment->questions()->attach($question->id, ['order' => 1]);

        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id,
            'academic_year_id' => $year->id,
            'title' => 'Jadwal A',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDay(),
            'target_type' => 'all',
            'is_active' => true,
        ]);

        $ownerUser = User::factory()->create(['role' => UserRole::Siswa, 'name' => 'Siswa Pemilik']);
        $owner = Student::create(['user_id' => $ownerUser->id, 'nisn' => '1111111111', 'gender' => Gender::Male]);
        $owner->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'status' => 'aktif']);

        $result = AssessmentResult::create([
            'student_id' => $owner->id,
            'assessment_schedule_id' => $schedule->id,
            'total_score' => 20,
            'category' => 'sedang',
            'completed_at' => now(),
        ]);
        $result->answers()->create(['question_id' => $question->id, 'answer_value' => 2]);

        return compact('result', 'ownerUser', 'owner');
    }

    public function test_owner_can_view_own_result_detail(): void
    {
        ['result' => $result, 'ownerUser' => $ownerUser] = $this->makeResult();

        Livewire::actingAs($ownerUser)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->assertOk()
            ->assertSee('20')
            ->assertSee('Sedang');
    }

    public function test_other_student_cannot_view_someone_elses_result(): void
    {
        ['result' => $result] = $this->makeResult();

        $otherUser = User::factory()->create(['role' => UserRole::Siswa]);
        Student::create(['user_id' => $otherUser->id, 'nisn' => '2222222222', 'gender' => Gender::Female]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($otherUser)->test(AssessmentResultDetail::class, ['result' => $result]);
    }

    public function test_guru_bk_can_view_any_students_result(): void
    {
        ['result' => $result] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->assertOk()
            ->assertSee('Siswa Pemilik');
    }

    public function test_student_history_only_shows_own_results(): void
    {
        ['result' => $result, 'ownerUser' => $ownerUser] = $this->makeResult();

        $otherUser = User::factory()->create(['role' => UserRole::Siswa]);
        $otherStudent = Student::create(['user_id' => $otherUser->id, 'nisn' => '3333333333', 'gender' => Gender::Female]);
        AssessmentResult::create([
            'student_id' => $otherStudent->id,
            'assessment_schedule_id' => $result->assessment_schedule_id,
            'total_score' => 30,
            'category' => 'tinggi',
            'completed_at' => now(),
        ]);

        $component = Livewire::actingAs($ownerUser)->test(AssessmentHistory::class);

        $this->assertCount(1, $component->get('results'));
    }
}
