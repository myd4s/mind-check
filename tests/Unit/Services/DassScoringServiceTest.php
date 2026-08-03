<?php

namespace Tests\Unit\Services;

use App\Enums\Severity;
use App\Enums\Subscale;
use App\Services\DassScoringService;
use PHPUnit\Framework\TestCase;

class DassScoringServiceTest extends TestCase
{
    private DassScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DassScoringService();
    }

    public function test_score_scales_raw_sum_by_one_point_five(): void
    {
        $this->assertSame(0, $this->service->score(0));
        $this->assertSame(8, $this->service->score(5));
        $this->assertSame(42, $this->service->score(28));
    }

    public function test_zero_raw_sum_is_normal_for_every_subscale(): void
    {
        foreach (Subscale::cases() as $subscale) {
            $this->assertSame(Severity::Normal, $this->service->severityFor($subscale, 0));
        }
    }

    public function test_max_raw_sum_is_extremely_severe_for_every_subscale(): void
    {
        foreach (Subscale::cases() as $subscale) {
            $this->assertSame(Severity::ExtremelySevere, $this->service->severityFor($subscale, 42));
        }
    }

    public function test_anxiety_score_of_eight_is_mild(): void
    {
        $score = $this->service->score(5);

        $this->assertSame(8, $score);
        $this->assertSame(Severity::Mild, $this->service->severityFor(Subscale::Anxiety, $score));
    }

    public function test_stress_score_of_fifteen_is_mild(): void
    {
        $score = $this->service->score(10);

        $this->assertSame(15, $score);
        $this->assertSame(Severity::Mild, $this->service->severityFor(Subscale::Stress, $score));
    }

    public function test_depression_score_of_twenty_nine_is_extremely_severe(): void
    {
        $score = $this->service->score(19);

        $this->assertSame(29, $score);
        $this->assertSame(Severity::ExtremelySevere, $this->service->severityFor(Subscale::Depression, $score));
    }

    public function test_severity_boundaries_are_inclusive(): void
    {
        $this->assertSame(Severity::Normal, $this->service->severityFor(Subscale::Anxiety, 7));
        $this->assertSame(Severity::Mild, $this->service->severityFor(Subscale::Anxiety, 8));
        $this->assertSame(Severity::Mild, $this->service->severityFor(Subscale::Anxiety, 9));
        $this->assertSame(Severity::Moderate, $this->service->severityFor(Subscale::Anxiety, 10));
    }
}
