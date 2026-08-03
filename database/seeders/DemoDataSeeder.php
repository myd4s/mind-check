<?php

namespace Database\Seeders;

use App\Enums\AssessmentStatus;
use App\Enums\Gender;
use App\Enums\UserRole;
use App\Events\AssessmentCompleted;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\DassScoringService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Data siswa & hasil asesmen contoh supaya dashboard admin/konselor/siswa
     * langsung terisi ketika pertama kali dicoba.
     */
    private const NAMES = [
        ['Ahmad Fauzi', 'L'], ['Siti Nurhaliza', 'P'], ['Budi Santoso', 'L'],
        ['Dewi Lestari', 'P'], ['Eko Prasetyo', 'L'], ['Fitri Handayani', 'P'],
        ['Galih Pratama', 'L'], ['Hana Wulandari', 'P'], ['Indra Gunawan', 'L'],
        ['Joko Susilo', 'L'], ['Kartika Sari', 'P'], ['Lukman Hakim', 'L'],
        ['Maya Anggraini', 'P'], ['Nanda Ramadhan', 'L'], ['Oktavia Putri', 'P'],
        ['Panji Nugroho', 'L'], ['Qonita Salsabila', 'P'], ['Rizky Firmansyah', 'L'],
        ['Sari Widyaningsih', 'P'], ['Taufik Hidayat', 'L'],
    ];

    /**
     * Kombinasi skor mentah [depression, anxiety, stress] (0-28 per subskala)
     * yang dipakai bergilir agar variasi tingkat severity beragam & bisa diprediksi.
     */
    private const RAW_SCORE_PATTERNS = [
        [2, 2, 3],   // normal
        [6, 5, 8],   // mild-ish
        [10, 9, 13], // moderate
        [16, 14, 18],// moderate-severe mix
        [20, 18, 22],// severe
        [26, 24, 26],// extremely severe
    ];

    public function run(): void
    {
        $classes = SchoolClass::all();
        $scoringService = app(DassScoringService::class);

        foreach (self::NAMES as $index => [$name, $gender]) {
            $email = 'siswa.'.str($name)->slug().'@mindcheck.test';

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'role' => UserRole::Student]
            );

            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'class_id' => $classes[$index % $classes->count()]->id,
                    'nis' => '2026'.str_pad((string) ($index + 100), 6, '0', STR_PAD_LEFT),
                    'gender' => Gender::from($gender),
                    'status' => 'active',
                ]
            );

            // Sisakan beberapa siswa terakhir tanpa asesmen untuk demo empty-state.
            if ($index >= count(self::NAMES) - 3) {
                continue;
            }

            if (Assessment::where('student_id', $student->id)->exists()) {
                continue;
            }

            $assessment = Assessment::create([
                'student_id' => $student->id,
                'status' => AssessmentStatus::InProgress,
                'started_at' => now()->subDays(count(self::NAMES) - $index),
            ]);

            [$depRaw, $anxRaw, $strRaw] = self::RAW_SCORE_PATTERNS[$index % count(self::RAW_SCORE_PATTERNS)];
            $this->fillAnswers($assessment, $depRaw, $anxRaw, $strRaw);

            $assessment->completed_at = now()->subDays(count(self::NAMES) - $index);
            $scoringService->scoreAssessment($assessment);
            $assessment->save();

            AssessmentCompleted::dispatch($assessment->fresh());
        }
    }

    private function fillAnswers(Assessment $assessment, int $depTotal, int $anxTotal, int $strTotal): void
    {
        $targets = [
            'depression' => $depTotal,
            'anxiety' => $anxTotal,
            'stress' => $strTotal,
        ];

        foreach ($targets as $subscale => $total) {
            $questions = Question::where('subscale', $subscale)->orderBy('order_number')->get();
            $values = $this->distribute($total, $questions->count());

            foreach ($questions as $i => $question) {
                AssessmentAnswer::create([
                    'assessment_id' => $assessment->id,
                    'question_id' => $question->id,
                    'answer_value' => $values[$i],
                ]);
            }
        }
    }

    /**
     * Sebarkan total skor ke sejumlah soal (masing-masing 0-4) serata mungkin.
     *
     * @return array<int, int>
     */
    private function distribute(int $total, int $count): array
    {
        $base = intdiv($total, $count);
        $remainder = $total % $count;

        $values = array_fill(0, $count, min($base, 4));

        for ($i = 0; $i < $remainder && $i < $count; $i++) {
            $values[$i] = min($values[$i] + 1, 4);
        }

        return $values;
    }
}
