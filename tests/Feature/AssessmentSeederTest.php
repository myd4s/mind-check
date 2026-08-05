<?php

namespace Tests\Feature;

use App\Models\Assessment;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\QuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_default_pss10_assessment_with_ten_questions_in_order(): void
    {
        $this->seed(QuestionSeeder::class);
        $this->seed(AssessmentSeeder::class);

        $assessment = Assessment::where('title', 'Asesmen Stress PSS-10')->first();

        $this->assertNotNull($assessment);
        $this->assertSame(10, $assessment->questions()->count());
        $this->assertSame(
            range(1, 10),
            $assessment->questions->pluck('pivot.order')->all()
        );
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(QuestionSeeder::class);
        $this->seed(AssessmentSeeder::class);
        $this->seed(AssessmentSeeder::class);

        $this->assertSame(1, Assessment::where('title', 'Asesmen Stress PSS-10')->count());
    }
}
