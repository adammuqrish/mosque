<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input Month & Year. Kalau tak ada, default bulan ni.
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // 2. Query Donations (Filter ikut bulan/tahun)
        $donations = Donation::whereMonth('donation_date', $month)
            ->whereYear('donation_date', $year)
            ->with('user') // Nak tahu siapa rekod
            ->get();

        // 3. Query Withdrawals (Hanya yang APPROVE sahaja & Filter ikut tarikh request/create)
        // Kita guna created_at untuk filtering report, atau approved_at?
        // Lebih selamat guna created_at atau approved_at ikut keperluan.
        // Kita guna 'created_at' request untuk senang.
        $withdrawals = WithdrawalRequest::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'approved') // Hanya yang lulus
            ->with('requester', 'approver')
            ->get();

        // 4. Kira Total
        $totalDonations = $donations->sum('amount');
        $totalWithdrawals = $withdrawals->sum('amount');
        $balance = $totalDonations - $totalWithdrawals;

        // 5. Sediakan nama bulan untuk display (Contoh: "March")
        $monthName = date('F', mktime(0, 0, 0, $month, 10));

        return view('reports.index', compact(
            'donations',
            'withdrawals',
            'totalDonations',
            'totalWithdrawals',
            'balance',
            'month',
            'year',
            'monthName'
        ));
    }
}