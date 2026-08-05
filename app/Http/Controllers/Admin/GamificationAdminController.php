<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\Reward;
use App\Models\TierMilestone;
use App\Models\MemberPoints;
use App\Models\PointTransaction;
use App\Models\RewardRedemption;
use App\Services\GamificationService;
use App\Notifications\RewardFulfillmentNotification;
use App\Notifications\PointsAdjustmentNotification;
use App\Http\Requests\BadgeRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\TierMilestoneRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GamificationAdminController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'total_points');
        $direction = $request->get('direction', 'desc');
        
        $allowedSorts = ['total_points', 'available_points', 'redeemed_points', 'current_streak', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'total_points';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $members = MemberPoints::with('user')
            ->when($search, function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(20);

        $totalPoints = MemberPoints::sum('total_points');
        $totalMembers = MemberPoints::count();

        return view('admin.gamification.index', compact('members', 'search', 'totalPoints', 'totalMembers', 'sort', 'direction'));
    }

    public function adjustPoints(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $admin = auth()->user();
        $points = (int) $request->points;
        
        DB::transaction(function () use ($user, $points, $request, $admin) {
            $memberPoints = $this->gamificationService->getOrCreateMemberPoints($user);
            $memberPoints = MemberPoints::where('user_id', $user->id)->lockForUpdate()->first();
            
            if ($points > 0) {
                $memberPoints->total_points += $points;
                $memberPoints->available_points += $points;
                $type = 'adjusted';
            } else {
                $absPoints = abs($points);
                if ($memberPoints->available_points < $absPoints) {
                    $absPoints = $memberPoints->available_points;
                }
                $memberPoints->total_points -= $absPoints;
                $memberPoints->available_points -= $absPoints;
                $points = -$absPoints;
                $type = 'revoked';
            }
            
            $memberPoints->save();

            $this->gamificationService->recordTransaction(
                $user,
                $type,
                $points,
                $memberPoints->total_points,
                $request->reason,
                'admin',
                null,
                $admin->id,
                $request->notes
            );

            $this->gamificationService->checkTierUpgrade($user, $memberPoints);
        });

        $type = $points >= 0 ? 'adjusted' : 'revoked';
        $user->notify(new PointsAdjustmentNotification($user, $points, $request->reason, $type));

        return redirect()
            ->back()
            ->with('success', "Points adjusted for {$user->name}");
    }

    public function viewTransactions(User $user)
    {
        $transactions = PointTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.gamification.transactions', compact('user', 'transactions'));
    }

    public function pendingRedemptions(Request $request)
    {
        $sort = $request->get('sort', 'redeemed_at');
        $direction = $request->get('direction', 'asc');
        
        $allowedSorts = ['redeemed_at', 'points_spent', 'status'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'redeemed_at';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $redemptions = RewardRedemption::with(['user', 'reward'])
            ->where('status', 'pending')
            ->orderBy($sort, $direction)
            ->paginate(20);

        return view('admin.gamification.redemptions', compact('redemptions', 'sort', 'direction'));
    }

    public function fulfillRedemption(Request $request, RewardRedemption $redemption)
    {
        $request->validate([
            'action' => 'required|in:fulfill,reject',
            'notes' => 'nullable|string',
        ]);

        $admin = auth()->user();

        if ($request->action === 'fulfill') {
            $redemption->markAsFulfilled($admin->id, $request->notes);
            $redemption->user->notify(new RewardFulfillmentNotification($redemption, 'fulfilled'));
            $message = 'Reward fulfillment confirmed';
        } else {
            $this->gamificationService->getOrCreateMemberPoints($redemption->user);
            $memberPoints = MemberPoints::where('user_id', $redemption->user_id)->first();
            
            if ($memberPoints) {
                $memberPoints->available_points += $redemption->points_spent;
                $memberPoints->redeemed_points -= $redemption->points_spent;
                $memberPoints->save();

                PointTransaction::create([
                    'user_id' => $redemption->user_id,
                    'type' => 'refunded',
                    'points' => $redemption->points_spent,
                    'balance_after' => $memberPoints->available_points,
                    'reason' => "Reward rejected: {$redemption->reward->name}",
                    'admin_id' => $admin->id,
                    'admin_notes' => $request->notes,
                ]);
            }

            $redemption->markAsRejected($admin->id, $request->notes);
            $redemption->user->notify(new RewardFulfillmentNotification($redemption, 'rejected'));
            $message = 'Reward rejected and points refunded';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function badgesIndex(Request $request)
    {
        $sort = $request->get('sort', 'tier');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['code', 'name', 'tier', 'points_awarded', 'is_active', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'tier';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $badges = Badge::orderBy($sort, $direction)->paginate(20);

        return view('admin.gamification.badges-index', compact('badges', 'sort', 'direction'));
    }

    public function createBadge()
    {
        return view('admin.gamification.badges-form', ['badge' => null]);
    }

    public function storeBadge(BadgeRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $data['icon'] = $request->file('icon')->store('badges', 'public');
            $data['icon_svg'] = null;
        }

        Badge::create($data);

        return redirect()
            ->route('admin.gamification.badges.index')
            ->with('success', 'Badge created successfully.');
    }

    public function editBadge(Badge $badge)
    {
        return view('admin.gamification.badges-form', compact('badge'));
    }

    public function updateBadge(BadgeRequest $request, Badge $badge)
    {
        $data = $request->validated();

        \Log::info('updateBadge request', [
            'hasFile' => $request->hasFile('icon'),
            'files' => $request->file('icon') ? [
                'name' => $request->file('icon')->getClientOriginalName(),
                'size' => $request->file('icon')->getSize(),
                'error' => $request->file('icon')->getError(),
                'realPath' => $request->file('icon')->getRealPath(),
            ] : null,
        ]);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $realPath = $file->getRealPath();
            if ($realPath && is_file($realPath)) {
                if ($badge->icon) {
                    Storage::disk('public')->delete($badge->icon);
                }
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationDir = storage_path('app/public/badges');
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                $moved = copy($realPath, $destination);
                if ($moved) {
                    chmod($destination, 0644);
                    $badge->icon = 'badges/' . $filename;
                    $badge->icon_svg = null;
                }
            } else {
                \Log::info('File not accessible via realPath', [
                    'realPath' => $realPath,
                    'isUploadError' => $file->getError(),
                    'originalName' => $file->getClientOriginalName(),
                    'tempDir' => sys_get_temp_dir(),
                ]);
                if ($file->getError() === UPLOAD_ERR_OK) {
                    if ($badge->icon) {
                        Storage::disk('public')->delete($badge->icon);
                    }
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $destinationDir = storage_path('app/public/badges');
                    if (!is_dir($destinationDir)) {
                        mkdir($destinationDir, 0755, true);
                    }
                    $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                    $handle = $file->openFile('rb');
                    $content = '';
                    while (!$handle->eof()) {
                        $content .= $handle->fread(8192);
                    }
                    file_put_contents($destination, $content);
                    chmod($destination, 0644);
                    $badge->icon = 'badges/' . $filename;
                    $badge->icon_svg = null;
                }
            }
        }

        $badge->fill($data)->save();

        return redirect()
            ->route('admin.gamification.badges.index')
            ->with('success', 'Badge updated successfully.');
    }

    public function toggleBadge(Badge $badge)
    {
        $badge->update(['is_active' => !$badge->is_active]);

        $status = $badge->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.gamification.badges.index')
            ->with('success', "Badge {$status} successfully.");
    }

    public function destroyBadge(Badge $badge)
    {
        if ($badge->icon) {
            Storage::disk('public')->delete($badge->icon);
        }
        $badge->delete();

        return redirect()
            ->route('admin.gamification.badges.index')
            ->with('success', 'Badge deleted successfully.');
    }

    public function rewardsIndex(Request $request)
    {
        $sort = $request->get('sort', 'category');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['code', 'name', 'category', 'points_cost', 'stock_quantity', 'is_active', 'valid_until', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'category';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $rewards = Reward::orderBy($sort, $direction)->paginate(20);

        return view('admin.gamification.rewards-index', compact('rewards', 'sort', 'direction'));
    }

    public function createReward()
    {
        return view('admin.gamification.rewards-form', ['reward' => null]);
    }

    public function storeReward(RewardRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $realPath = $file->getRealPath();
            if (!$realPath || !is_file($realPath)) {
                $handle = $file->openFile('rb');
                $content = '';
                while (!$handle->eof()) {
                    $content .= $handle->fread(8192);
                }
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationDir = storage_path('app/public/rewards');
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                file_put_contents($destination, $content);
                chmod($destination, 0644);
                $data['image'] = 'rewards/' . $filename;
            } else {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationDir = storage_path('app/public/rewards');
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                copy($realPath, $destination);
                chmod($destination, 0644);
                $data['image'] = 'rewards/' . $filename;
            }
            $data['image_svg'] = null;
        }

        Reward::create($data);

        return redirect()
            ->route('admin.gamification.rewards.index')
            ->with('success', 'Reward created successfully.');
    }

    public function editReward(Reward $reward)
    {
        return view('admin.gamification.rewards-form', compact('reward'));
    }

    public function updateReward(RewardRequest $request, Reward $reward)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $realPath = $file->getRealPath();
            if ($realPath && is_file($realPath)) {
                if ($reward->image) {
                    Storage::disk('public')->delete($reward->image);
                }
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationDir = storage_path('app/public/rewards');
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                copy($realPath, $destination);
                chmod($destination, 0644);
                $reward->image = 'rewards/' . $filename;
                $reward->image_svg = null;
            } else {
                if ($reward->image) {
                    Storage::disk('public')->delete($reward->image);
                }
                $handle = $file->openFile('rb');
                $content = '';
                while (!$handle->eof()) {
                    $content .= $handle->fread(8192);
                }
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationDir = storage_path('app/public/rewards');
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destination = $destinationDir . DIRECTORY_SEPARATOR . $filename;
                file_put_contents($destination, $content);
                chmod($destination, 0644);
                $reward->image = 'rewards/' . $filename;
                $reward->image_svg = null;
            }
        }

        $reward->fill($data)->save();

        return redirect()
            ->route('admin.gamification.rewards.index')
            ->with('success', 'Reward updated successfully.');
    }

    public function toggleReward(Reward $reward)
    {
        $reward->update(['is_active' => !$reward->is_active]);

        $status = $reward->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.gamification.rewards.index')
            ->with('success', "Reward {$status} successfully.");
    }

    public function destroyReward(Reward $reward)
    {
        if ($reward->redemptions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete a reward that has been redeemed. Deactivate it instead.');
        }

        if ($reward->image) {
            Storage::disk('public')->delete($reward->image);
        }
        $reward->delete();

        return redirect()
            ->route('admin.gamification.rewards.index')
            ->with('success', 'Reward deleted successfully.');
    }

    public function tiersIndex()
    {
        $tiers = TierMilestone::orderBy('min_points', 'asc')->get();
        return view('admin.gamification.tiers-index', compact('tiers'));
    }

    public function createTier()
    {
        return view('admin.gamification.tiers-form', ['tier' => null]);
    }

    public function storeTier(TierMilestoneRequest $request)
    {
        TierMilestone::create($request->validated());

        return redirect()
            ->route('admin.gamification.tiers.index')
            ->with('success', 'Tier created successfully.');
    }

    public function editTier(TierMilestone $tier)
    {
        return view('admin.gamification.tiers-form', compact('tier'));
    }

    public function updateTier(TierMilestoneRequest $request, TierMilestone $tier)
    {
        $tier->fill($request->validated())->save();

        return redirect()
            ->route('admin.gamification.tiers.index')
            ->with('success', 'Tier updated successfully.');
    }

    public function destroyTier(TierMilestone $tier)
    {
        $tier->delete();

        return redirect()
            ->route('admin.gamification.tiers.index')
            ->with('success', 'Tier deleted successfully.');
    }
}
