<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AssessmentResultManagement extends Component
{
    use WithPagination, WithTableControls;

    #[Url]
    public string $classFilter = '';

    #[Url]
    public string $scheduleFilter = '';

    #[Url]
    public string $categoryFilter = '';

    // Default 15/halaman dipertahankan (sebelum redesain hardcoded paginate(15)) — property $perPage
    // sudah dideklarasikan trait WithTableControls, jadi di-set lewat mount() bukan redeklarasi properti.
    public function mount(): void
    {
        $this->perPage = 15;
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingScheduleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function schoolClasses()
    {
        return SchoolClass::orderBy('grade_level')->orderBy('name')->get();
    }

    #[Computed]
    public function schedules()
    {
        return AssessmentSchedule::orderByDesc('start_at')->get();
    }

    #[Computed]
    public function results()
    {
        return AssessmentResult::query()
            ->with(['student.user', 'student.currentClassHistory.schoolClass', 'assessmentSchedule'])
            ->when($this->classFilter, fn ($query) => $query->whereHas(
                'student.currentClassHistory',
                fn ($q) => $q->where('school_class_id', $this->classFilter)
            ))
            ->when($this->scheduleFilter, fn ($query) => $query->where('assessment_schedule_id', $this->scheduleFilter))
            ->when($this->categoryFilter, fn ($query) => $query->where('category', $this->categoryFilter))
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->whereHas('student.user', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('assessmentSchedule', fn ($sq) => $sq->where('title', 'like', "%{$this->search}%"));
            }))
            ->orderBy($this->sortField ?: 'completed_at', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.guru-bk.assessment-result-management');
    }
}
