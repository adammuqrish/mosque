<?php

namespace App\Observers;

use App\Models\EventVolunteer;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Log;

class EventVolunteerObserver
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function updated(EventVolunteer $volunteer)
    {
        if ($volunteer->wasChanged('attendance_status') && $volunteer->attendance_status === 'completed') {
            try {
                $result = $this->gamificationService->awardPointsForEventCompletion($volunteer);
                
                Log::info("Gamification: Points awarded", [
                    'user_id' => $volunteer->user_id,
                    'event_id' => $volunteer->event_id,
                    'total_points' => $result['points_earned'],
                    'new_badges' => count($result['new_badges']),
                    'tier_upgrade' => $result['tier_upgrade'],
                ]);
            } catch (\Exception $e) {
                Log::error("Gamification: Failed to award points", [
                    'user_id' => $volunteer->user_id,
                    'event_id' => $volunteer->event_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
