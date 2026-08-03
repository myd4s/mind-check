<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\AssessmentCompleted;
use App\Models\User;
use App\Notifications\ConcerningAssessmentNotification;
use Illuminate\Support\Facades\Notification;

class NotifyCounselorsOfConcerningAssessment
{
    public function handle(AssessmentCompleted $event): void
    {
        $assessment = $event->assessment;

        if (! $assessment->overall_severity->isConcerning()) {
            return;
        }

        $counselors = User::where('role', UserRole::Counselor)->get();

        Notification::send($counselors, new ConcerningAssessmentNotification($assessment));
    }
}
