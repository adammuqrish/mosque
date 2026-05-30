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
    public $breakdown;

    public function __construct(int $points, Event $event, ?array $breakdown = null)
    {
        $this->points = $points;
        $this->event = $event;
        $this->breakdown = $breakdown;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $breakdownText = '';
        if ($this->breakdown) {
            $parts = [];
            if (isset($this->breakdown['base'])) {
                $parts[] = "{$this->breakdown['base']} base";
            }
            if (isset($this->breakdown['early_join'])) {
                $parts[] = "{$this->breakdown['early_join']} early join";
            }
            if (isset($this->breakdown['streak_bonus'])) {
                $parts[] = "{$this->breakdown['streak_bonus']} streak";
            }
            if (isset($this->breakdown['category_bonus'])) {
                $parts[] = "{$this->breakdown['category_bonus']} category";
            }
            $breakdownText = !empty($parts) ? ' (' . implode(' + ', $parts) . ')' : '';
        }

        return [
            'type' => 'points_earned',
            'points' => $this->points,
            'event_title' => $this->event->title,
            'message' => "You earned {$this->points} points for completing '{$this->event->title}'{$breakdownText}!",
            'icon' => 'star',
            'color' => 'emerald',
        ];
    }
}
