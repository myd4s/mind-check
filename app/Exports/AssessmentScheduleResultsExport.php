<?php

namespace App\Exports;

use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\StudentClassHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssessmentScheduleResultsExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly AssessmentSchedule $schedule) {}

    public function headings(): array
    {
        return ['NISN', 'Nama', 'Kelas', 'Skor', 'Kategori', 'Waktu Selesai'];
    }

    public function collection(): Collection
    {
        $participants = StudentClassHistory::with(['student.user', 'schoolClass'])
            ->where('academic_year_id', $this->schedule->academic_year_id)
            ->when(
                $this->schedule->target_type === 'specific',
                fn ($query) => $query->whereIn('school_class_id', $this->schedule->targetClasses()->pluck('school_classes.id'))
            )
            ->get()
            ->unique('student_id');

        $results = AssessmentResult::where('assessment_schedule_id', $this->schedule->id)
            ->get()
            ->keyBy('student_id');

        return $participants->map(function (StudentClassHistory $history) use ($results) {
            $result = $results->get($history->student_id);

            return [
                $history->student->nisn,
                $history->student->user->name,
                $history->schoolClass->name,
                $result?->total_score ?? '—',
                $result ? ucfirst($result->category) : 'Belum Mengerjakan',
                $result?->completed_at?->format('d-m-Y H:i') ?? '—',
            ];
        })->values();
    }
}
