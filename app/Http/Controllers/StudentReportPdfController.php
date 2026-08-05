<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AssessmentResult;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class StudentReportPdfController extends Controller
{
    public function __invoke(Student $student): Response
    {
        $user = auth()->user();

        if (! $user->hasRoleAtLeast(UserRole::GuruBk)) {
            abort_unless($student->id === $user->student?->id, 403);
        }

        $student->load(['user', 'currentClassHistory.schoolClass']);

        $results = AssessmentResult::with(['assessmentSchedule.assessment', 'note.guruBk'])
            ->where('student_id', $student->id)
            ->orderByDesc('completed_at')
            ->get();

        $pdf = Pdf::loadView('pdf.student-report', [
            'student' => $student,
            'results' => $results,
        ]);

        return $pdf->download("laporan-hasil-{$student->nisn}.pdf");
    }
}
