<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gamification Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; font-size: 13px; }
        h1 { color: #1f2937; font-size: 22px; margin-bottom: 5px; }
        h2 { color: #374151; font-size: 16px; margin-top: 25px; border-bottom: 1px solid #d1d5db; padding-bottom: 5px; }
        .stats { display: flex; flex-wrap: wrap; gap: 10px; margin: 15px 0; }
        .stat-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 15px; border-radius: 6px; flex: 1; min-width: 150px; }
        .stat-label { font-size: 11px; color: #6b7280; text-transform: uppercase; }
        .stat-value { font-size: 18px; font-weight: bold; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
        th { background-color: #f59e0b; color: #fff; padding: 10px; text-align: left; }
        td { border: 1px solid #d1d5db; padding: 8px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .footer { margin-top: 30px; font-size: 11px; color: #9ca3af; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Gamification Report</h1>
    @if(!empty($period))
        <p>Report Period: {{ $period }}</p>
    @endif
    <p>Generated at: {{ $generatedAt }}</p>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total Members</div>
            <div class="stat-value">{{ number_format($totalMembers) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Points Earned</div>
            <div class="stat-value">{{ number_format($totalEarned) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Points Redeemed</div>
            <div class="stat-value">{{ number_format($totalRedeemed) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Points Adjusted</div>
            <div class="stat-value">{{ number_format($totalAdjusted) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Points Refunded</div>
            <div class="stat-value">{{ number_format($totalRefunded) }}</div>
        </div>
    </div>

    <h2>Member Points Summary</h2>
    @if($memberPoints->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Tier</th>
                <th>Total Points</th>
                <th>Available</th>
                <th>Redeemed</th>
                <th>Streak</th>
                <th>Last Activity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($memberPoints as $mp)
            <?php $mpTier = \App\Models\TierMilestone::where('min_points', '<=', $mp->total_points)->orderByDesc('min_points')->first(); ?>
            <tr>
                <td>{{ $mp->user_id }}</td>
                <td>{{ $mp->user ? $mp->user->name : '-' }}</td>
                <td>{{ $mp->user ? $mp->user->email : '-' }}</td>
                <td>{{ $mpTier ? ucfirst($mpTier->tier) : '-' }}</td>
                <td>{{ number_format($mp->total_points) }}</td>
                <td>{{ number_format($mp->available_points) }}</td>
                <td>{{ number_format($mp->redeemed_points) }}</td>
                <td>{{ $mp->current_streak }}</td>
                <td>{{ $mp->last_activity_date ? \Carbon\Carbon::parse($mp->last_activity_date)->format('Y-m-d') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No member points data available.</p>
    @endif

    <h2>Point Transactions</h2>
    @if($transactions->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date/Time</th>
                <th>Member</th>
                <th>Type</th>
                <th>Points</th>
                <th>Balance After</th>
                <th>Reason</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->id }}</td>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $tx->user ? $tx->user->name : '-' }}</td>
                <td>{{ ucfirst($tx->type) }}</td>
                <td>{{ $tx->points > 0 ? '+' . number_format($tx->points) : number_format($tx->points) }}</td>
                <td>{{ number_format($tx->balance_after) }}</td>
                <td>{{ $tx->reason ?? '-' }}</td>
                <td>{{ $tx->admin ? $tx->admin->name : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No transaction data available.</p>
    @endif

    <div class="page-break"></div>

    <h2>Badge Earnings</h2>
    @if($badgeEarnings->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date/Time</th>
                <th>Member</th>
                <th>Badge Code</th>
                <th>Badge Name</th>
                <th>Tier</th>
                <th>Points Awarded</th>
            </tr>
        </thead>
        <tbody>
            @foreach($badgeEarnings as $be)
            <tr>
                <td>{{ $be->id }}</td>
                <td>{{ $be->earned_at->format('Y-m-d H:i') }}</td>
                <td>{{ $be->user ? $be->user->name : '-' }}</td>
                <td>{{ $be->badge ? $be->badge->code : '-' }}</td>
                <td>{{ $be->badge ? $be->badge->name : '-' }}</td>
                <td>{{ $be->badge ? ucfirst($be->badge->tier) : '-' }}</td>
                <td>{{ $be->badge ? number_format($be->badge->points_awarded) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No badge earnings data available.</p>
    @endif

    <h2>Reward Redemptions</h2>
    @if($redemptions->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date/Time</th>
                <th>Member</th>
                <th>Reward Name</th>
                <th>Category</th>
                <th>Points Spent</th>
                <th>Status</th>
                <th>Claim Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($redemptions as $rd)
            <tr>
                <td>{{ $rd->id }}</td>
                <td>{{ $rd->redeemed_at->format('Y-m-d H:i') }}</td>
                <td>{{ $rd->user ? $rd->user->name : '-' }}</td>
                <td>{{ $rd->reward ? $rd->reward->name : '-' }}</td>
                <td>{{ $rd->reward ? $rd->reward->category : '-' }}</td>
                <td>{{ number_format($rd->points_spent) }}</td>
                <td>{{ ucfirst($rd->status) }}</td>
                <td>{{ $rd->claim_code ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No reward redemptions data available.</p>
    @endif

    <div class="footer">
        <p>Mosque Management System - Automatically generated gamification report</p>
    </div>
</body>
</html>