<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Event;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Services\CertificateService;
use App\Services\GamificationService;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    protected $gamificationService;
    protected $leaderboardService;

    public function __construct(
        GamificationService $gamificationService,
        LeaderboardService $leaderboardService
    ) {
        $this->gamificationService = $gamificationService;
        $this->leaderboardService = $leaderboardService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $stats = $this->getGamificationStats($user);
        $allBadges = Badge::where('is_active', true)->get();
        $earnedBadgeIds = $user->badgeEarnings()->pluck('badge_id')->toArray();
        
        $nextBadges = $this->getNextBadges($user);
        
        $leaderboard = $this->leaderboardService->getGlobalLeaderboard(5);
        $userRank = $this->leaderboardService->getUserRank($user);

        $upcomingEvents = Event::where('status', 'open')
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->limit(6)
            ->get();

        return view('gamification.dashboard', compact(
            'stats',
            'allBadges',
            'earnedBadgeIds',
            'nextBadges',
            'leaderboard',
            'userRank',
            'upcomingEvents'
        ));
    }

    public function pointsHistory(Request $request)
    {
        $user = Auth::user();
        $transactions = $user->pointTransactions()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('gamification.points-history', compact('transactions'));
    }

    public function badges()
    {
        $user = Auth::user();
        $allBadges = Badge::where('is_active', true)
            ->orderBy('tier')
            ->orderBy('points_awarded', 'desc')
            ->get();
        $earnedBadges = $user->badgeEarnings()->with('badge')->get()->pluck('badge');
        
        return view('gamification.badges', compact('allBadges', 'earnedBadges'));
    }

    public function rewards(Request $request)
    {
        $category = $request->get('category');
        $rewards = Reward::where('is_active', true)
            ->when($category, function($q) use ($category) {
                $q->where('category', $category);
            })
            ->get()
            ->filter(function($r) {
                return $r->isAvailable();
            });

        $userPoints = $this->gamificationService->getAvailablePoints(Auth::user());
        $myRedemptions = Auth::user()->rewardRedemptions()
            ->with('reward')
            ->orderByDesc('redeemed_at')
            ->paginate(5);

        return view('gamification.rewards', compact('rewards', 'userPoints', 'category', 'myRedemptions'));
    }

    public function redeem(Request $request, Reward $reward)
    {
        $result = $this->gamificationService->redeemReward(Auth::user(), $reward);

        if ($result['status'] === 'success') {
            return redirect()
                ->back()
                ->with('success', "Successfully redeemed {$reward->name}! Your claim code: {$result['redemption']->claim_code}");
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function leaderboard(Request $request)
    {
        $type = $request->get('type', 'global');
        $category = $request->get('category');
        $limit = 20;

        if ($type === 'monthly') {
            $leaderboard = $this->leaderboardService->getMonthlyLeaderboard(null, null, $limit);
        } elseif ($type === 'category') {
            $leaderboard = $this->leaderboardService->getCategoryLeaderboard($category ?? 'religious', $limit);
        } else {
            $leaderboard = $this->leaderboardService->getGlobalLeaderboard($limit);
        }

        $userRank = $this->leaderboardService->getUserRank(Auth::user());

        return view('gamification.leaderboard', compact('leaderboard', 'userRank', 'type', 'category'));
    }

    public function myRedemptions()
    {
        $redemptions = Auth::user()->rewardRedemptions()
            ->with('reward')
            ->orderByDesc('redeemed_at')
            ->paginate(15);

        return view('gamification.my-redemptions', compact('redemptions'));
    }

    public function downloadCertificate(RewardRedemption $redemption, CertificateService $certService)
    {
        if ($redemption->user_id !== Auth::id()) {
            abort(403);
        }
        if ($redemption->status !== 'claimed') {
            abort(403, 'Certificate not available.');
        }
        return $certService->downloadCertificate($redemption);
    }

    private function getGamificationStats($user): array
    {
        $memberPoints = $user->memberPoints;
        $tier = $memberPoints ? $memberPoints->tier : null;
        $progress = $memberPoints ? $memberPoints->progress_to_next_tier : null;
        
        return [
            'total_points' => $memberPoints ? $memberPoints->total_points : 0,
            'available_points' => $memberPoints ? $memberPoints->available_points : 0,
            'redeemed_points' => $memberPoints ? $memberPoints->redeemed_points : 0,
            'tier' => $tier ? $tier->tier : null,
            'tier_name' => $tier ? $tier->name : null,
            'tier_icon' => $tier ? $tier->icon_svg : null,
            'current_streak' => $memberPoints ? $memberPoints->current_streak : 0,
            'longest_streak' => $memberPoints ? $memberPoints->longest_streak : 0,
            'badge_count' => $user->badgeEarnings()->count(),
            'tier_progress' => $progress,
        ];
    }

    private function getNextBadges($user): array
    {
        $earnedCodes = $user->badgeEarnings()->with('badge')->get()->pluck('badge.code')->toArray();
        $completedEvents = \App\Models\EventVolunteer::where('user_id', $user->id)
            ->where('attendance_status', 'completed')
            ->count();

        $nextBadges = [];
        $criteria = [
            'first_step' => 1,
            'consistent' => 5,
            'dedicated' => 10,
            'helping_hand' => 25,
        ];

        foreach ($criteria as $code => $required) {
            if (!in_array($code, $earnedCodes)) {
                $badge = Badge::where('code', $code)->first();
                if ($badge) {
                    $nextBadges[] = [
                        'badge' => $badge,
                        'progress' => min(100, ($completedEvents / $required) * 100),
                        'remaining' => max(0, $required - $completedEvents),
                    ];
                }
            }
        }

        return array_slice($nextBadges, 0, 3);
    }
}
