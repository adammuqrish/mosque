<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Donation;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Models\MemberPoints;
use App\Models\PointTransaction;
use App\Models\Badge;
use App\Models\BadgeEarning;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\TierMilestone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ExportService
{
    private const CHUNK_SIZE = 1000;

    public function generateDonationsReport(string $format = 'csv', ?int $month = null, ?int $year = null, string $reportType = 'monthly')
    {
        $query = Donation::orderBy('donation_date', 'desc');

        if ($reportType === 'monthly' && $month) {
            $query->whereMonth('donation_date', $month);
        }

        if ($year) {
            $query->whereYear('donation_date', $year);
        }

        $period = $this->formatPeriodLabel($month, $year, $reportType);

        if ($format === 'pdf') {
            $donations = $query->get();
            $data = $donations->map(function ($donation) {
                return [
                    'ID' => $donation->id,
                    'Donation Date' => $donation->donation_date ? $donation->donation_date->format('Y-m-d') : '-',
                    'Amount' => 'RM ' . number_format($donation->amount, 2),
                    'Category' => $donation->category ?? '-',
                    'Fund Purpose' => $donation->fund_purpose ?? '-',
                    'Donor Name' => $donation->donor_name ?? '-',
                    'Donor IC' => $donation->donor_ic ?? '-',
                    'Source' => ucfirst($donation->source ?? 'N/A'),
                    'Status' => ucfirst($donation->status ?? 'pending'),
                    'Receipt #' => $donation->receipt_number ?? '-',
                    'Description' => $donation->description ?? '-',
                    'Recorded At' => $donation->created_at ? $donation->created_at->format('Y-m-d H:i') : '-',
                ];
            });
            return $this->generatePDF($data, 'donations', 'Donations Report', $period);
        }

        $filename = $reportType === 'yearly' 
            ? 'donations_report_' . $year 
            : 'donations_report_' . sprintf('%02d-%s', $month, $year);
        
        return $this->generateCSVChunked($query, $filename, $period, function ($donation) {
            return [
                'ID' => $donation->id,
                'Donation Date' => $donation->donation_date ? $donation->donation_date->format('Y-m-d') : '-',
                'Amount' => 'RM ' . number_format($donation->amount, 2),
                'Category' => $donation->category ?? '-',
                'Fund Purpose' => $donation->fund_purpose ?? '-',
                'Donor Name' => $donation->donor_name ?? '-',
                'Donor IC' => $donation->donor_ic ?? '-',
                'Source' => ucfirst($donation->source ?? 'N/A'),
                'Status' => ucfirst($donation->status ?? 'pending'),
                'Receipt #' => $donation->receipt_number ?? '-',
                'Description' => $donation->description ?? '-',
                'Recorded At' => $donation->created_at ? $donation->created_at->format('Y-m-d H:i') : '-',
            ];
        });
    }

public function generateEventsReport(string $format = 'csv', ?int $month = null, ?int $year = null, string $reportType = 'monthly')
    {
        $query = Event::withCount('volunteers');

        if ($reportType === 'monthly' && $month) {
            $query->whereMonth('event_date', $month);
        }

        if ($year) {
            $query->whereYear('event_date', $year);
        }

        $period = $this->formatPeriodLabel($month, $year, $reportType);

        if ($format === 'pdf') {
            $events = $query->with('volunteers')->orderBy('event_date', 'desc')->get();
            $data = $events->map(function ($event) {
                return [
                    'ID' => $event->id,
                    'Title' => $event->title,
                    'Date' => $event->event_date ? $event->event_date->format('Y-m-d H:i') : '-',
                    'Location' => $event->event_location,
                    'Max Volunteers' => $event->max_volunteers,
                    'Current Volunteers' => $event->volunteers->count(),
                    'Status' => ucfirst($event->status),
                    'Created At' => $event->created_at->format('Y-m-d H:i'),
                ];
            });
            return $this->generatePDF($data, 'events', 'Events Report', $period);
        }

        $filename = $reportType === 'yearly' 
            ? 'events_report_' . $year 
            : 'events_report_' . sprintf('%02d-%s', $month, $year);
        
        return $this->generateCSVChunked($query, $filename, $period, function ($event) {
            return [
                'ID' => $event->id,
                'Title' => $event->title,
                'Date' => $event->event_date ? $event->event_date->format('Y-m-d H:i') : '-',
                'Location' => $event->event_location ?? '-',
                'Max Volunteers' => $event->max_volunteers,
                'Current Volunteers' => $event->volunteers_count ?? 0,
                'Status' => ucfirst($event->status),
                'Created At' => $event->created_at ? $event->created_at->format('Y-m-d H:i') : '-',
            ];
        });
    }

    public function generateAttendanceReport(string $format = 'csv', ?int $month = null, ?int $year = null, string $reportType = 'monthly')
    {
        $query = Event::with(['volunteers' => function ($query) {
            $query->withPivot('attendance_status', 'joined_at');
        }])
        ->where('status', '!=', 'cancelled');

        if ($reportType === 'monthly' && $month) {
            $query->whereMonth('event_date', $month);
        }

        if ($year) {
            $query->whereYear('event_date', $year);
        }

        $period = $this->formatPeriodLabel($month, $year, $reportType);

        if ($format === 'pdf') {
            $events = $query->orderBy('event_date', 'desc')->get();
            $data = [];
            foreach ($events as $event) {
                foreach ($event->volunteers as $volunteer) {
                    $data[] = [
                        'Event ID' => $event->id,
                        'Event Title' => $event->title,
                        'Event Date' => $event->event_date ? $event->event_date->format('Y-m-d') : '-',
                        'Volunteer Name' => $volunteer->name,
                        'Volunteer Email' => $volunteer->email,
                        'Attendance Status' => ucfirst(str_replace('_', ' ', $volunteer->pivot->attendance_status)),
                        'Joined At' => Carbon::parse($volunteer->pivot->joined_at)->format('Y-m-d H:i'),
                    ];
                }
            }
            $data = collect($data);
            return $this->generatePDF($data, 'attendance', 'Volunteer Attendance Report', $period);
        }

        $filename = $reportType === 'yearly' 
            ? 'attendance_report_' . $year 
            : 'attendance_report_' . sprintf('%02d-%s', $month, $year);
        
        return $this->generateAttendanceCSVChunked($query, $filename, $period);
    }

    public function generateFinancialSummary(string $format = 'csv', ?int $month = null, ?int $year = null, string $reportType = 'monthly')
    {
        $period = $this->formatPeriodLabel($month, $year, $reportType);

        $totalDonationsQuery = Donation::query();
        $totalWithdrawalsQuery = WithdrawalRequest::where('status', 'approved');

        if ($reportType === 'monthly' && $month) {
            $totalDonationsQuery->whereMonth('donation_date', $month);
            $totalWithdrawalsQuery->whereMonth('created_at', $month);
        }

        if ($year) {
            $totalDonationsQuery->whereYear('donation_date', $year);
            $totalWithdrawalsQuery->whereYear('created_at', $year);
        }

        $totalDonations = $totalDonationsQuery->sum('amount');
        $zakatDonations = (clone $totalDonationsQuery)->where('category', 'zakat')->sum('amount');
        $zakatFitrDonations = (clone $totalDonationsQuery)->where('category', 'zakat_fitr')->sum('amount');
        $sadaqahDonations = (clone $totalDonationsQuery)->voluntary()->sum('amount');
        $waqfDonations = (clone $totalDonationsQuery)->endowment()->sum('amount');
        $totalWithdrawals = $totalWithdrawalsQuery->sum('amount');
        $zakatWithdrawals = (clone $totalWithdrawalsQuery)->where('type', 'zakat')->sum('amount');
        $zakatFitrWithdrawals = (clone $totalWithdrawalsQuery)->where('type', 'zakat_fitr')->sum('amount');
        $sadaqahWithdrawals = (clone $totalWithdrawalsQuery)->where('type', 'sadaqah')->sum('amount');
        $waqfWithdrawals = (clone $totalWithdrawalsQuery)->where('type', 'waqf')->sum('amount');
        $balance = $totalDonations - $totalWithdrawals;

        $summaryData = [
            ['Label' => 'Zakat In', 'Value' => 'RM ' . number_format($zakatDonations, 2)],
            ['Label' => 'Zakat Fitr In', 'Value' => 'RM ' . number_format($zakatFitrDonations, 2)],
            ['Label' => 'Sadaqah In', 'Value' => 'RM ' . number_format($sadaqahDonations, 2)],
            ['Label' => 'Waqf In', 'Value' => 'RM ' . number_format($waqfDonations, 2)],
            ['Label' => 'Zakat Out', 'Value' => '- RM ' . number_format($zakatWithdrawals, 2)],
            ['Label' => 'Zakat Fitr Out', 'Value' => '- RM ' . number_format($zakatFitrWithdrawals, 2)],
            ['Label' => 'Sadaqah Out', 'Value' => '- RM ' . number_format($sadaqahWithdrawals, 2)],
            ['Label' => 'Waqf Out', 'Value' => '- RM ' . number_format($waqfWithdrawals, 2)],
            ['Label' => 'Net Balance', 'Value' => 'RM ' . number_format($balance, 2)],
            ['Label' => 'Report Period', 'Value' => $period],
            ['Label' => 'Generated At', 'Value' => now()->format('Y-m-d H:i:s')],
        ];

        $monthlyData = [];
        if ($month && $year) {
            $monthName = Carbon::create($year, $month, 1)->format('F Y');
            $monthlyData[] = [
                'Month' => $monthName,
                'Donations' => 'RM ' . number_format($totalDonations, 2),
                'Withdrawals' => 'RM ' . number_format($totalWithdrawals, 2),
            ];
        } else {
            $donationsByMonth = Donation::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

            $withdrawalsByMonth = WithdrawalRequest::where('status', 'approved')
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            foreach ($donationsByMonth as $donation) {
                $monthName = Carbon::create($donation->year, $donation->month, 1)->format('F Y');
                $withdrawal = $withdrawalsByMonth->first(function ($w) use ($donation) {
                    return $w->month == $donation->month && $w->year == $donation->year;
                });

                $monthlyData[] = [
                    'Month' => $monthName,
                    'Donations' => 'RM ' . number_format($donation->total, 2),
                    'Withdrawals' => 'RM ' . number_format($withdrawal->total ?? 0, 2),
                ];
            }
        }

        $data = collect($summaryData);

        if ($format === 'pdf') {
            return $this->generateFinancialPDF($summaryData, $monthlyData, $period);
        }

        $filename = 'financial_summary' . ($month && $year ? '_' . sprintf('%02d-%s', $month, $year) : '') . '_' . date('Y-m-d');
        return $this->generateCSV($data, $filename, $period);
    }

    public function generateGamificationReport(string $format = 'csv', ?int $month = null, ?int $year = null, string $reportType = 'monthly')
    {
        $period = $this->formatPeriodLabel($month, $year, $reportType);

        $memberPointsQuery = MemberPoints::with('user');
        $transactionQuery = PointTransaction::with('user', 'admin');
        $badgeEarningQuery = BadgeEarning::with('user', 'badge');
        $redemptionQuery = RewardRedemption::with('user', 'reward', 'fulfilledBy');

        if ($reportType === 'monthly' && $month && $year) {
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth()->toDateTimeString();
            $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->toDateTimeString();
            $transactionQuery->whereBetween('created_at', [$monthStart, $monthEnd]);
            $badgeEarningQuery->whereBetween('earned_at', [$monthStart, $monthEnd]);
            $redemptionQuery->whereBetween('redeemed_at', [$monthStart, $monthEnd]);
        } elseif ($year) {
            $yearStart = Carbon::create($year, 1, 1)->startOfYear()->toDateTimeString();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear()->toDateTimeString();
            $transactionQuery->whereBetween('created_at', [$yearStart, $yearEnd]);
            $badgeEarningQuery->whereBetween('earned_at', [$yearStart, $yearEnd]);
            $redemptionQuery->whereBetween('redeemed_at', [$yearStart, $yearEnd]);
        }

        $memberPoints = $memberPointsQuery->orderBy('total_points', 'desc')->get();
        $transactions = $transactionQuery->orderBy('created_at', 'desc')->get();
        $badgeEarnings = $badgeEarningQuery->orderBy('earned_at', 'desc')->get();
        $redemptions = $redemptionQuery->orderBy('redeemed_at', 'desc')->get();

        $totalEarned = $transactions->where('type', 'earned')->sum('points');
        $totalRedeemed = abs($transactions->where('type', 'redeemed')->sum('points'));
        $totalAdjusted = $transactions->whereIn('type', ['adjusted', 'revoked'])->sum('points');
        $totalRefunded = abs($transactions->where('type', 'refunded')->sum('points'));
        $totalMembers = MemberPoints::count();

        if ($format === 'pdf') {
            $pdfMemberPoints = $memberPoints->take(100);
            $pdfTransactions = $transactions->take(500);
            $pdfBadgeEarnings = $badgeEarnings->take(500);
            $dataLimited = $memberPoints->count() > 100 || $transactions->count() > 500 || $badgeEarnings->count() > 500;

            return $this->generateGamificationPDF($pdfMemberPoints, $pdfTransactions, $pdfBadgeEarnings, $redemptions, $totalEarned, $totalRedeemed, $totalAdjusted, $totalRefunded, $totalMembers, $period, $dataLimited);
        }

        $filename = 'gamification_report' . ($month && $year ? '_' . sprintf('%02d-%s', $month, $year) : '') . '_' . date('Y-m-d');

        $callback = function () use ($memberPoints, $transactions, $badgeEarnings, $redemptions, $totalEarned, $totalRedeemed, $totalAdjusted, $totalRefunded, $totalMembers, $period) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['GAMIFICATION REPORT']);
            fputcsv($handle, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Period', $period]);
            fputcsv($handle, ['Total Members', $totalMembers]);
            fputcsv($handle, ['Total Points Earned', number_format($totalEarned)]);
            fputcsv($handle, ['Total Points Redeemed', number_format($totalRedeemed)]);
            fputcsv($handle, ['Total Points Adjusted', number_format($totalAdjusted)]);
            fputcsv($handle, ['Total Points Refunded', number_format($totalRefunded)]);
            fputcsv($handle, []);
            fputcsv($handle, []);

            fputcsv($handle, ['--- MEMBER POINTS SUMMARY ---']);
            fputcsv($handle, ['Member ID', 'Name', 'Email', 'Tier', 'Total Points', 'Available Points', 'Redeemed Points', 'Current Streak', 'Longest Streak', 'Last Activity']);

            foreach ($memberPoints as $mp) {
                $mpTier = TierMilestone::where('min_points', '<=', $mp->total_points)->orderByDesc('min_points')->first();
                fputcsv($handle, [
                    $mp->user_id,
                    $mp->user ? $mp->user->name : '-',
                    $mp->user ? $mp->user->email : '-',
                    $mpTier ? ucfirst($mpTier->tier) : '-',
                    number_format($mp->total_points),
                    number_format($mp->available_points),
                    number_format($mp->redeemed_points),
                    $mp->current_streak,
                    $mp->longest_streak,
                    $mp->last_activity_date ? Carbon::parse($mp->last_activity_date)->format('Y-m-d') : '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- POINT TRANSACTIONS ---']);
            fputcsv($handle, ['ID', 'Date/Time', 'Member', 'Email', 'Type', 'Points', 'Balance After', 'Reason', 'Admin']);

            foreach ($transactions as $tx) {
                $pointsDisplay = $tx->points > 0 ? '+' . number_format($tx->points) : number_format($tx->points);
                fputcsv($handle, [
                    $tx->id,
                    $tx->created_at->format('Y-m-d H:i'),
                    $tx->user ? $tx->user->name : '-',
                    $tx->user ? $tx->user->email : '-',
                    ucfirst($tx->type),
                    $pointsDisplay,
                    number_format($tx->balance_after),
                    $tx->reason ?? '-',
                    $tx->admin ? $tx->admin->name : '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- BADGE EARNINGS ---']);
            fputcsv($handle, ['ID', 'Date/Time', 'Member', 'Email', 'Badge Code', 'Badge Name', 'Tier', 'Points Awarded']);

            foreach ($badgeEarnings as $be) {
                fputcsv($handle, [
                    $be->id,
                    $be->earned_at->format('Y-m-d H:i'),
                    $be->user ? $be->user->name : '-',
                    $be->user ? $be->user->email : '-',
                    $be->badge ? $be->badge->code : '-',
                    $be->badge ? $be->badge->name : '-',
                    $be->badge ? ucfirst($be->badge->tier) : '-',
                    $be->badge ? number_format($be->badge->points_awarded) : '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- REWARD REDEMPTIONS ---']);
            fputcsv($handle, ['ID', 'Date/Time', 'Member', 'Email', 'Reward Name', 'Category', 'Points Spent', 'Status', 'Claim Code', 'Fulfilled By', 'Fulfilled At']);

            foreach ($redemptions as $rd) {
                fputcsv($handle, [
                    $rd->id,
                    $rd->redeemed_at->format('Y-m-d H:i'),
                    $rd->user ? $rd->user->name : '-',
                    $rd->user ? $rd->user->email : '-',
                    $rd->reward ? $rd->reward->name : '-',
                    $rd->reward ? $rd->reward->category : '-',
                    number_format($rd->points_spent),
                    ucfirst($rd->status),
                    $rd->claim_code ?? '-',
                    $rd->fulfilledBy ? $rd->fulfilledBy->name : '-',
                    $rd->fulfilled_at ? Carbon::parse($rd->fulfilled_at)->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }

    private function generateGamificationPDF($memberPoints, $transactions, $badgeEarnings, $redemptions, $totalEarned, $totalRedeemed, $totalAdjusted, $totalRefunded, $totalMembers, $period, $dataLimited = false)
    {
        $filename = 'gamification_report_' . date('Y-m-d_His') . '.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = view('reports.gamification_pdf', [
            'memberPoints' => $memberPoints,
            'transactions' => $transactions,
            'badgeEarnings' => $badgeEarnings,
            'redemptions' => $redemptions,
            'totalEarned' => $totalEarned,
            'totalRedeemed' => $totalRedeemed,
            'totalAdjusted' => $totalAdjusted,
            'totalRefunded' => $totalRefunded,
            'totalMembers' => $totalMembers,
            'period' => $period,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'dataLimited' => $dataLimited,
        ])->render();

        $pdf = \PDF::loadHTML($html);

        return response($pdf->download(), 200, $headers);
    }

    private function generateCSV($data, string $filename, ?string $period = null)
    {
        $chunkSize = self::CHUNK_SIZE;
        
        if ($data->count() > $chunkSize) {
            return $this->generateCSVChunkedFromCollection($data, $filename, $period);
        }

        if ($data->isEmpty()) {
            $data = collect([['Message' => 'No data available']]);
        }

        $filename .= '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data, $period) {
            $handle = fopen('php://output', 'w');

            if ($period) {
                fputcsv($handle, ['Report Period', $period]);
                fputcsv($handle, []);
            }

            fputcsv($handle, array_keys($data->first()));

            foreach ($data as $row) {
                fputcsv($handle, array_values($row));
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateCSVChunked($query, string $filename, ?string $period, callable $transformCallback)
    {
        $filename .= '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($query, $period, $transformCallback) {
            $handle = fopen('php://output', 'w');
            $firstRow = true;
            $hasData = false;
            $chunkSize = self::CHUNK_SIZE;

            $query->chunkById($chunkSize, function ($records) use ($handle, &$firstRow, &$hasData, $period, $transformCallback) {
                if ($firstRow) {
                    $hasData = true;
                    if ($period) {
                        fputcsv($handle, ['Report Period', $period]);
                        fputcsv($handle, []);
                    }
                    
                    $firstRecord = $transformCallback($records->first());
                    fputcsv($handle, array_keys($firstRecord));
                    $firstRow = false;
                }

                foreach ($records as $record) {
                    $row = $transformCallback($record);
                    fputcsv($handle, array_values($row));
                }
            });

            if (!$hasData) {
                if ($period) {
                    fputcsv($handle, ['Report Period', $period]);
                    fputcsv($handle, []);
                }
                fputcsv($handle, ['No data available']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateCSVChunkedFromCollection($data, string $filename, ?string $period)
    {
        $filename .= '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data, $period) {
            $handle = fopen('php://output', 'w');
            $firstRow = true;

            $data->chunk(self::CHUNK_SIZE, function ($chunk) use ($handle, &$firstRow, $period) {
                if ($firstRow) {
                    if ($period) {
                        fputcsv($handle, ['Report Period', $period]);
                        fputcsv($handle, []);
                    }
                    
                    fputcsv($handle, array_keys($chunk->first()));
                    $firstRow = false;
                }

                foreach ($chunk as $row) {
                    fputcsv($handle, array_values($row));
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateAttendanceCSVChunked($query, string $filename, ?string $period)
    {
        $filename .= '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($query, $period) {
            $handle = fopen('php://output', 'w');
            $firstRow = true;
            $hasData = false;

            $query->chunk(self::CHUNK_SIZE, function ($events) use ($handle, &$firstRow, &$hasData, $period) {
                foreach ($events as $event) {
                    foreach ($event->volunteers as $volunteer) {
                        $hasData = true;
                        if ($firstRow) {
                            if ($period) {
                                fputcsv($handle, ['Report Period', $period]);
                                fputcsv($handle, []);
                            }
                            fputcsv($handle, ['Event ID', 'Event Title', 'Event Date', 'Volunteer Name', 'Volunteer Email', 'Attendance Status', 'Joined At']);
                            $firstRow = false;
                        }

                        fputcsv($handle, [
                            $event->id,
                            $event->title,
                            $event->event_date ? $event->event_date->format('Y-m-d') : '-',
                            $volunteer->name,
                            $volunteer->email,
                            ucfirst(str_replace('_', ' ', $volunteer->pivot->attendance_status)),
                            Carbon::parse($volunteer->pivot->joined_at)->format('Y-m-d H:i'),
                        ]);
                    }
                }
            });

            if (!$hasData) {
                if ($period) {
                    fputcsv($handle, ['Report Period', $period]);
                    fputcsv($handle, []);
                }
                fputcsv($handle, ['No data available']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generatePDF($data, string $type, string $title, ?string $period = null)
    {
        $filename = $type . '_report_' . date('Y-m-d_His') . '.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = $this->generateHTMLForPDF($data, $title, $period);

        $pdf = \PDF::loadHTML($html);

        return response($pdf->download(), 200, $headers);
    }

    private function formatPeriodLabel(?int $month, ?int $year, string $reportType = 'monthly'): string
    {
        if ($reportType === 'yearly' && $year) {
            return 'Year ' . $year;
        }

        if ($month && $year) {
            return Carbon::create($year, $month, 1)->format('F Y');
        }

        if ($year) {
            return (string) $year;
        }

        return 'All time';
    }

    private function generateFinancialPDF($summaryData, $monthlyData, ?string $period = null)
    {
        $filename = 'financial_summary_' . date('Y-m-d_His') . '.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = view('reports.financial_pdf', [
            'summary' => $summaryData,
            'monthly' => collect($monthlyData),
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'period' => $period,
        ])->render();

        $pdf = \PDF::loadHTML($html);

        return response($pdf->download(), 200, $headers);
    }

    private function generateHTMLForPDF($data, string $title, ?string $period = null)
    {
        $colCount = $data->isNotEmpty() ? count($data->first()) : 0;
        $isWide = $colCount > 8;

        $rows = '';
        foreach ($data as $row) {
            $rows .= '<tr>';
            foreach ($row as $cell) {
                $rows .= '<td style="border:1px solid #d1d5db;padding:2px 4px;font-size:7px;word-break:break-word;overflow-wrap:break-word;">' . $cell . '</td>';
            }
            $rows .= '</tr>';
        }

        $headers = '';
        if ($data->isNotEmpty()) {
            $headers = collect($data->first())->keys()->map(function($key) {
                return '<th style="background-color:#065f46;color:#fff;padding:3px 4px;text-align:left;font-size:6px;text-transform:uppercase;letter-spacing:0.2px;word-break:break-word;overflow-wrap:break-word;">' . $key . '</th>';
            })->implode('');
        }

        $pageSize = $isWide ? 'A4 landscape' : 'A4 portrait';
        $margin = $isWide ? '15mm 10mm 20mm 10mm' : '30mm 20mm 25mm 20mm';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                @page { margin: ' . $margin . '; size: ' . $pageSize . '; }
                body { font-family: "DejaVu Sans", "Amiri", Arial, sans-serif; color: #374151; font-size: 10px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 6mm; padding-bottom: 3mm; border-bottom: 2px solid #065f46; }
                .header .bismillah { font-family: "Amiri", "DejaVu Sans", serif; font-size: 18px; color: #065f46; direction: rtl; margin-bottom: 2px; }
                .header .mosque-name { font-size: 16px; font-weight: bold; color: #065f46; }
                .report-title { text-align: center; margin: 4mm 0 3mm; }
                .report-title h1 { color: #065f46; font-size: 14px; font-weight: bold; margin: 0 0 3px; }
                .report-title .meta { font-size: 9px; color: #6b7280; }
                table { width: 100%; max-width: 100%; border-collapse: collapse; margin-top: 3mm; table-layout: fixed; }
                th, td { border: 1px solid #d1d5db; word-break: break-word; overflow-wrap: break-word; }
                tr:nth-child(even) { background-color: #f9fafb; }
                .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 7px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 3mm; }
                .footer .brand { color: #065f46; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="bismillah">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                <div class="mosque-name">Smart Mosque System</div>
            </div>
            <div class="report-title">
                <h1>' . $title . '</h1>
                <div class="meta">' .
                ($period ? 'Period: ' . $period . ' &bull; ' : '') .
                'Generated: ' . date('Y-m-d H:i:s') . '</div>
            </div>
            <table>
                <thead><tr>' . $headers . '</tr></thead>
                <tbody>' . $rows . '</tbody>
            </table>
            <div class="footer">
                <span class="brand">Smart Mosque System</span>
                &mdash; Laporan ini dijana secara automatik &bull; Automatically generated report
            </div>
        </body>
        </html>';
    }
}
