<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\EventVolunteer;
use App\Models\MemberPoints;
use App\Models\Badge;
use App\Models\BadgeEarning;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\TierMilestone;
use App\Notifications\PointsEarnedNotification;
use App\Notifications\BadgeUnlockedNotification;
use App\Notifications\TierUpgradedNotification;
use App\Services\CertificateService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GamificationService
{
    public const BASE_POINTS = 50;
    public const EARLY_JOIN_BONUS = 10;
    public const STREAK_3_BONUS = 25;
    public const STREAK_5_BONUS = 50;
    public const STREAK_10_BONUS = 100;
    public const HIGH_IMPACT_BONUS = 20;
    public const PROFILE_COMPLETION_BONUS = 20;
    public const REFERRAL_BONUS = 15;

    public const HIGH_IMPACT_CATEGORIES = ['religious', 'education', 'emergency'];

    public function awardPointsForEventCompletion(EventVolunteer $volunteer): array
    {
        if ($volunteer->points_awarded) {
            return ['status' => 'skipped', 'reason' => 'already_awarded'];
        }

        return DB::transaction(function () use ($volunteer) {
            $user = $volunteer->user;
            $event = $volunteer->event;
            $memberPoints = $this->getOrCreateMemberPoints($user);
            
            $pointsBreakdown = $this->calculatePoints($user, $event, $volunteer);
            $totalPoints = array_sum($pointsBreakdown);

            $memberPoints->total_points += $totalPoints;
            $memberPoints->available_points += $totalPoints;
            $memberPoints->last_activity_date = now();
            $this->updateStreak($memberPoints, $event->event_date);
            $memberPoints->save();

            $volunteer->points_awarded = true;
            $volunteer->points_earned = $totalPoints;
            $volunteer->save();

            $this->recordTransaction(
                $user,
                'earned',
                $totalPoints,
                $memberPoints->total_points,
                "Event completion: {$event->title}",
                'event',
                $event->id
            );

            $newBadges = $this->checkAndAwardBadges($user, $event);
            $tierUpgrade = $this->checkTierUpgrade($user, $memberPoints);
            
            if ($totalPoints > 0) {
                $this->notifyPointsEarned($user, $totalPoints, $event);
            }

            return [
                'status' => 'success',
                'points_earned' => $totalPoints,
                'breakdown' => $pointsBreakdown,
                'new_badges' => $newBadges,
                'tier_upgrade' => $tierUpgrade,
                'new_total' => $memberPoints->total_points,
            ];
        });
    }

    public function calculatePoints(User $user, Event $event, EventVolunteer $volunteer): array
    {
        $points = ['base' => self::BASE_POINTS];

        if ($this->isEarlyJoin($event, $volunteer)) {
            $points['early_join'] = self::EARLY_JOIN_BONUS;
        }

        $memberPoints = $this->getOrCreateMemberPoints($user);
        $streakBonus = $this->calculateStreakBonus($memberPoints->current_streak);
        if ($streakBonus > 0) {
            $points['streak_bonus'] = $streakBonus;
        }

        if (in_array($event->gamification_category, self::HIGH_IMPACT_CATEGORIES)) {
            $points['category_bonus'] = self::HIGH_IMPACT_BONUS;
        }

        return $points;
    }

    public function calculateStreakBonus(int $currentStreak): int
    {
        if ($currentStreak >= 10) return self::STREAK_10_BONUS;
        if ($currentStreak >= 5) return self::STREAK_5_BONUS;
        if ($currentStreak >= 3) return self::STREAK_3_BONUS;
        return 0;
    }

    public function updateStreak(MemberPoints $memberPoints, Carbon $eventDate): void
    {
        $lastActivity = $memberPoints->last_activity_date;
        
        if (!$lastActivity) {
            $memberPoints->current_streak = 1;
        } else {
            $daysSinceLastActivity = $lastActivity->diffInDays($eventDate);
            $memberPoints->current_streak = ($daysSinceLastActivity <= 60) 
                ? $memberPoints->current_streak + 1 
                : 1;
        }

        if ($memberPoints->current_streak > $memberPoints->longest_streak) {
            $memberPoints->longest_streak = $memberPoints->current_streak;
        }
    }

    public function checkAndAwardBadges(User $user, ?Event $event = null): array
    {
        $newBadges = [];
        $userBadges = BadgeEarning::where('user_id', $user->id)->pluck('badge_id')->toArray();
        
        $completedEvents = EventVolunteer::where('user_id', $user->id)
            ->where('attendance_status', 'completed')
            ->count();

        $badgeCriteria = [
            'first_step' => 1,
            'consistent' => 5,
            'dedicated' => 10,
            'helping_hand' => 25,
            'masjid_hero' => 50,
        ];

        foreach ($badgeCriteria as $code => $required) {
            if ($completedEvents >= $required) {
                $badge = Badge::where('code', $code)->first();
                if ($badge && !in_array($badge->id, $userBadges)) {
                    $this->awardBadge($user, $badge, $event ? $event->id : null);
                    $newBadges[] = $badge;
                }
            }
        }

        if ($event) {
            $categoryCount = EventVolunteer::where('user_id', $user->id)
                ->where('attendance_status', 'completed')
                ->whereHas('event', function($q) use ($event) {
                    $q->where('gamification_category', $event->gamification_category);
                })
                ->count();

            $categoryBadges = [
                'religious' => ['religious_scholar', 10],
                'emergency' => ['emergency_responder', 5],
            ];

            if (isset($categoryBadges[$event->gamification_category])) {
                [$code, $required] = $categoryBadges[$event->gamification_category];
                if ($categoryCount >= $required) {
                    $badge = Badge::where('code', $code)->first();
                    if ($badge && !in_array($badge->id, $userBadges)) {
                        $this->awardBadge($user, $badge, $event->id);
                        $newBadges[] = $badge;
                    }
                }
            }
        }

        foreach ($newBadges as $badge) {
            $this->addBadgePoints($user, $badge);
        }

        return $newBadges;
    }

    public function awardBadge(User $user, Badge $badge, ?int $eventId = null): BadgeEarning
    {
        $earning = BadgeEarning::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'earned_at' => now(),
            'source_event_id' => $eventId,
        ]);

        $user->notify(new BadgeUnlockedNotification($badge));

        return $earning;
    }

    public function addBadgePoints(User $user, Badge $badge): void
    {
        if ($badge->points_awarded <= 0) return;

        $memberPoints = $this->getOrCreateMemberPoints($user);
        $memberPoints->total_points += $badge->points_awarded;
        $memberPoints->available_points += $badge->points_awarded;
        $memberPoints->save();

        $this->recordTransaction(
            $user,
            'earned',
            $badge->points_awarded,
            $memberPoints->total_points,
            "Badge earned: {$badge->name}",
            'badge',
            $badge->id
        );
    }

    public function checkTierUpgrade(User $user, MemberPoints $memberPoints): ?array
    {
        $currentTier = $this->getTierForPoints($memberPoints->total_points);
        $previousTier = $this->getTierForPoints(max(0, $memberPoints->total_points - 1));

        if ($currentTier && (!$previousTier || $currentTier->tier !== $previousTier->tier)) {
            $user->notify(new TierUpgradedNotification($currentTier));
            return [
                'tier' => $currentTier,
                'from_tier' => $previousTier ? $previousTier->tier : null,
            ];
        }

        return null;
    }

    public function getTierForPoints(int $points): ?TierMilestone
    {
        return TierMilestone::where('min_points', '<=', $points)
            ->orderBy('min_points', 'desc')
            ->first();
    }

    public function redeemReward(User $user, Reward $reward): array
    {
        if ($reward->points_cost > $this->getAvailablePoints($user)) {
            return ['status' => 'error', 'message' => 'Insufficient points'];
        }

        if (!$reward->isAvailable()) {
            return ['status' => 'error', 'message' => 'Reward not available'];
        }

        return DB::transaction(function () use ($user, $reward) {
            $memberPoints = MemberPoints::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$memberPoints || $memberPoints->available_points < $reward->points_cost) {
                throw new \Exception('Insufficient points');
            }

            $memberPoints->available_points -= $reward->points_cost;
            $memberPoints->redeemed_points += $reward->points_cost;
            $memberPoints->save();

            $redemption = RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'status' => 'pending',
                'redeemed_at' => now(),
                'claim_code' => strtoupper(substr(md5($user->id . $reward->id . time()), 0, 8)),
            ]);

            if ($reward->code === 'APPRECIATION_CERT') {
                $redemption->update(['status' => 'claimed', 'claimed_at' => now()]);
                $certService = app(CertificateService::class);
                $certService->generateCertificate($user, $redemption);
            }

            $this->recordTransaction(
                $user,
                'redeemed',
                -$reward->points_cost,
                $memberPoints->available_points,
                "Redeemed: {$reward->name}",
                'reward',
                $reward->id
            );

            return [
                'status' => 'success',
                'redemption' => $redemption,
                'remaining_points' => $memberPoints->available_points,
            ];
        });
    }

    public function getOrCreateMemberPoints(User $user): MemberPoints
    {
        return MemberPoints::firstOrCreate(
            ['user_id' => $user->id],
            ['total_points' => 0, 'available_points' => 0, 'redeemed_points' => 0]
        );
    }

    public function getAvailablePoints(User $user): int
    {
        $mp = MemberPoints::where('user_id', $user->id)->first();
        return $mp ? $mp->available_points : 0;
    }

    public function recordTransaction(
        User $user,
        string $type,
        int $points,
        int $balanceAfter,
        string $reason,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?int $adminId = null,
        ?string $adminNotes = null
    ): PointTransaction {
        return PointTransaction::create([
            'user_id' => $user->id,
            'type' => $type,
            'points' => $points,
            'balance_after' => $balanceAfter,
            'reason' => $reason,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'admin_id' => $adminId,
            'admin_notes' => $adminNotes,
        ]);
    }

    private function isEarlyJoin(Event $event, EventVolunteer $volunteer): bool
    {
        $joinedEarly = $volunteer->joined_at->diffInDays($event->event_date) >= 7;
        $daysUntilEvent = now()->diffInDays($event->event_date, false);
        return $joinedEarly && $daysUntilEvent > 7;
    }

    private function notifyPointsEarned(User $user, int $points, Event $event): void
    {
        $user->notify(new PointsEarnedNotification($points, $event));
    }

    public function generateReferralCode(User $user): string
    {
        $code = strtoupper(substr(md5($user->id . $user->email . time()), 0, 8));
        $user->update(['referred_code' => $code]);
        return $code;
    }

    public function processReferral(User $referrer, User $referred): void
    {
        if ($referrer->id === $referred->id) return;
        if ($referred->referred_by) return;

        $referred->update(['referred_by' => $referrer->id]);

        $memberPoints = $this->getOrCreateMemberPoints($referrer);
        $memberPoints->total_points += self::REFERRAL_BONUS;
        $memberPoints->available_points += self::REFERRAL_BONUS;
        $memberPoints->save();

        $this->recordTransaction(
            $referrer,
            'earned',
            self::REFERRAL_BONUS,
            $memberPoints->total_points,
            "Referral bonus: {$referred->name} joined",
            'referral',
            $referred->id
        );

        $referrer->notify(new \App\Notifications\ReferralBonusNotification($referred, self::REFERRAL_BONUS));
    }
}
