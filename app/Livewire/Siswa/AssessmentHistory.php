<?php

namespace App\Livewire\Siswa;

use App\Livewire\Concerns\WithTableControls;
use App\Models\AssessmentResult;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AssessmentHistory extends Component
{
    use WithPagination, WithTableControls;

    #[Computed]
    public function results()
    {
        $student = auth()->user()->student;

        if (! $student) {
            return AssessmentResult::query()->where('id', 0)->paginate($this->perPage);
        }

        return AssessmentResult::with('assessmentSchedule.assessment')
            ->where('student_id', $student->id)
            ->when($this->search, fn ($query) => $query->whereHas(
                'assessmentSchedule.assessment',
                fn ($q) => $q->where('title', 'like', "%{$this->search}%")
            ))
            ->orderBy($this->sortField ?: 'completed_at', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.siswa.assessment-history');
    }
}
