<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the Event "retrieved" event.
     * Automatically close events that have passed their event_date.
     * Also update attendance statuses from confirmed to pending_review for past events.
     */
    public function retrieved(Event $event): void
    {
        if ($event->status !== 'closed' && $event->status !== 'cancelled' && $event->isPast()) {
            $event->update(['status' => 'closed']);
        }

        // Also update attendance statuses for past events
        if ($event->isPast() && $event->status !== 'cancelled') {
            $confirmedVolunteers = $event->volunteers()
                ->wherePivot('attendance_status', 'confirmed')
                ->get();

            if ($confirmedVolunteers->count() > 0) {
                foreach ($confirmedVolunteers as $volunteer) {
                    $event->volunteers()->updateExistingPivot($volunteer->id, [
                        'attendance_status' => 'pending_review',
                    ]);
                }
            }
        }
    }
}
