<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class MarkPendingAttendance extends Command
{
    protected $signature = 'attendance:mark-pending';

    protected $description = 'Mark all confirmed volunteers for past events (24h+) as pending review';

    public function handle(): int
    {
        $this->info('Checking for events needing attendance review...');

        $events = Event::needsAttendanceReview()->get();
        $totalUpdated = 0;

        foreach ($events as $event) {
            $updated = $event->volunteers()
                ->wherePivot('attendance_status', 'confirmed')
                ->count();

            if ($updated > 0) {
                $event->volunteers()
                    ->wherePivot('attendance_status', 'confirmed')
                    ->update([
                        'attendance_status' => 'pending_review',
                    ]);

                $totalUpdated += $updated;
                $this->line("  ✓ Updated {$updated} volunteers for event: {$event->title}");
            }
        }

        if ($totalUpdated > 0) {
            $this->info("Completed! Updated {$totalUpdated} volunteer attendance records.");
        } else {
            $this->info('No attendance records needed updating.');
        }

        return Command::SUCCESS;
    }
}
