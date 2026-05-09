<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    // 1. Paparkan Senarai Request + Form untuk Admin
    public function index()
    {
        // Ambil semua request, dengan info requester & approver
        $requests = WithdrawalRequest::with('requester', 'approver')->latest()->get();

        return view('withdrawals.index', compact('requests'));
    }

    // 2. Admin buat Request Baru
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'purpose' => 'required|string|max:255',
        ]);

        WithdrawalRequest::create([
            'requested_by' => auth()->id(), // Admin yang request
            'amount' => $request->amount,
            'purpose' => $request->purpose,
            'status' => 'pending', // Default status
        ]);

        return redirect()->back()->with('success', 'Request submitted successfully!');
    }

    // 3. Bendahari Approve Request
    public function approve($id)
    {
        $request = WithdrawalRequest::find($id);

        if ($request) {
            $request->update([
                'status' => 'approved',
                'approved_by' => auth()->id(), // Bendahari yang approve
                'approved_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Request Approved!');
    }

    // 4. Bendahari Reject Request
    public function reject($id)
    {
        $request = WithdrawalRequest::find($id);

        if ($request) {
            // Update status DAN simpan siapa yang reject
            $request->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(), // Simpan ID Bendahari yang reject
                'approved_at' => now(),
            ]);
        }

        return redirect()->back()->with('error', 'Request Rejected.');
    }
}