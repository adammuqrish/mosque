<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $donation;
    protected $action;

    public function __construct(Donation $donation, string $action = 'created')
    {
        $this->donation = $donation;
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
            case 'created':
                $title = 'New Donation Recorded';
                $message = 'A new <strong>' . ucfirst($this->donation->category) . '</strong> donation of <strong>RM ' . number_format($this->donation->amount, 2) . '</strong> has been recorded by ' . $this->donation->user->name . '. <a href="' . route('donations.index') . '" class="text-blue-600 hover:underline">Review it</a>.';
                $icon = 'fa-hand-holding-usd';
                $iconColor = 'text-emerald-500';
                break;

            case 'confirmed':
                $title = 'Donation Confirmed';
                $message = 'Your donation of <strong>RM ' . number_format($this->donation->amount, 2) . '</strong> (' . ucfirst($this->donation->category) . ') has been <span class="text-green-600 font-semibold">CONFIRMED</span>.';
                $icon = 'fa-check-circle';
                $iconColor = 'text-green-500';
                break;

            case 'disputed':
                $title = 'Donation Disputed';
                $message = 'Your donation of <strong>RM ' . number_format($this->donation->amount, 2) . '</strong> (' . ucfirst($this->donation->category) . ') has been <span class="text-red-600 font-semibold">DISPUTED</span>. Please check details.';
                $icon = 'fa-exclamation-triangle';
                $iconColor = 'text-red-500';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'donation_id' => $this->donation->id,
            'action' => $this->action,
            'url' => route('donations.index'),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
