<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReferralBonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $referredUser;
    public $bonusPoints;

    public function __construct(User $referredUser, int $bonusPoints)
    {
        $this->referredUser = $referredUser;
        $this->bonusPoints = $bonusPoints;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'referral_bonus',
            'referred_user_id' => $this->referredUser->id,
            'referred_user_name' => $this->referredUser->name,
            'bonus_points' => $this->bonusPoints,
            'message' => "{$this->referredUser->name} joined using your referral! +{$this->bonusPoints} points",
            'icon' => 'user-plus',
            'color' => 'blue',
        ];
    }
}
