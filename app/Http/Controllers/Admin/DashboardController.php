<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Enums\Severity;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $completedQuery = fn () => Assessment::where('status', AssessmentStatus::Completed);

        $totalStudents = Student::count();
        $totalCompleted = $completedQuery()->count();
        $concerningStudents = $completedQuery()->whereIn('overall_severity', ['severe', 'extremely_severe'])
            ->distinct('student_id')
            ->count('student_id');
        $averageStressScore = (int) round($completedQuery()->avg('stress_score') ?? 0);

        $severityDistribution = $completedQuery()
            ->select('overall_severity', DB::raw('count(*) as total'))
            ->groupBy('overall_severity')
            ->pluck('total', 'overall_severity');

        $studentsPerClass = SchoolClass::withCount('students')
            ->orderBy('name')
            ->get()
            ->pluck('students_count', 'name');

        $monthlyCounts = $completedQuery()
            ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['completed_at'])
            ->countBy(fn (Assessment $assessment) => $assessment->completed_at->format('Y-m'));

        $monthlyTrend = collect(range(5, 0))->mapWithKeys(function (int $monthsAgo) use ($monthlyCounts) {
            $key = now()->subMonths($monthsAgo)->format('Y-m');

            return [$key => $monthlyCounts->get($key, 0)];
        });

        $latestActivities = $completedQuery()
            ->with(['student.user', 'student.schoolClass'])
            ->latest('completed_at')
            ->take(10)
            ->get();

        $severityLabels = collect(Severity::cases())->mapWithKeys(fn (Severity $s) => [$s->value => $s->label()]);

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalCompleted',
            'concerningStudents',
            'averageStressScore',
            'severityDistribution',
            'studentsPerClass',
            'monthlyTrend',
            'latestActivities',
            'severityLabels',
        ));
    }
}
