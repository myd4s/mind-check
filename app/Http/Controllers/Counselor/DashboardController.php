<?php

namespace App\Http\Controllers\Counselor;

use App\Enums\AssessmentStatus;
use App\Enums\FollowUpStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $latestPerStudent = Assessment::where('status', AssessmentStatus::Completed)
            ->selectRaw('MAX(id) as id')
            ->groupBy('student_id');

        $needsAttention = Assessment::whereIn('id', $latestPerStudent)
            ->whereIn('overall_severity', ['severe', 'extremely_severe'])
            ->with(['student.user', 'student.schoolClass', 'followUps'])
            ->orderByDesc('completed_at')
            ->get();

        $needsAttention->each(function (Assessment $assessment) {
            $assessment->currentFollowUpStatus = $assessment->followUps->sortByDesc('id')->first()?->status
                ?? FollowUpStatus::BelumDitangani;
        });

        $totalStudents = Student::count();
        $belumDitangani = $needsAttention->filter(fn ($a) => $a->currentFollowUpStatus === FollowUpStatus::BelumDitangani)->count();
        $sedangDitangani = $needsAttention->filter(fn ($a) => $a->currentFollowUpStatus === FollowUpStatus::SedangDitangani)->count();
        $selesai = $needsAttention->filter(fn ($a) => $a->currentFollowUpStatus === FollowUpStatus::Selesai)->count();

        return view('counselor.dashboard', compact(
            'needsAttention',
            'totalStudents',
            'belumDitangani',
            'sedangDitangani',
            'selesai',
        ));
    }
}
