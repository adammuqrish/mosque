<?php

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BadgeUnlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $badge;

    public function __construct(Badge $badge)
    {
        $this->badge = $badge;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'badge_unlocked',
            'badge_id' => $this->badge->id,
            'badge_code' => $this->badge->code,
            'badge_name' => $this->badge->name,
            'badge_icon' => $this->badge->icon_svg,
            'points_awarded' => $this->badge->points_awarded,
            'message' => "You unlocked the '{$this->badge->name}' badge! +{$this->badge->points_awarded} points",
            'icon' => 'trophy',
            'color' => 'amber',
        ];
    }
}
