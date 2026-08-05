<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Question;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Seed paket asesmen default berisi 10 soal inti PSS-10.
     */
    public function run(): void
    {
        $assessment = Assessment::updateOrCreate(
            ['title' => 'Asesmen Stress PSS-10'],
            ['description' => 'Paket asesmen standar berbasis Perceived Stress Scale (PSS-10) untuk mengukur tingkat stress siswa.']
        );

        $coreQuestions = Question::where('is_core', true)->orderBy('order')->get();

        $assessment->questions()->sync(
            $coreQuestions->mapWithKeys(fn (Question $question) => [
                $question->id => ['order' => $question->order],
            ])
        );
    }
}
