<?php

namespace App\Services;

use App\Models\User;
use App\Models\MemberPoints;
use App\Models\BadgeEarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    public const CACHE_TTL_MINUTES = 60;

    public function getGlobalLeaderboard(int $limit = 10): array
    {
        return Cache::remember('leaderboard_global', self::CACHE_TTL_MINUTES * 60, function () use ($limit) {
            return $this->buildLeaderboard(MemberPoints::orderByDesc('total_points'), $limit);
        });
    }

    public function getMonthlyLeaderboard(?int $year = null, ?int $month = null, int $limit = 10): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        $cacheKey = "leaderboard_monthly_{$year}_{$month}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($year, $month, $limit) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $userIds = \App\Models\PointTransaction::where('type', 'earned')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('user_id')
                ->selectRaw('user_id, SUM(points) as total')
                ->orderByDesc('total')
                ->limit($limit)
                ->pluck('user_id');

            $query = MemberPoints::whereIn('user_id', $userIds);
            
            return $this->buildLeaderboard($query, $limit);
        });
    }

    public function getCategoryLeaderboard(string $category, int $limit = 10): array
    {
        $cacheKey = "leaderboard_category_{$category}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($category, $limit) {
            // Get users who completed events in this category, ordered by event count
            $completedEvents = \App\Models\EventVolunteer::where('attendance_status', 'completed')
                ->whereHas('event', function($q) use ($category) {
                    $q->where('gamification_category', $category);
                })
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as event_count')
                ->orderByDesc('event_count')
                ->limit($limit)
                ->get();

            $leaderboard = [];
            $position = 1;
            $gamificationService = app(GamificationService::class);

            foreach ($completedEvents as $entry) {
                $user = \App\Models\User::find($entry->user_id);
                if (!$user || $user->hide_from_leaderboard) {
                    continue;
                }

                $memberPoints = \App\Models\MemberPoints::where('user_id', $user->id)->first();
                $tier = $memberPoints ? $gamificationService->getTierForPoints($memberPoints->total_points) : null;

                $leaderboard[] = [
                    'position' => $position++,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'points' => $entry->event_count, // Show event count for category
                    'tier' => $tier ? $tier->tier : null,
                    'tier_icon' => $tier ? $tier->icon_svg : null,
                    'badge_count' => \App\Models\BadgeEarning::where('user_id', $user->id)->count(),
                ];
            }

            return $leaderboard;
        });
    }

    public function getUserRank(User $user): array
    {
        $globalRank = MemberPoints::where('total_points', '>', function ($q) use ($user) {
            $q->select('total_points')
              ->from('member_points')
              ->where('user_id', $user->id);
        })->count() + 1;

        return [
            'global' => $globalRank,
            'monthly' => $this->getMonthlyUserRank($user),
            'total_members' => MemberPoints::count(),
        ];
    }

    public function getMonthlyUserRank(User $user): int
    {
        $userPoints = $this->getUserMonthlyPoints($user);
        
        return \App\Models\PointTransaction::where('user_id', '!=', $user->id)
            ->where('type', 'earned')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(points) as total')
            ->having('total', '>', $userPoints)
            ->count() + 1;
    }

    public function getUserMonthlyPoints(User $user): int
    {
        return (int) \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('type', 'earned')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('points');
    }

    private function buildLeaderboard($query, int $limit): array
    {
        $leaderboard = [];
        $position = 1;
        $gamificationService = app(GamificationService::class);

        foreach ($query->limit($limit)->get() as $memberPoints) {
            $user = $memberPoints->user;
            
            if ($user->hide_from_leaderboard) {
                continue;
            }

            $tier = $gamificationService->getTierForPoints($memberPoints->total_points);

            $leaderboard[] = [
                'position' => $position++,
                'user_id' => $user->id,
                'name' => $user->name,
                'points' => $memberPoints->total_points,
                'tier' => $tier ? $tier->tier : null,
                'tier_icon' => $tier ? $tier->icon_svg : null,
                'badge_count' => BadgeEarning::where('user_id', $user->id)->count(),
            ];
        }

        return $leaderboard;
    }

    public function clearCache(): void
    {
        Cache::forget('leaderboard_global');
        
        for ($month = 1; $month <= 12; $month++) {
            Cache::forget("leaderboard_monthly_" . now()->year . "_{$month}");
        }
    }
}
