<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PointsAdjustmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $points;
    protected $reason;
    protected $type;

    public function __construct(User $user, int $points, string $reason, string $type = 'adjusted')
    {
        $this->user = $user;
        $this->points = $points;
        $this->reason = $reason;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $absPoints = abs($this->points);
        $title = '';
        $message = '';
        $icon = '';
        $iconColor = '';

        if ($this->type === 'revoked') {
            $title = 'Points Revoked';
            $message = '<strong>' . number_format($absPoints) . ' points</strong> have been <span class="text-red-600 font-semibold">revoked</span>.<br><em>Reason: ' . e($this->reason) . '</em>';
            $icon = 'fa-minus-circle';
            $iconColor = 'text-red-500';
        } else {
            $title = 'Points Adjusted';
            $message = '<strong>' . number_format($this->points) . ' points</strong> have been <span class="text-green-600 font-semibold">added</span> to your account.<br><em>Reason: ' . e($this->reason) . '</em>';
            $icon = 'fa-plus-circle';
            $iconColor = 'text-green-500';
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'points' => $this->points,
            'action' => $this->type,
            'url' => route('gamification.dashboard'),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
