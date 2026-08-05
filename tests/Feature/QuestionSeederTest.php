<?php

namespace Tests\Feature;

use App\Models\Question;
use Database\Seeders\QuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_ten_core_pss10_questions_with_correct_reverse_scoring(): void
    {
        $this->seed(QuestionSeeder::class);

        $this->assertSame(10, Question::where('is_core', true)->count());

        $reverseScoredOrders = Question::where('is_core', true)
            ->where('reverse_scored', true)
            ->pluck('order')
            ->sort()
            ->values()
            ->all();

        $this->assertSame([4, 5, 7, 8], $reverseScoredOrders);

        $this->assertSame(
            range(1, 10),
            Question::where('is_core', true)->orderBy('order')->pluck('order')->all()
        );
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(QuestionSeeder::class);
        $this->seed(QuestionSeeder::class);

        $this->assertSame(10, Question::where('is_core', true)->count());
    }
}
