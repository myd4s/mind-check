<?php

namespace App\Livewire\Questionnaire;

use App\Enums\AssessmentStatus;
use App\Events\AssessmentCompleted;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\Question;
use App\Services\DassScoringService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Wizard extends Component
{
    public int $assessmentId;

    public int $currentIndex = 0;

    /** @var array<int, int> question_id => answer_value (0-4) */
    public array $answers = [];

    public const LIKERT_OPTIONS = [
        0 => 'Tidak Pernah',
        1 => 'Jarang',
        2 => 'Kadang-kadang',
        3 => 'Sering',
        4 => 'Selalu',
    ];

    public function mount(): void
    {
        $student = auth()->user()->student;

        abort_if(! $student, 403, 'Profil siswa tidak ditemukan.');

        $assessment = Assessment::firstOrCreate(
            ['student_id' => $student->id, 'status' => AssessmentStatus::InProgress],
            ['started_at' => now()]
        );

        $this->assessmentId = $assessment->id;
        $this->answers = $assessment->answers()->pluck('answer_value', 'question_id')->toArray();
    }

    #[Computed]
    public function questions(): Collection
    {
        return Question::orderBy('order_number')->get();
    }

    #[Computed]
    public function currentQuestion(): Question
    {
        return $this->questions[$this->currentIndex];
    }

    #[Computed]
    public function progressPercent(): int
    {
        return (int) round((count($this->answers) / $this->questions->count()) * 100);
    }

    public function selectAnswer(int $questionId, int $value): void
    {
        AssessmentAnswer::updateOrCreate(
            ['assessment_id' => $this->assessmentId, 'question_id' => $questionId],
            ['answer_value' => $value]
        );

        $this->answers[$questionId] = $value;

        if ($this->currentIndex < $this->questions->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function previous(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function next(): void
    {
        if ($this->currentIndex < $this->questions->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function goTo(int $index): void
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentIndex = $index;
        }
    }

    public function submit(DassScoringService $scoringService)
    {
        if (count($this->answers) < $this->questions->count()) {
            $this->addError('form', 'Harap jawab semua pertanyaan sebelum mengirim.');

            return;
        }

        $assessment = Assessment::findOrFail($this->assessmentId);
        $scoringService->scoreAssessment($assessment);

        AssessmentCompleted::dispatch($assessment);

        return $this->redirect(route('student.result', $assessment), navigate: true);
    }

    #[Layout('layouts.focused')]
    public function render()
    {
        return view('livewire.questionnaire.wizard');
    }
}
