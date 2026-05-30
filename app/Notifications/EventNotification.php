<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $action;

    public function __construct(Event $event, string $action = 'created')
    {
        $this->event = $event;
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
                $title = 'New Event Available';
                $message = 'A new event <strong>' . e($this->event->title) . '</strong> is now open for registration on <strong>' . $this->event->event_date->format('d M Y') . '</strong>. <a href="' . route('volunteer.my-events') . '" class="text-blue-600 hover:underline">Join now</a>.';
                $icon = 'fa-calendar-plus';
                $iconColor = 'text-blue-500';
                break;

            case 'cancelled':
                $title = 'Event Cancelled';
                $message = 'The event <strong>' . e($this->event->title) . '</strong> scheduled on <strong>' . $this->event->event_date->format('d M Y') . '</strong> has been <span class="text-red-600 font-semibold">CANCELLED</span>.';
                $icon = 'fa-calendar-times';
                $iconColor = 'text-red-500';
                break;

            case 'deleted':
                $title = 'Event Removed';
                $message = 'The event <strong>' . e($this->event->title) . '</strong> scheduled on <strong>' . $this->event->event_date->format('d M Y') . '</strong> has been <span class="text-red-600 font-semibold">REMOVED</span>.';
                $icon = 'fa-trash-alt';
                $iconColor = 'text-red-500';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'event_id' => $this->event->id,
            'action' => $this->action,
            'url' => route('volunteer.my-events'),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
