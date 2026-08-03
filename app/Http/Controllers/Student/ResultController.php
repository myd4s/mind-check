<?php

namespace App\Http\Controllers\Student;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Recommendation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class ResultController extends Controller
{
    public function show(Assessment $assessment): View
    {
        $student = auth()->user()->student;

        abort_if(! $student || $assessment->student_id !== $student->id, 403);
        abort_if($assessment->status !== AssessmentStatus::Completed, 404);

        $recommendations = Recommendation::forAssessment($assessment);

        return view('student.result', compact('assessment', 'recommendations'));
    }

    public function downloadPdf(Assessment $assessment): Response
    {
        $student = auth()->user()->student;

        abort_if(! $student || $assessment->student_id !== $student->id, 403);
        abort_if($assessment->status !== AssessmentStatus::Completed, 404);

        $recommendations = Recommendation::forAssessment($assessment);

        $pdf = Pdf::loadView('pdf.assessment-result', compact('assessment', 'recommendations'));

        return $pdf->download("hasil-asesmen-{$assessment->id}.pdf");
    }
}
