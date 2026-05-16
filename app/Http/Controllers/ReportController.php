<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Models\WithdrawalRequest;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'monthly');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'donations');
        
        $sortDonation = $request->get('sort', 'donation_date');
        $directionDonation = $request->get('direction', 'desc');
        $allowedSortsDonation = ['donation_date', 'amount', 'category', 'source', 'created_at'];
        if (!in_array($sortDonation, $allowedSortsDonation)) {
            $sortDonation = 'donation_date';
        }
        if (!in_array($directionDonation, ['asc', 'desc'])) {
            $directionDonation = 'desc';
        }

        $sortEvent = $request->get('sort_event', 'event_date');
        $directionEvent = $request->get('direction_event', 'desc');
        $allowedSortsEvent = ['event_date', 'title', 'status', 'event_location', 'created_at'];
        if (!in_array($sortEvent, $allowedSortsEvent)) {
            $sortEvent = 'event_date';
        }
        if (!in_array($directionEvent, ['asc', 'desc'])) {
            $directionEvent = 'desc';
        }

        $sortAttendance = $request->get('sort_attendance', 'event_date');
        $directionAttendance = $request->get('direction_attendance', 'desc');
        $allowedSortsAttendance = ['event_date', 'event_title', 'volunteer_name', 'email', 'attendance_status', 'joined_at'];
        if (!in_array($sortAttendance, $allowedSortsAttendance)) {
            $sortAttendance = 'event_date';
        }
        if (!in_array($directionAttendance, ['asc', 'desc'])) {
            $directionAttendance = 'desc';
        }

        $sortWithdrawal = $request->get('sort_withdrawal', 'created_at');
        $directionWithdrawal = $request->get('direction_withdrawal', 'desc');
        $allowedSortsWithdrawal = ['created_at', 'purpose', 'amount', 'requested_by', 'approved_by'];
        if (!in_array($sortWithdrawal, $allowedSortsWithdrawal)) {
            $sortWithdrawal = 'created_at';
        }
        if (!in_array($directionWithdrawal, ['asc', 'desc'])) {
            $directionWithdrawal = 'desc';
        }

        $donationQuery = $reportType === 'yearly' 
            ? Donation::whereYear('donation_date', $year)
            : Donation::whereMonth('donation_date', $month)->whereYear('donation_date', $year);
        
        $withdrawalQuery = $reportType === 'yearly'
            ? WithdrawalRequest::whereYear('created_at', $year)->where('status', 'approved')
            : WithdrawalRequest::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'approved');
        
        $eventQuery = $reportType === 'yearly'
            ? Event::whereYear('event_date', $year)
            : Event::whereMonth('event_date', $month)->whereYear('event_date', $year);
        
        $attendanceQuery = $reportType === 'yearly'
            ? \DB::table('event_volunteer')->join('events', 'event_volunteer.event_id', '=', 'events.id')->join('users', 'event_volunteer.user_id', '=', 'users.id')->whereYear('events.event_date', $year)
            : \DB::table('event_volunteer')->join('events', 'event_volunteer.event_id', '=', 'events.id')->join('users', 'event_volunteer.user_id', '=', 'users.id')->whereMonth('events.event_date', $month)->whereYear('events.event_date', $year);

        $donations = $donationQuery->with('user')->orderBy($sortDonation, $directionDonation)->paginate(20);

        $withdrawals = $withdrawalQuery->with('requester', 'approver')->orderBy($sortWithdrawal, $directionWithdrawal)->paginate(20);

        $events = $eventQuery->withCount('volunteers')->orderBy($sortEvent, $directionEvent)->paginate(20);

        $attendance = $attendanceQuery->select(
                'events.id as event_id',
                'events.title as event_title',
                'events.event_date',
                'users.id as user_id',
                'users.name as volunteer_name',
                'users.email',
                'event_volunteer.attendance_status',
                'event_volunteer.joined_at'
            )
            ->orderBy($sortAttendance, $directionAttendance)
            ->paginate(20);

        $donationBase = $reportType === 'yearly'
            ? Donation::whereYear('donation_date', $year)
            : Donation::whereMonth('donation_date', $month)->whereYear('donation_date', $year);
        $totalDonations = (clone $donationBase)->sum('amount');
        $zakatDonations = (clone $donationBase)->where('category', 'zakat')->sum('amount');
        $zakatFitrDonations = (clone $donationBase)->where('category', 'zakat_fitr')->sum('amount');
        $sadaqahDonations = (clone $donationBase)->voluntary()->sum('amount');
        $waqfDonations = (clone $donationBase)->endowment()->sum('amount');
        
        $donationStats = (clone $donationBase)->selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount, SUM(CASE WHEN source = "cash" THEN 1 ELSE 0 END) as cash_count, SUM(CASE WHEN source = "online" THEN 1 ELSE 0 END) as online_count')->first();
        
        $cashCount = $donationStats->cash_count ?? 0;
        $onlineCount = $donationStats->online_count ?? 0;

        $withdrawalBase = $reportType === 'yearly'
            ? WithdrawalRequest::whereYear('created_at', $year)->where('status', 'approved')
            : WithdrawalRequest::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'approved');
        $totalWithdrawals = (clone $withdrawalBase)->sum('amount');
        $zakatWithdrawals = (clone $withdrawalBase)->where('type', 'zakat')->sum('amount');
        $zakatFitrWithdrawals = (clone $withdrawalBase)->where('type', 'zakat_fitr')->sum('amount');
        $sadaqahWithdrawals = (clone $withdrawalBase)->where('type', 'sadaqah')->sum('amount');
        $waqfWithdrawals = (clone $withdrawalBase)->where('type', 'waqf')->sum('amount');
        $balance = $totalDonations - $totalWithdrawals;

        $totalEvents = $reportType === 'yearly'
            ? Event::whereYear('event_date', $year)->count()
            : Event::whereMonth('event_date', $month)->whereYear('event_date', $year)->count();

        $monthName = $reportType === 'yearly' ? 'All Months' : date('F', mktime(0, 0, 0, $month, 10));

        // Category breakdown
        $catLabels = ['zakat' => 'Zakat', 'zakat_fitr' => 'Zakat Fitr', 'sadaqah' => 'Sadaqah', 'waqf' => 'Waqf'];
        $catTotals = (clone $donationBase)->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->pluck('total', 'category')->toArray();
        $categoryBreakdown = [];
        foreach ($catLabels as $key => $label) {
            $categoryBreakdown[$key] = $catTotals[$key] ?? 0;
        }

        // Chart data (last 6 months)
        $chartLabels = [];
        $chartDonations = [];
        $chartExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $chartLabels[] = $m->format('M');
            $chartDonations[] = Donation::whereMonth('donation_date', $m->month)
                ->whereYear('donation_date', $m->year)->sum('amount');
            $chartExpenses[] = WithdrawalRequest::where('status', 'approved')
                ->whereMonth('approved_at', $m->month)
                ->whereYear('approved_at', $m->year)->sum('amount');
        }

return view('reports.index', compact(
            'donations',
            'withdrawals',
            'events',
            'attendance',
            'zakatDonations', 'zakatFitrDonations', 'sadaqahDonations', 'waqfDonations',
            'zakatWithdrawals', 'zakatFitrWithdrawals', 'sadaqahWithdrawals', 'waqfWithdrawals',
            'totalDonations',
            'totalWithdrawals',
            'totalEvents',
            'balance',
            'cashCount',
            'onlineCount',
            'reportType',
            'month',
            'year',
            'monthName',
            'tab',
            'sortDonation',
            'directionDonation',
            'sortEvent',
            'directionEvent',
            'sortAttendance',
            'directionAttendance',
            'sortWithdrawal',
            'directionWithdrawal',
            'categoryBreakdown',
            'catLabels',
            'chartLabels',
            'chartDonations',
            'chartExpenses'
        ));
    }

    // STEP 1: Export Donations Report
    public function exportDonationsCSV(Request $request)
    {
        return $this->exportService->generateDonationsReport('csv', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    public function exportDonationsPDF(Request $request)
    {
        return $this->exportService->generateDonationsReport('pdf', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    // STEP 2: Export Events Report
    public function exportEventsCSV(Request $request)
    {
        return $this->exportService->generateEventsReport('csv', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    public function exportEventsPDF(Request $request)
    {
        return $this->exportService->generateEventsReport('pdf', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    // STEP 3: Export Attendance Report
    public function exportAttendanceCSV(Request $request)
    {
        return $this->exportService->generateAttendanceReport('csv', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    public function exportAttendancePDF(Request $request)
    {
        return $this->exportService->generateAttendanceReport('pdf', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    // STEP 4: Financial Summary
    public function exportFinancialCSV(Request $request)
    {
        return $this->exportService->generateFinancialSummary('csv', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    public function exportFinancialPDF(Request $request)
    {
        return $this->exportService->generateFinancialSummary('pdf', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    // STEP 5: Gamification Report
    public function exportGamificationCSV(Request $request)
    {
        return $this->exportService->generateGamificationReport('csv', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }

    public function exportGamificationPDF(Request $request)
    {
        return $this->exportService->generateGamificationReport('pdf', $request->input('month'), $request->input('year'), $request->input('report_type', 'monthly'));
    }
}