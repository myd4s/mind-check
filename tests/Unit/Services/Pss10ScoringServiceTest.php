<?php

namespace Tests\Unit\Services;

use App\Models\Question;
use App\Services\Pss10ScoringService;
use PHPUnit\Framework\TestCase;

class Pss10ScoringServiceTest extends TestCase
{
    private function question(bool $reverseScored): Question
    {
        return new Question(['reverse_scored' => $reverseScored]);
    }

    public function test_non_reverse_item_score_equals_raw_answer(): void
    {
        $service = new Pss10ScoringService;

        $this->assertSame(0, $service->scoreItem($this->question(false), 0));
        $this->assertSame(3, $service->scoreItem($this->question(false), 3));
        $this->assertSame(4, $service->scoreItem($this->question(false), 4));
    }

    public function test_reverse_item_score_is_inverted(): void
    {
        $service = new Pss10ScoringService;

        $this->assertSame(4, $service->scoreItem($this->question(true), 0));
        $this->assertSame(2, $service->scoreItem($this->question(true), 2));
        $this->assertSame(0, $service->scoreItem($this->question(true), 4));
    }

    public function test_known_answer_total_matches_manual_calculation(): void
    {
        $service = new Pss10ScoringService;

        // 6 soal non-reverse (order 1,2,3,6,9,10) dijawab 2, 4 soal reverse (order 4,5,7,8) dijawab 2.
        // Non-reverse: 2*6=12. Reverse: (4-2)*4=8. Total = 20.
        $reverseFlags = [false, false, false, true, true, false, true, true, false, false];
        $total = 0;

        foreach ($reverseFlags as $isReverse) {
            $total += $service->scoreItem($this->question($isReverse), 2);
        }

        $this->assertSame(20, $total);
        $this->assertSame('sedang', $service->categorize($total));
    }

    public function test_minimum_possible_score_is_rendah(): void
    {
        $service = new Pss10ScoringService;

        // Non-reverse dijawab 0 (skor 0), reverse dijawab 4 (skor 4-4=0) -> total 0.
        $reverseFlags = [false, false, false, true, true, false, true, true, false, false];
        $total = 0;

        foreach ($reverseFlags as $isReverse) {
            $total += $service->scoreItem($this->question($isReverse), $isReverse ? 4 : 0);
        }

        $this->assertSame(0, $total);
        $this->assertSame('rendah', $service->categorize($total));
    }

    public function test_maximum_possible_score_is_tinggi(): void
    {
        $service = new Pss10ScoringService;

        // Non-reverse dijawab 4 (skor 4), reverse dijawab 0 (skor 4-0=4) -> total 40.
        $reverseFlags = [false, false, false, true, true, false, true, true, false, false];
        $total = 0;

        foreach ($reverseFlags as $isReverse) {
            $total += $service->scoreItem($this->question($isReverse), $isReverse ? 0 : 4);
        }

        $this->assertSame(40, $total);
        $this->assertSame('tinggi', $service->categorize($total));
    }

    public function test_category_boundaries(): void
    {
        $service = new Pss10ScoringService;

        $this->assertSame('rendah', $service->categorize(0));
        $this->assertSame('rendah', $service->categorize(13));
        $this->assertSame('sedang', $service->categorize(14));
        $this->assertSame('sedang', $service->categorize(26));
        $this->assertSame('tinggi', $service->categorize(27));
        $this->assertSame('tinggi', $service->categorize(40));
    }

    public function test_category_boundaries_scale_with_extra_questions(): void
    {
        $service = new Pss10ScoringService;

        // 10 soal inti + 10 soal pendamping = 20 soal, skor maks 80.
        // Cut-off proporsional: rendah <=26, sedang <=52, tinggi >52.
        $this->assertSame('rendah', $service->categorize(26, 20));
        $this->assertSame('sedang', $service->categorize(27, 20));
        $this->assertSame('sedang', $service->categorize(52, 20));
        $this->assertSame('tinggi', $service->categorize(53, 20));
    }
}
