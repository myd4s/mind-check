<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\QuestionManagement;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.questions'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_custom_question(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(QuestionManagement::class)
            ->call('create')
            ->set('text', 'Soal tambahan uji coba?')
            ->set('reverse_scored', false)
            ->call('save');

        $this->assertDatabaseHas('questions', [
            'text' => 'Soal tambahan uji coba?',
            'is_core' => false,
        ]);
    }

    public function test_guru_bk_can_edit_custom_question(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $question = Question::create([
            'text' => 'Soal lama', 'order' => 100, 'reverse_scored' => false, 'is_active' => true, 'is_core' => false,
        ]);

        Livewire::actingAs($guruBk)
            ->test(QuestionManagement::class)
            ->call('edit', $question->id)
            ->set('text', 'Soal sudah diedit')
            ->call('save');

        $this->assertDatabaseHas('questions', ['id' => $question->id, 'text' => 'Soal sudah diedit']);
    }

    public function test_guru_bk_can_delete_custom_question(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $question = Question::create([
            'text' => 'Soal dihapus', 'order' => 100, 'reverse_scored' => false, 'is_active' => true, 'is_core' => false,
        ]);

        Livewire::actingAs($guruBk)
            ->test(QuestionManagement::class)
            ->call('confirmDelete', $question->id)
            ->call('delete');

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_core_question_cannot_be_edited_even_via_direct_call(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $core = Question::create([
            'text' => 'Soal inti PSS-10', 'order' => 1, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($guruBk)
            ->test(QuestionManagement::class)
            ->call('edit', $core->id);
    }

    public function test_core_question_cannot_be_deleted_even_via_direct_call(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $core = Question::create([
            'text' => 'Soal inti PSS-10', 'order' => 1, 'reverse_scored' => false, 'is_active' => true, 'is_core' => true,
        ]);

        Livewire::actingAs($guruBk)
            ->test(QuestionManagement::class)
            ->call('confirmDelete', $core->id)
            ->call('delete');

        $this->assertDatabaseHas('questions', ['id' => $core->id]);
    }
}
