<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gamification Report — Smart Mosque System</title>
    <style>
        @page {
            margin: 30mm 20mm 25mm 20mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', 'Amiri', 'Arial', sans-serif;
            color: #374151;
            font-size: 10px;
            line-height: 1.6;
        }

        /* ── Letterhead Header ── */
        .header {
            text-align: center;
            margin-bottom: 6mm;
            padding-bottom: 4mm;
            border-bottom: 2px solid #065f46;
        }
        .header .bismillah {
            font-family: 'Amiri', 'DejaVu Sans', serif;
            font-size: 18px;
            color: #065f46;
            direction: rtl;
            margin-bottom: 2px;
        }
        .header .mosque-name {
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            letter-spacing: 0.5px;
        }
        .header .tagline {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        /* ── Report Title ── */
        .report-title {
            text-align: center;
            margin: 5mm 0 3mm;
        }
        .report-title h1 {
            color: #d97706;
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 3px;
        }
        .report-title .meta {
            font-size: 9px;
            color: #6b7280;
        }

        /* ── Stats Cards ── */
        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 4mm 0;
        }
        .stat-box {
            flex: 1;
            min-width: 100px;
            border: 1px solid #e5e7eb;
            border-left: 3px solid #d97706;
            padding: 6px 10px;
            border-radius: 3px;
            background: #fffbeb;
        }
        .stat-label {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-top: 1px;
        }

        /* ── Section Titles ── */
        h2 {
            color: #374151;
            font-size: 12px;
            margin-top: 5mm;
            margin-bottom: 2mm;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
        }

        /* ── Data Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            font-size: 8px;
        }
        th {
            background-color: #d97706;
            color: #ffffff;
            padding: 4px 6px;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 3px 6px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* ── Page Break ── */
        .page-break {
            page-break-after: always;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 3mm;
        }
        .footer .page-number:before {
            content: "Page ";
        }
        .footer .page-number:after {
            content: " of " counter(totalPages);
        }
        .footer .brand {
            color: #d97706;
            font-weight: 600;
        }
    </style>
</head>
<body>

    {{-- Letterhead Header --}}
    <div class="header">
        <div class="bismillah">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
        <div class="mosque-name">Smart Mosque System</div>
        <div class="tagline">Laporan Gamifikasi &bull; Gamification Report</div>
    </div>

    {{-- Report Title --}}
    <div class="report-title">
        <h1>Gamification Report</h1>
        <div class="meta">
            @if(!empty($period))
                Period: {{ $period }} &bull;
            @endif
            Generated: {{ $generatedAt }}
        </div>
    </div>

    {{-- Stats Overview --}}
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

    {{-- Member Points Summary --}}
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
    <p style="color:#9ca3af;text-align:center;">No member points data available.</p>
    @endif

    {{-- Point Transactions --}}
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
    <p style="color:#9ca3af;text-align:center;">No transaction data available.</p>
    @endif

    <div class="page-break"></div>

    {{-- Badge Earnings --}}
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
    <p style="color:#9ca3af;text-align:center;">No badge earnings data available.</p>
    @endif

    {{-- Reward Redemptions --}}
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
    <p style="color:#9ca3af;text-align:center;">No reward redemptions data available.</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span class="brand">Smart Mosque System</span>
        &mdash; Laporan ini dijana secara automatik &bull; Automatically generated report
        &mdash; <span class="page-number"></span>
    </div>

</body>
</html>
