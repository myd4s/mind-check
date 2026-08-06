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
    public function categoryDescription(): string
    {
        return match ($this->result->category) {
            'rendah' => __('Skor ini menunjukkan tingkat stress yang rendah. Kamu tampak mampu mengelola tekanan dan tuntutan sehari-hari dengan cukup baik. Pertahankan kebiasaan positif seperti pola tidur cukup, olahraga, dan waktu istirahat yang sudah berjalan selama ini.'),
            'sedang' => __('Skor ini menunjukkan tingkat stress yang sedang. Beberapa tekanan mulai terasa memberatkan, namun masih dapat dikelola. Coba jaga pola istirahat, atur waktu dengan lebih baik, dan jangan ragu bicarakan bebanmu dengan orang terdekat atau Guru BK jika dirasa perlu.'),
            'tinggi' => __('Skor ini menunjukkan tingkat stress yang tinggi. Ini adalah sinyal penting untuk segera mendapat dukungan — sangat disarankan untuk berkonsultasi dengan Guru BK agar mendapat pendampingan yang tepat sebelum berdampak lebih jauh pada kesehatan dan aktivitas sehari-hari.'),
            default => '',
        };
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
