<?php

namespace Tests\Feature\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Livewire\Shared\AssessmentResultDetail;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\ResultNote;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResultNoteTest extends TestCase
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
            'total_score' => 30, 'category' => 'tinggi', 'completed_at' => now(),
        ]);

        return compact('result', 'ownerUser', 'owner');
    }

    public function test_guru_bk_can_add_note_to_result(): void
    {
        ['result' => $result] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->call('editNote')
            ->set('noteContent', 'Perlu perhatian khusus, sudah dihubungi orang tua.')
            ->call('saveNote');

        $this->assertDatabaseHas('result_notes', [
            'assessment_result_id' => $result->id,
            'guru_bk_id' => $guruBk->id,
            'content' => 'Perlu perhatian khusus, sudah dihubungi orang tua.',
        ]);
    }

    public function test_guru_bk_can_edit_existing_note(): void
    {
        ['result' => $result] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        ResultNote::create([
            'assessment_result_id' => $result->id, 'guru_bk_id' => $guruBk->id, 'content' => 'Catatan awal.',
        ]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->call('editNote')
            ->set('noteContent', 'Catatan sudah diperbarui.')
            ->call('saveNote');

        $this->assertDatabaseHas('result_notes', [
            'assessment_result_id' => $result->id,
            'content' => 'Catatan sudah diperbarui.',
        ]);
        $this->assertSame(1, ResultNote::where('assessment_result_id', $result->id)->count());
    }

    public function test_note_content_is_required(): void
    {
        ['result' => $result] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->call('editNote')
            ->set('noteContent', '')
            ->call('saveNote')
            ->assertHasErrors(['noteContent']);
    }

    public function test_owner_student_can_see_note_read_only(): void
    {
        ['result' => $result, 'ownerUser' => $ownerUser] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        ResultNote::create([
            'assessment_result_id' => $result->id, 'guru_bk_id' => $guruBk->id, 'content' => 'Catatan untuk siswa.',
        ]);

        $component = Livewire::actingAs($ownerUser)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->assertSee('Catatan untuk siswa.')
            ->assertDontSee('Tambah Catatan')
            ->assertDontSee('wire:click="editNote"', false);

        $this->assertFalse($component->get('canManageNote'));
    }

    public function test_siswa_cannot_call_save_note_even_if_attempted_directly(): void
    {
        ['result' => $result, 'ownerUser' => $ownerUser] = $this->makeResult();

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($ownerUser)
            ->test(AssessmentResultDetail::class, ['result' => $result])
            ->set('noteContent', 'Mencoba menulis catatan sendiri.')
            ->call('saveNote');
    }

    public function test_other_student_never_sees_the_note(): void
    {
        ['result' => $result] = $this->makeResult();
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        ResultNote::create([
            'assessment_result_id' => $result->id, 'guru_bk_id' => $guruBk->id, 'content' => 'Rahasia catatan.',
        ]);

        $otherUser = User::factory()->create(['role' => UserRole::Siswa]);
        Student::create(['user_id' => $otherUser->id, 'nisn' => '2222222222', 'gender' => Gender::Female]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($otherUser)->test(AssessmentResultDetail::class, ['result' => $result]);
    }
}
