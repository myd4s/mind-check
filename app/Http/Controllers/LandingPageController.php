<?php

namespace App\Http\Controllers;

use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\StudentClassHistory;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Halaman publik. Mockup "Sebaran Tingkat Stress" di hero diisi data live
     * dari asesmen terakhir yang sudah punya hasil (bukan angka statis).
     */
    public function __invoke(): View
    {
        return view('welcome', ['heroStats' => $this->latestAssessmentStats()]);
    }

    private function latestAssessmentStats(): ?array
    {
        $schedule = AssessmentSchedule::query()
            ->whereIn('id', AssessmentResult::query()->select('assessment_schedule_id'))
            ->with('targetClasses')
            ->orderByDesc('start_at')
            ->first();

        if (! $schedule) {
            return null;
        }

        // Hasil terbaru per siswa untuk jadwal ini (satu baris per siswa).
        $latestPerStudent = AssessmentResult::query()
            ->where('assessment_schedule_id', $schedule->id)
            ->get()
            ->groupBy('student_id')
            ->map(fn ($group) => $group->sortByDesc('completed_at')->first())
            ->values();

        $done = $latestPerStudent->count();

        if ($done === 0) {
            return null;
        }

        $counts = $latestPerStudent->countBy('category');
        $rendah = (int) round($counts->get('rendah', 0) / $done * 100);
        $sedang = (int) round($counts->get('sedang', 0) / $done * 100);
        $tinggi = max(0, 100 - $rendah - $sedang);

        // Total siswa sasaran jadwal (kelas target + tahun ajarannya).
        $target = StudentClassHistory::query()
            ->where('academic_year_id', $schedule->academic_year_id)
            ->where('status', 'aktif')
            ->when(
                $schedule->target_type !== 'all',
                fn ($q) => $q->whereIn('school_class_id', $schedule->targetClasses->pluck('id'))
            )
            ->distinct()
            ->count('student_id');

        // Spark bar: skor 7 pengerjaan terakhir, dinormalisasi relatif.
        $recent = $latestPerStudent
            ->sortByDesc('completed_at')
            ->take(7)
            ->pluck('total_score')
            ->reverse()
            ->values();

        $maxScore = max((int) $recent->max(), 1);
        $bars = $recent->map(fn ($score) => max(12, (int) round($score / $maxScore * 100)))->all();

        return [
            'title' => Str::limit($schedule->title, 40),
            'done' => $done,
            'total' => max($target, $done),
            'rendah' => $rendah,
            'sedang' => $sedang,
            'tinggi' => $tinggi,
            'bars' => $bars ?: [40, 65, 45, 80, 55, 90, 60],
        ];
    }
}
