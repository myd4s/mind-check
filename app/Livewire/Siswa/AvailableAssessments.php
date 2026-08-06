<?php

namespace App\Livewire\Siswa;

use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AvailableAssessments extends Component
{
    #[Computed]
    public function student()
    {
        return auth()->user()->student;
    }

    #[Computed]
    public function availableSchedules()
    {
        $student = $this->student;
        $classId = $student?->currentClassHistory?->school_class_id;

        if (! $classId) {
            return collect();
        }

        $completedScheduleIds = AssessmentResult::where('student_id', $student->id)->pluck('assessment_schedule_id');

        return AssessmentSchedule::with('assessment.questions')
            ->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereNotIn('id', $completedScheduleIds)
            ->get()
            ->filter(fn (AssessmentSchedule $schedule) => $schedule->appliesToClass($classId))
            ->values();
    }

    public function render()
    {
        return view('livewire.siswa.available-assessments');
    }
}
