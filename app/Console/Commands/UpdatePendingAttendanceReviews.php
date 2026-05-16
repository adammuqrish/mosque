<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class UpdatePendingAttendanceReviews extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:mark-pending';

    /**
     * The console command description.
     */
    protected $description = 'Auto-mark confirmed attendance as pending_review for events that passed 24+ hours ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // STEP 1: Find events that passed more than 24 hours ago
        // STEP 1: Find events that passed immediately (for testing) - this will show the yellow button as soon as the event is past.
        // Original delayed review logic:
        // $events = Event::needsAttendanceReview()->get();

        // Immediate review logic for testing: use events that have already passed.
        $events = Event::where('event_date', '<=', now())
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalUpdated = 0;

        foreach ($events as $event) {
            // STEP 2: Get all volunteers with 'confirmed' status
            $confirmedVolunteers = $event->volunteers()
                ->wherePivot('attendance_status', 'confirmed')
                ->get();

            if ($confirmedVolunteers->count() > 0) {
                // STEP 3: Update their status to pending_review
                foreach ($confirmedVolunteers as $volunteer) {
                    $event->volunteers()->updateExistingPivot($volunteer->id, [
                        'attendance_status' => 'pending_review',
                    ]);
                }

                $totalUpdated += $confirmedVolunteers->count();
                $this->info("Event '{$event->title}': {$confirmedVolunteers->count()} volunteers marked as pending_review");
            }
        }

        if ($totalUpdated === 0) {
            $this->info('No attendance records needed updating.');
        } else {
            $this->info("Total: {$totalUpdated} volunteers updated to pending_review.");
        }

        return 0;
    }
}
