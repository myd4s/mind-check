<?php

namespace App\Livewire\Siswa;

use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Services\Pss10ScoringService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AssessmentWizard extends Component
{
    public AssessmentSchedule $schedule;

    public int $currentIndex = 0;

    /** @var array<int, int> key = question_id, value = jawaban mentah 0-4 */
    public array $answers = [];

    public bool $justSubmitted = false;

    public ?array $submittedResult = null;

    public function mount(AssessmentSchedule $schedule): void
    {
        $student = auth()->user()->student;
        abort_unless($student, 403);

        $currentClassId = $student->currentClassHistory?->school_class_id;
        abort_unless(
            $currentClassId && $schedule->appliesToClass($currentClassId),
            403,
            'Jadwal ini tidak berlaku untuk kelas Anda.'
        );

        $this->schedule = $schedule;
    }

    #[Computed]
    public function student()
    {
        return auth()->user()->student;
    }

    #[Computed]
    public function questions()
    {
        return $this->schedule->assessment->questions;
    }

    #[Computed]
    public function existingResult(): ?AssessmentResult
    {
        return AssessmentResult::where('student_id', $this->student->id)
            ->where('assessment_schedule_id', $this->schedule->id)
            ->first();
    }

    #[Computed]
    public function currentQuestion()
    {
        return $this->questions[$this->currentIndex] ?? null;
    }

    #[Computed]
    public function isLastQuestion(): bool
    {
        return $this->currentIndex === $this->questions->count() - 1;
    }

    public function selectAnswer(int $questionId, int $value): void
    {
        $this->answers[$questionId] = $value;
    }

    public function next(): void
    {
        if (! $this->currentQuestion || ! array_key_exists($this->currentQuestion->id, $this->answers)) {
            return;
        }

        if (! $this->isLastQuestion) {
            $this->currentIndex++;
        }
    }

    public function previous(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function submit(): void
    {
        abort_if($this->existingResult, 403, 'Anda sudah mengerjakan asesmen ini.');
        abort_unless($this->schedule->isOpenNow(), 403, 'Jadwal asesmen ini tidak sedang berlangsung.');

        foreach ($this->questions as $question) {
            if (! array_key_exists($question->id, $this->answers)) {
                $this->addError('answers', 'Semua soal wajib dijawab.');

                return;
            }
        }

        $scoring = app(Pss10ScoringService::class);
        $totalScore = 0;

        $result = DB::transaction(function () use ($scoring, &$totalScore) {
            $result = AssessmentResult::create([
                'student_id' => $this->student->id,
                'assessment_schedule_id' => $this->schedule->id,
                'total_score' => 0,
                'category' => 'rendah',
                'completed_at' => now(),
            ]);

            foreach ($this->questions as $question) {
                $raw = (int) $this->answers[$question->id];
                $totalScore += $scoring->scoreItem($question, $raw);

                $result->answers()->create([
                    'question_id' => $question->id,
                    'answer_value' => $raw,
                ]);
            }

            $result->update([
                'total_score' => $totalScore,
                'category' => $scoring->categorize($totalScore),
            ]);

            return $result;
        });

        $this->submittedResult = [
            'total_score' => $result->total_score,
            'category' => $result->category,
        ];
        $this->justSubmitted = true;
    }

    public function render()
    {
        return view('livewire.siswa.assessment-wizard');
    }
}
