<?php

namespace App\Livewire\Shared;

use App\Enums\UserRole;
use App\Models\AssessmentResult;
use App\Models\ResultNote;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AssessmentResultDetail extends Component
{
    public AssessmentResult $result;

    public bool $editingNote = false;

    public string $noteContent = '';

    public function mount(AssessmentResult $result): void
    {
        $user = auth()->user();

        if (! $user->hasRoleAtLeast(UserRole::GuruBk)) {
            abort_unless($result->student_id === $user->student?->id, 403);
        }

        $this->result = $result->load([
            'student.user',
            'assessmentSchedule.assessment',
            'answers.question',
            'note.guruBk',
        ]);
    }

    #[Computed]
    public function answers()
    {
        return $this->result->answers->sortBy('question.order');
    }

    #[Computed]
    public function canManageNote(): bool
    {
        return auth()->user()->hasRoleAtLeast(UserRole::GuruBk);
    }

    public function editNote(): void
    {
        abort_unless($this->canManageNote, 403);

        $this->noteContent = $this->result->note?->content ?? '';
        $this->editingNote = true;
    }

    public function saveNote(): void
    {
        abort_unless($this->canManageNote, 403);

        $validated = $this->validate([
            'noteContent' => 'required|string|max:2000',
        ]);

        ResultNote::updateOrCreate(
            ['assessment_result_id' => $this->result->id],
            ['guru_bk_id' => auth()->id(), 'content' => $validated['noteContent']]
        );

        $this->result->load('note.guruBk');
        $this->editingNote = false;
    }

    public function cancelEditNote(): void
    {
        $this->editingNote = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.shared.assessment-result-detail');
    }
}
