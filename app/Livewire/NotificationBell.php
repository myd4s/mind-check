<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $notificationId): mixed
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        if ($assessmentId = $notification->data['assessment_id'] ?? null) {
            return $this->redirect(route('assessment.show', $assessmentId), navigate: true);
        }

        return null;
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->each->markAsRead();
    }

    #[Computed]
    public function notifications()
    {
        return auth()->user()->notifications()->latest()->take(10)->get();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
