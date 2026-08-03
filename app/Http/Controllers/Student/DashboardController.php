<?php

namespace App\Http\Controllers\Student;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;

        abort_if(! $student, 403, 'Profil siswa tidak ditemukan.');

        $inProgress = $student->assessments()
            ->where('status', AssessmentStatus::InProgress)
            ->latest()
            ->first();

        $latestCompleted = $student->assessments()
            ->where('status', AssessmentStatus::Completed)
            ->latest('completed_at')
            ->first();

        $recentAssessments = $student->assessments()
            ->where('status', AssessmentStatus::Completed)
            ->latest('completed_at')
            ->take(5)
            ->get();

        $inProgressPercent = null;

        if ($inProgress) {
            $totalQuestions = Question::count();
            $inProgressPercent = $totalQuestions > 0
                ? (int) round($inProgress->answers()->count() / $totalQuestions * 100)
                : 0;
        }

        return view('student.dashboard', compact('inProgress', 'latestCompleted', 'recentAssessments', 'inProgressPercent'));
    }
}
