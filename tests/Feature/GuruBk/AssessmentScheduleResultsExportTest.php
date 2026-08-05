<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Exports\AssessmentScheduleResultsExport;
use App\Livewire\GuruBk\AssessmentScheduleManagement;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class AssessmentScheduleResultsExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(AcademicYear $year, SchoolClass $class, string $nisn, string $name): Student
    {
        $user = User::factory()->create(['role' => UserRole::Siswa, 'name' => $name]);
        $student = Student::create(['user_id' => $user->id, 'nisn' => $nisn, 'gender' => Gender::Male]);
        $student->classHistories()->create(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'status' => 'aktif']);

        return $student;
    }

    public function test_export_includes_all_participants_marking_unfinished_ones(): void
    {
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);

        $finished = $this->makeStudent($year, $class, '1111111111', 'Siswa Selesai');
        $this->makeStudent($year, $class, '2222222222', 'Siswa Belum');

        AssessmentResult::create([
            'student_id' => $finished->id, 'assessment_schedule_id' => $schedule->id,
            'total_score' => 25, 'category' => 'tinggi', 'completed_at' => now(),
        ]);

        $rows = (new AssessmentScheduleResultsExport($schedule))->collection();

        $this->assertCount(2, $rows);

        $finishedRow = $rows->firstWhere(0, '1111111111');
        $this->assertSame('Siswa Selesai', $finishedRow[1]);
        $this->assertSame('X IPA 1', $finishedRow[2]);
        $this->assertSame(25, $finishedRow[3]);
        $this->assertSame('Tinggi', $finishedRow[4]);

        $unfinishedRow = $rows->firstWhere(0, '2222222222');
        $this->assertSame('Belum Mengerjakan', $unfinishedRow[4]);
        $this->assertSame('—', $unfinishedRow[3]);
    }

    public function test_export_respects_specific_class_targeting(): void
    {
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $classA = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $classB = SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal Kelas A',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'specific', 'is_active' => true,
        ]);
        $schedule->targetClasses()->sync([$classA->id]);

        $this->makeStudent($year, $classA, '1111111111', 'Siswa A');
        $this->makeStudent($year, $classB, '2222222222', 'Siswa B');

        $rows = (new AssessmentScheduleResultsExport($schedule))->collection();

        $this->assertCount(1, $rows);
        $this->assertSame('1111111111', $rows->first()[0]);
    }

    public function test_guru_bk_can_trigger_export_download(): void
    {
        Excel::fake();

        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $class = SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        $assessment = Assessment::create(['title' => 'Paket A']);
        $schedule = AssessmentSchedule::create([
            'assessment_id' => $assessment->id, 'academic_year_id' => $year->id, 'title' => 'Jadwal Unduh',
            'start_at' => now()->subDay(), 'end_at' => now()->addDay(), 'target_type' => 'all', 'is_active' => true,
        ]);
        $this->makeStudent($year, $class, '1111111111', 'Siswa A');

        Livewire::actingAs($guruBk)
            ->test(AssessmentScheduleManagement::class)
            ->call('downloadResultsExport', $schedule->id);

        Excel::assertDownloaded('rekap-jadwal-unduh.xlsx', function (AssessmentScheduleResultsExport $export) {
            return $export->collection()->count() === 1;
        });
    }
}
