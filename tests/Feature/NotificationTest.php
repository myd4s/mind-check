<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Events\AssessmentCompleted;
use App\Livewire\NotificationBell;
use App\Models\Assessment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ConcerningAssessmentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAssessment(string $overallSeverity): Assessment
    {
        $class = SchoolClass::create(['name' => 'X IPA 1']);
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => fake()->unique()->numerify('##########'),
            'gender' => Gender::Male,
            'status' => 'active',
        ]);

        return Assessment::create([
            'student_id' => $student->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
            'depression_raw' => 10, 'anxiety_raw' => 10, 'stress_raw' => 10,
            'depression_score' => 15, 'anxiety_score' => 15, 'stress_score' => 15,
            'depression_severity' => 'moderate', 'anxiety_severity' => 'moderate', 'stress_severity' => 'moderate',
            'overall_severity' => $overallSeverity,
        ]);
    }

    public function test_concerning_assessment_notifies_all_counselors(): void
    {
        Notification::fake();

        $counselor1 = User::factory()->counselor()->create();
        $counselor2 = User::factory()->counselor()->create();
        $assessment = $this->makeAssessment('severe');

        event(new AssessmentCompleted($assessment));

        Notification::assertSentTo([$counselor1, $counselor2], ConcerningAssessmentNotification::class);
    }

    public function test_normal_assessment_does_not_notify_counselors(): void
    {
        Notification::fake();

        $counselor = User::factory()->counselor()->create();
        $assessment = $this->makeAssessment('normal');

        event(new AssessmentCompleted($assessment));

        Notification::assertNotSentTo($counselor, ConcerningAssessmentNotification::class);
    }

    public function test_counselor_receives_actual_database_notification_with_correct_data(): void
    {
        $counselor = User::factory()->counselor()->create();
        $assessment = $this->makeAssessment('extremely_severe');

        event(new AssessmentCompleted($assessment));

        $this->assertDatabaseCount('notifications', 1);
        $notification = $counselor->notifications()->firstOrFail();

        $this->assertSame($assessment->id, $notification->data['assessment_id']);
        $this->assertSame('extremely_severe', $notification->data['severity']);
    }

    public function test_notification_bell_shows_correct_unread_count(): void
    {
        $counselor = User::factory()->counselor()->create();
        $assessment = $this->makeAssessment('severe');

        event(new AssessmentCompleted($assessment));
        event(new AssessmentCompleted($this->makeAssessment('extremely_severe')));

        Livewire::actingAs($counselor)
            ->test(NotificationBell::class)
            ->assertSet('unreadCount', 2);
    }

    public function test_mark_as_read_marks_notification_and_redirects(): void
    {
        $counselor = User::factory()->counselor()->create();
        $assessment = $this->makeAssessment('severe');

        event(new AssessmentCompleted($assessment));

        $notification = $counselor->notifications()->firstOrFail();

        Livewire::actingAs($counselor)
            ->test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertRedirect(route('assessment.show', $assessment));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_as_read(): void
    {
        $counselor = User::factory()->counselor()->create();
        event(new AssessmentCompleted($this->makeAssessment('severe')));
        event(new AssessmentCompleted($this->makeAssessment('extremely_severe')));

        Livewire::actingAs($counselor)
            ->test(NotificationBell::class)
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0);

        $this->assertSame(0, $counselor->unreadNotifications()->count());
    }
}
