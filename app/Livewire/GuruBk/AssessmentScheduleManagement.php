<?php

namespace App\Livewire\GuruBk;

use App\Exports\AssessmentScheduleResultsExport;
use App\Livewire\Concerns\WithTableControls;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class AssessmentScheduleManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $title = '';

    public ?int $assessment_id = null;

    public ?int $academic_year_id = null;

    public string $start_at = '';

    public string $end_at = '';

    public string $target_type = 'all';

    /** @var array<int, bool> key = school_class_id */
    public array $target_class_ids = [];

    public bool $is_active = true;

    public bool $showModal = false;

    public ?int $deletingId = null;

    #[Computed]
    public function schedules()
    {
        return AssessmentSchedule::with(['assessment', 'academicYear'])
            ->when($this->search, fn ($query) => $query->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'start_at', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function assessments()
    {
        return Assessment::orderBy('title')->get();
    }

    #[Computed]
    public function academicYears()
    {
        return AcademicYear::orderByDesc('start_date')->get();
    }

    #[Computed]
    public function schoolClasses()
    {
        return SchoolClass::orderBy('grade_level')->orderBy('name')->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->academic_year_id = AcademicYear::where('is_active', true)->value('id');
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $schedule = AssessmentSchedule::with('targetClasses')->findOrFail($id);

        $this->editingId = $schedule->id;
        $this->title = $schedule->title;
        $this->assessment_id = $schedule->assessment_id;
        $this->academic_year_id = $schedule->academic_year_id;
        $this->start_at = $schedule->start_at->format('Y-m-d\TH:i');
        $this->end_at = $schedule->end_at->format('Y-m-d\TH:i');
        $this->target_type = $schedule->target_type;
        $this->target_class_ids = $schedule->targetClasses->pluck('id')->mapWithKeys(fn ($id) => [$id => true])->all();
        $this->is_active = $schedule->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'assessment_id' => 'required|exists:assessments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'target_type' => ['required', Rule::in(['all', 'specific'])],
            'is_active' => 'boolean',
        ]);

        $targetClassIds = collect($this->target_class_ids)->filter()->keys();

        if ($this->target_type === 'specific' && $targetClassIds->isEmpty()) {
            $this->addError('target_class_ids', 'Pilih minimal 1 kelas target.');

            return;
        }

        $schedule = AssessmentSchedule::updateOrCreate(['id' => $this->editingId], $validated);

        $schedule->targetClasses()->sync($this->target_type === 'specific' ? $targetClassIds : []);

        $this->showModal = false;
        $this->resetForm();
    }

    public function downloadResultsExport(int $id)
    {
        $schedule = AssessmentSchedule::findOrFail($id);

        return Excel::download(
            new AssessmentScheduleResultsExport($schedule),
            'rekap-'.str($schedule->title)->slug().'.xlsx'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        AssessmentSchedule::find($this->deletingId)?->delete();
        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'assessment_id', 'academic_year_id', 'start_at', 'end_at', 'target_class_ids']);
        $this->target_type = 'all';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.assessment-schedule-management');
    }
}
