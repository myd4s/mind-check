<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\Question;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class QuestionManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $text = '';

    public bool $reverse_scored = false;

    public bool $is_active = true;

    public bool $showModal = false;

    public ?int $deletingId = null;

    #[Computed]
    public function coreQuestions()
    {
        return Question::where('is_core', true)->orderBy('order')->get();
    }

    #[Computed]
    public function customQuestions()
    {
        return Question::where('is_core', false)
            ->when($this->search, fn ($query) => $query->where('text', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'order', $this->sortField ? $this->sortDirection : 'asc')
            ->orderBy('created_at')
            ->paginate($this->perPage);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $question = Question::where('is_core', false)->findOrFail($id);

        $this->editingId = $question->id;
        $this->text = $question->text;
        $this->reverse_scored = $question->reverse_scored;
        $this->is_active = $question->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'text' => 'required|string|max:1000',
            'reverse_scored' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($this->editingId) {
            Question::where('is_core', false)->findOrFail($this->editingId)->update($validated);
        } else {
            $nextOrder = (int) Question::max('order') + 1;

            Question::create([
                ...$validated,
                'order' => $nextOrder,
                'is_core' => false,
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        Question::where('is_core', false)->find($this->deletingId)?->delete();
        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'text', 'reverse_scored', 'is_active']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.question-management');
    }
}
