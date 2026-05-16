<?php

namespace App\Notifications;

use App\Models\TierMilestone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TierUpgradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tier;

    public function __construct(TierMilestone $tier)
    {
        $this->tier = $tier;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tier_upgrade',
            'tier' => $this->tier->tier,
            'tier_name' => $this->tier->name,
            'tier_icon' => $this->tier->icon_svg,
            'message' => "Congratulations! You've reached {$this->tier->name} tier!",
            'icon' => 'chevron-up',
            'color' => 'emerald',
        ];
    }
}
