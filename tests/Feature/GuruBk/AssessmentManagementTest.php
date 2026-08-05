<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\AssessmentManagement;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.assessments'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_assessment_from_selected_questions(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $q1 = Question::create(['text' => 'Soal 1', 'order' => 1, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true]);
        $q2 = Question::create(['text' => 'Soal 2', 'order' => 2, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentManagement::class)
            ->call('create')
            ->set('title', 'Paket Uji Coba')
            ->set('description', 'Deskripsi paket')
            ->set("selectedQuestionIds.{$q1->id}", true)
            ->set("selectedQuestionIds.{$q2->id}", true)
            ->call('save');

        $this->assertDatabaseHas('assessments', ['title' => 'Paket Uji Coba']);

        $assessment = Assessment::where('title', 'Paket Uji Coba')->first();
        $this->assertSame(2, $assessment->questions()->count());
    }

    public function test_assessment_requires_at_least_one_question(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentManagement::class)
            ->call('create')
            ->set('title', 'Paket Kosong')
            ->call('save')
            ->assertHasErrors(['selectedQuestionIds']);

        $this->assertDatabaseMissing('assessments', ['title' => 'Paket Kosong']);
    }

    public function test_guru_bk_can_edit_assessment_questions(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $q1 = Question::create(['text' => 'Soal 1', 'order' => 1, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true]);
        $q2 = Question::create(['text' => 'Soal 2', 'order' => 2, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true]);

        $assessment = Assessment::create(['title' => 'Paket A', 'description' => null]);
        $assessment->questions()->sync([$q1->id => ['order' => 1]]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentManagement::class)
            ->call('edit', $assessment->id)
            ->set("selectedQuestionIds.{$q2->id}", true)
            ->call('save');

        $assessment->refresh();
        $this->assertSame(2, $assessment->questions()->count());
    }

    public function test_guru_bk_can_delete_assessment(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $assessment = Assessment::create(['title' => 'Paket Dihapus', 'description' => null]);

        Livewire::actingAs($guruBk)
            ->test(AssessmentManagement::class)
            ->call('confirmDelete', $assessment->id)
            ->call('delete');

        $this->assertDatabaseMissing('assessments', ['id' => $assessment->id]);
    }
}
