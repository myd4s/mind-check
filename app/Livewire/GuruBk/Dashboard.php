<?php

namespace App\Livewire\GuruBk;

use App\Models\AcademicYear;
use App\Models\AssessmentResult;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $academicYearFilter = null;

    public ?int $classFilter = null;

    public function mount(): void
    {
        $this->academicYearFilter = AcademicYear::where('is_active', true)->value('id');
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

    /**
     * Hasil terbaru per siswa (satu baris per siswa) dalam tahun ajaran
     * terpilih, opsional difilter per kelas.
     */
    #[Computed]
    public function latestResultsPerStudent()
    {
        if (! $this->academicYearFilter) {
            return collect();
        }

        return AssessmentResult::query()
            ->with(['student.user', 'student.currentClassHistory.schoolClass'])
            ->whereHas('assessmentSchedule', fn ($q) => $q->where('academic_year_id', $this->academicYearFilter))
            ->when($this->classFilter, fn ($q) => $q->whereHas(
                'student.currentClassHistory',
                fn ($q2) => $q2->where('school_class_id', $this->classFilter)
            ))
            ->get()
            ->groupBy('student_id')
            ->map(fn ($group) => $group->sortByDesc('completed_at')->first())
            ->values();
    }

    #[Computed]
    public function categoryCounts(): array
    {
        $counts = $this->latestResultsPerStudent->countBy('category');

        return [
            'rendah' => $counts->get('rendah', 0),
            'sedang' => $counts->get('sedang', 0),
            'tinggi' => $counts->get('tinggi', 0),
        ];
    }

    #[Computed]
    public function highCategoryStudents()
    {
        return $this->latestResultsPerStudent
            ->where('category', 'tinggi')
            ->sortByDesc('completed_at')
            ->values();
    }

    public function render()
    {
        return view('livewire.guru-bk.dashboard');
    }
}
