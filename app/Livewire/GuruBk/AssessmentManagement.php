<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\Assessment;
use App\Models\Question;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AssessmentManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    /** @var array<int, bool> key = question_id */
    public array $selectedQuestionIds = [];

    public bool $showModal = false;

    public ?int $deletingId = null;

    #[Computed]
    public function assessments()
    {
        return Assessment::withCount('questions')
            ->when($this->search, fn ($query) => $query->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'created_at', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function allQuestions()
    {
        return Question::where('is_active', true)->orderBy('order')->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $assessment = Assessment::with('questions')->findOrFail($id);

        $this->editingId = $assessment->id;
        $this->title = $assessment->title;
        $this->description = (string) $assessment->description;
        $this->selectedQuestionIds = $assessment->questions->pluck('id')->mapWithKeys(fn ($id) => [$id => true])->all();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $questionIds = collect($this->selectedQuestionIds)->filter()->keys();

        if ($questionIds->isEmpty()) {
            $this->addError('selectedQuestionIds', 'Pilih minimal 1 soal untuk paket asesmen ini.');

            return;
        }

        $assessment = Assessment::updateOrCreate(['id' => $this->editingId], $validated);

        $questions = Question::whereIn('id', $questionIds)->get();

        $assessment->questions()->sync(
            $questions->mapWithKeys(fn (Question $question) => [
                $question->id => ['order' => $question->order],
            ])
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        Assessment::find($this->deletingId)?->delete();
        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'selectedQuestionIds']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.assessment-management');
    }
}
