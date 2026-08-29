<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\AcademicYear;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\SchoolClass;
use App\Models\StudentClassHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Rincian siswa yang sudah/belum mengerjakan sebuah jadwal asesmen.
 * "Sudah" = punya baris assessment_results untuk jadwal tsb; "Belum" = terdaftar
 * aktif di kelas sasaran jadwal tapi belum mengerjakan (PRD §7).
 */
#[Layout('layouts.app')]
class StudentParticipation extends Component
{
    use WithPagination, WithTableControls;

    #[Url]
    public ?int $academicYearFilter = null;

    #[Url]
    public ?int $scheduleFilter = null;

    #[Url]
    public string $classFilter = '';

    /** '', 'sudah', atau 'belum'. */
    #[Url]
    public string $statusFilter = '';

    public function mount(): void
    {
        $this->perPage = 15;
        $this->academicYearFilter ??= AcademicYear::where('is_active', true)->value('id');
        $this->scheduleFilter ??= $this->schedules->first()?->id;
    }

    public function updatedAcademicYearFilter(): void
    {
        unset($this->schedules);
        $this->scheduleFilter = $this->schedules->first()?->id;
        $this->resetPage();
    }

    public function updatingScheduleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function academicYears()
    {
        return AcademicYear::orderByDesc('start_date')->get();
    }

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

    #[Computed]
    public function currentSchedule(): ?AssessmentSchedule
    {
        return $this->scheduleFilter
            ? AssessmentSchedule::with('targetClasses')->find($this->scheduleFilter)
            : null;
    }

    /**
     * Semua baris siswa dalam cakupan jadwal + kelas + pencarian
     * (tanpa filter status, tanpa paginasi) — dipakai kartu ringkasan & tabel.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function scopedRows(): Collection
    {
        $schedule = $this->currentSchedule;

        if (! $schedule || ! $this->academicYearFilter) {
            return collect();
        }

        $histories = StudentClassHistory::query()
            ->with(['student.user', 'schoolClass'])
            ->where('academic_year_id', $this->academicYearFilter)
            ->where('status', 'aktif')
            ->when($this->classFilter, fn ($q) => $q->where('school_class_id', $this->classFilter))
            ->when(
                $schedule->target_type !== 'all',
                fn ($q) => $q->whereIn('school_class_id', $schedule->targetClasses->pluck('id'))
            )
            ->get()
            ->filter(fn ($history) => $history->student && $history->student->user);

        $results = AssessmentResult::where('assessment_schedule_id', $schedule->id)
            ->get()
            ->keyBy('student_id');

        $search = trim(mb_strtolower($this->search));

        return $histories
            ->map(function ($history) use ($results) {
                $result = $results->get($history->student_id);

                return [
                    'student_id' => $history->student_id,
                    'name' => $history->student->user->name,
                    'nisn' => (string) $history->student->nisn,
                    'class' => $history->schoolClass?->name ?? '—',
                    'done' => (bool) $result,
                    'completed_at' => $result?->completed_at,
                    'category' => $result?->category,
                    'score' => $result?->total_score,
                    'result_id' => $result?->id,
                ];
            })
            ->when($search !== '', fn ($rows) => $rows->filter(
                fn ($row) => str_contains(mb_strtolower($row['name']), $search)
                    || str_contains(mb_strtolower($row['nisn']), $search)
            ))
            ->pipe(fn ($rows) => $this->sortRows($rows))
            ->values();
    }

    /**
     * Urutan default: siswa "belum" dulu, lalu kelas & nama. Bila header kolom
     * diklik ($sortField terisi), pakai urutan itu.
     */
    protected function sortRows(Collection $rows): Collection
    {
        if ($this->sortField === '') {
            return $rows->sortBy([
                ['done', 'asc'],
                ['class', 'asc'],
                ['name', 'asc'],
            ]);
        }

        $categoryRank = ['rendah' => 1, 'sedang' => 2, 'tinggi' => 3];

        $accessor = match ($this->sortField) {
            'status' => fn ($row) => $row['done'] ? 1 : 0,
            'category' => fn ($row) => $categoryRank[$row['category']] ?? 0,
            'completed_at' => fn ($row) => $row['completed_at']?->timestamp ?? 0,
            'class' => fn ($row) => mb_strtolower($row['class']),
            default => fn ($row) => mb_strtolower($row['name']),
        };

        return $rows->sortBy($accessor, SORT_REGULAR, $this->sortDirection === 'desc');
    }

    #[Computed]
    public function summary(): array
    {
        $rows = $this->scopedRows();
        $done = $rows->where('done', true)->count();

        return [
            'total' => $rows->count(),
            'sudah' => $done,
            'belum' => $rows->count() - $done,
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $rows = $this->scopedRows()
            ->when($this->statusFilter === 'sudah', fn ($rows) => $rows->where('done', true))
            ->when($this->statusFilter === 'belum', fn ($rows) => $rows->where('done', false))
            ->values();

        $page = $this->getPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, $this->perPage)->values(),
            $rows->count(),
            $this->perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function render()
    {
        return view('livewire.guru-bk.student-participation');
    }
}
