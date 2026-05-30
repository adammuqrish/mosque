<?php

namespace App\Notifications;

use App\Models\RewardRedemption;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RewardFulfillmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $redemption;
    protected $action;

    public function __construct(RewardRedemption $redemption, string $action = 'fulfilled')
    {
        $this->redemption = $redemption;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $title = '';
        $message = '';
        $icon = '';
        $iconColor = '';

        switch ($this->action) {
            case 'fulfilled':
                $title = 'Reward Fulfilled';
                $message = 'Your reward <strong>' . e($this->redemption->reward->name) . '</strong> has been <span class="text-green-600 font-semibold">FULFILLED</span>.';
                $icon = 'fa-gift';
                $iconColor = 'text-green-500';
                break;

            case 'rejected':
                $title = 'Reward Rejected';
                $message = 'Your reward <strong>' . e($this->redemption->reward->name) . '</strong> has been <span class="text-red-600 font-semibold">REJECTED</span>.';
                if ($this->redemption->admin_notes) {
                    $message .= '<br><em>Reason: ' . e($this->redemption->admin_notes) . '</em>';
                }
                $message .= '<br>Your points have been refunded.';
                $icon = 'fa-times-circle';
                $iconColor = 'text-red-500';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'redemption_id' => $this->redemption->id,
            'action' => $this->action,
            'url' => route('gamification.dashboard'),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
