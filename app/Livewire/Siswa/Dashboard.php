<?php

namespace App\Livewire\Siswa;

use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function student()
    {
        return auth()->user()->student;
    }

    #[Computed]
    public function results()
    {
        if (! $this->student) {
            return collect();
        }

        return AssessmentResult::where('student_id', $this->student->id)
            ->orderBy('completed_at')
            ->get();
    }

    #[Computed]
    public function latestResult(): ?AssessmentResult
    {
        return $this->results->last();
    }

    #[Computed]
    public function availableCount(): int
    {
        $classId = $this->student?->currentClassHistory?->school_class_id;

        if (! $classId) {
            return 0;
        }

        $completedScheduleIds = $this->results->pluck('assessment_schedule_id');

        return AssessmentSchedule::where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereNotIn('id', $completedScheduleIds)
            ->get()
            ->filter(fn ($schedule) => $schedule->appliesToClass($classId))
            ->count();
    }

    #[Computed]
    public function chartData(): array
    {
        return [
            'labels' => $this->results->map(fn (AssessmentResult $r) => $r->completed_at->format('d M Y'))->values()->all(),
            'scores' => $this->results->pluck('total_score')->values()->all(),
        ];
    }

    public function render()
    {
        return view('livewire.siswa.dashboard');
    }
}
