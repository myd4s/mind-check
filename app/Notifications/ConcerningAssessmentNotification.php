<?php

namespace App\Notifications;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConcerningAssessmentNotification extends Notification
{
    use Queueable;

    public function __construct(public Assessment $assessment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $studentName = $this->assessment->student->user->name;
        $severityLabel = $this->assessment->overall_severity->label();

        return [
            'assessment_id' => $this->assessment->id,
            'student_name' => $studentName,
            'severity' => $this->assessment->overall_severity->value,
            'severity_label' => $severityLabel,
            'message' => "Siswa {$studentName} memiliki hasil asesmen dengan tingkat {$severityLabel}, perlu ditindaklanjuti.",
        ];
    }
}
