<?php

namespace App\Livewire\GuruBk;

use App\Models\AcademicYear;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\StudentClassHistory;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $academicYearFilter = null;

    public ?int $classFilter = null;

    public ?int $scheduleFilter = null;

    public function mount(): void
    {
        $this->academicYearFilter = AcademicYear::where('is_active', true)->value('id');
        $this->scheduleFilter = $this->schedules->first()?->id;
    }

    public function updatedAcademicYearFilter(): void
    {
        unset($this->schedules);
        $this->scheduleFilter = $this->schedules->first()?->id;
    }

    #[Computed]
    public function academicYears()
    {
        return AcademicYear::orderByDesc('start_date')->get();
    }

    /**
     * Jadwal asesmen dalam tahun ajaran terpilih — dasar hitung
     * "sudah/belum mengerjakan" yang bersifat per-jadwal.
     */
    #[Computed]
    public function schedules()
    {
        if (! $this->academicYearFilter) {
            return collect();
        }

        return AssessmentSchedule::where('academic_year_id', $this->academicYearFilter)
            ->orderByDesc('start_at')
            ->get();
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

    /**
     * Rekap partisipasi siswa terhadap satu jadwal asesmen terpilih:
     * "sudah" = punya hasil untuk jadwal itu, "belum" = terdaftar aktif di
     * kelas sasaran jadwal tapi belum mengerjakan (PRD §7).
     */
    #[Computed]
    public function participation(): array
    {
        $empty = ['sudah' => 0, 'belum' => 0, 'total' => 0];

        if (! $this->academicYearFilter || ! $this->scheduleFilter) {
            return $empty;
        }

        $schedule = AssessmentSchedule::with('targetClasses')->find($this->scheduleFilter);

        if (! $schedule) {
            return $empty;
        }

        $enrolledStudentIds = StudentClassHistory::query()
            ->where('academic_year_id', $this->academicYearFilter)
            ->where('status', 'aktif')
            ->when($this->classFilter, fn ($q) => $q->where('school_class_id', $this->classFilter))
            ->when(
                $schedule->target_type !== 'all',
                fn ($q) => $q->whereIn('school_class_id', $schedule->targetClasses->pluck('id'))
            )
            ->pluck('student_id')
            ->unique();

        $completed = AssessmentResult::where('assessment_schedule_id', $this->scheduleFilter)
            ->whereIn('student_id', $enrolledStudentIds)
            ->distinct()
            ->count('student_id');

        $total = $enrolledStudentIds->count();

        return [
            'sudah' => $completed,
            'belum' => max($total - $completed, 0),
            'total' => $total,
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
