<?php

namespace App\Livewire\GuruBk;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\StudentClassHistory;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClassPromotion extends Component
{
    public ?int $sourceYearId = null;

    public ?int $targetYearId = null;

    /** @var array<int, string> key = school_class_id, value = target school_class_id or 'lulus' */
    public array $mappings = [];

    public ?array $result = null;

    public function mount(): void
    {
        $current = AcademicYear::where('is_active', true)->first();
        $this->sourceYearId = $current?->id;
        $this->prefillMappings();
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

    #[Computed]
    public function sourceClassSummaries()
    {
        if (! $this->sourceYearId) {
            return collect();
        }

        return StudentClassHistory::query()
            ->where('academic_year_id', $this->sourceYearId)
            ->where('status', 'aktif')
            ->selectRaw('school_class_id, count(*) as student_count')
            ->groupBy('school_class_id')
            ->with('schoolClass')
            ->get();
    }

    public function updatedSourceYearId(): void
    {
        $this->prefillMappings();
        $this->result = null;
    }

    public function updatedTargetYearId(): void
    {
        $this->prefillMappings();
        $this->result = null;
    }

    private function prefillMappings(): void
    {
        $this->mappings = [];

        foreach ($this->sourceClassSummaries as $summary) {
            // Default mapping: kelas asal itu sendiri — guru BK ubah manual jika naik ke kelas berbeda.
            $this->mappings[$summary->school_class_id] = (string) $summary->school_class_id;
        }
    }

    public function promote(): void
    {
        $this->validate([
            'sourceYearId' => 'required|exists:academic_years,id',
            'targetYearId' => ['required', 'exists:academic_years,id', 'different:sourceYearId'],
        ]);

        $promoted = 0;
        $graduated = 0;
        $skippedExisting = 0;

        DB::transaction(function () use (&$promoted, &$graduated, &$skippedExisting) {
            foreach ($this->sourceClassSummaries as $summary) {
                $mapping = $this->mappings[$summary->school_class_id] ?? null;

                $histories = StudentClassHistory::where('academic_year_id', $this->sourceYearId)
                    ->where('school_class_id', $summary->school_class_id)
                    ->where('status', 'aktif')
                    ->get();

                if ($mapping === 'lulus' || ! $mapping) {
                    $graduated += $histories->count();

                    continue;
                }

                foreach ($histories as $history) {
                    $alreadyExists = StudentClassHistory::where('student_id', $history->student_id)
                        ->where('academic_year_id', $this->targetYearId)
                        ->exists();

                    if ($alreadyExists) {
                        $skippedExisting++;

                        continue;
                    }

                    StudentClassHistory::create([
                        'student_id' => $history->student_id,
                        'academic_year_id' => $this->targetYearId,
                        'school_class_id' => (int) $mapping,
                        'status' => 'aktif',
                    ]);

                    $promoted++;
                }
            }
        });

        $this->result = [
            'promoted' => $promoted,
            'graduated' => $graduated,
            'skipped' => $skippedExisting,
        ];
    }

    public function render()
    {
        return view('livewire.guru-bk.class-promotion');
    }
}
