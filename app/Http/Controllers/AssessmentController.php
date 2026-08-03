<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Enums\FollowUpStatus;
use App\Models\Assessment;
use App\Models\Recommendation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AssessmentController extends Controller
{
    public function show(Assessment $assessment): View
    {
        abort_if($assessment->status !== AssessmentStatus::Completed, 404);

        $assessment->load(['student.user', 'student.schoolClass', 'followUps.counselor']);

        $recommendations = Recommendation::forAssessment($assessment);

        return view('shared.assessment-detail', compact('assessment', 'recommendations'));
    }

    public function storeFollowUp(Request $request, Assessment $assessment): RedirectResponse
    {
        abort_if($assessment->status !== AssessmentStatus::Completed, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(FollowUpStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $assessment->followUps()->create([
            'counselor_id' => auth()->id(),
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', 'Catatan tindak lanjut berhasil disimpan.');
    }

    public function downloadPdf(Assessment $assessment): Response
    {
        abort_if($assessment->status !== AssessmentStatus::Completed, 404);

        $assessment->load(['student.user', 'student.schoolClass']);

        $recommendations = Recommendation::forAssessment($assessment);

        $pdf = Pdf::loadView('pdf.assessment-result', compact('assessment', 'recommendations'));

        return $pdf->download("hasil-asesmen-{$assessment->id}.pdf");
    }
}
