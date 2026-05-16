<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPoints extends Model
{
    protected $fillable = [
        'user_id',
        'total_points',
        'available_points',
        'redeemed_points',
        'current_streak',
        'longest_streak',
        'last_activity_date',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'available_points' => 'integer',
        'redeemed_points' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'last_activity_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTierAttribute(): ?TierMilestone
    {
        return app(\App\Services\GamificationService::class)->getTierForPoints($this->total_points);
    }

    public function getProgressToNextTierAttribute(): array
    {
        $currentTier = $this->tier;
        $currentTierMinPoints = $currentTier ? $currentTier->min_points : 0;
        $nextTier = TierMilestone::where('min_points', '>', $currentTierMinPoints)
            ->orderBy('min_points')
            ->first();

        if (!$nextTier) {
            return ['progress' => 100, 'points_needed' => 0, 'next_tier' => null, 'current_tier' => $currentTier];
        }

        $pointsInCurrentTier = $this->total_points - $currentTierMinPoints;
        $pointsNeededForNextTier = $nextTier->min_points - $currentTierMinPoints;
        $progress = $pointsNeededForNextTier > 0 ? ($pointsInCurrentTier / $pointsNeededForNextTier) * 100 : 100;

        return [
            'progress' => min(100, round($progress, 1)),
            'points_needed' => max(0, $nextTier->min_points - $this->total_points),
            'next_tier' => $nextTier,
            'current_tier' => $currentTier,
        ];
    }
}
