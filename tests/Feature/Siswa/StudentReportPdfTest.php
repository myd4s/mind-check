<?php

namespace Tests\Feature\Siswa;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\ResultNote;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReportPdfTest extends TestCase
{
    use RefreshDatabase;

    private function makeResult(): array
    {
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $ownerUser = User::factory()->create(['role' => UserRole::Siswa, 'name' => 'Siswa Pemilik']);
        $owner = Student::create(['user_id' => $ownerUser->id, 'nisn' => '1111111111', 'gender' => Gender::Male]);
        $owner->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'status' => 'aktif']);

        $result = AssessmentResult::create([
            'student_id' => $owner->id, 'assessment_schedule_id' => $schedule->id,
            'total_score' => 22, 'category' => 'tinggi', 'completed_at' => now(),
        ]);

        return compact('result', 'ownerUser', 'owner');
    }

    public function test_owner_student_can_export_own_report(): void
    {
        ['owner' => $owner, 'ownerUser' => $ownerUser] = $this->makeResult();

        $response = $this->actingAs($ownerUser)->get(route('siswa.report-pdf', $owner));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }

    public function test_student_cannot_export_another_students_report(): void
    {
        ['owner' => $owner] = $this->makeResult();

        $otherUser = User::factory()->create(['role' => UserRole::Siswa]);
        Student::create(['user_id' => $otherUser->id, 'nisn' => '9999999999', 'gender' => Gender::Female]);

        $this->actingAs($otherUser)
            ->get(route('siswa.report-pdf', $owner))
            ->assertForbidden();
    }

    public function test_guru_bk_can_export_any_students_report(): void
    {
        ['owner' => $owner, 'result' => $result] = $this->makeResult();
        ResultNote::create(['assessment_result_id' => $result->id, 'guru_bk_id' => User::factory()->create(['role' => UserRole::GuruBk])->id, 'content' => 'Perlu konsultasi.']);

        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $response = $this->actingAs($guruBk)->get(route('siswa.report-pdf', $owner));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }
}
