<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PointsEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $points;
    public $event;

    public function __construct(int $points, Event $event)
    {
        $this->points = $points;
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'points_earned',
            'points' => $this->points,
            'event_title' => $this->event->title,
            'message' => "You earned {$this->points} points for completing '{$this->event->title}'!",
            'icon' => 'star',
            'color' => 'emerald',
        ];
    }
}
