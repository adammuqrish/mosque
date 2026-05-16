<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Http\Requests\WithdrawalRequestForm;
use App\Notifications\WithdrawalRequestNotification;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'amount', 'status', 'purpose'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $requests = WithdrawalRequest::with('requester', 'approver')->orderBy($sort, $direction)->paginate(10);
        
        $pending = WithdrawalRequest::where('status', 'pending')->count();
        $approved = WithdrawalRequest::where('status', 'approved')->count();
        $rejected = WithdrawalRequest::where('status', 'rejected')->count();

        $zakatOut = WithdrawalRequest::where('type', 'zakat')->where('status', 'approved')->sum('amount');
        $zakatFitrOut = WithdrawalRequest::where('type', 'zakat_fitr')->where('status', 'approved')->sum('amount');
        $sadaqahOut = WithdrawalRequest::where('type', 'sadaqah')->where('status', 'approved')->sum('amount');
        $waqfOut = WithdrawalRequest::where('type', 'waqf')->where('status', 'approved')->sum('amount');

        // Current donation totals per Shariah type (confirmed funds only)
        $typeBalances = [
            'zakat' => Donation::where('category', 'zakat')->confirmed()->sum('amount') - $zakatOut,
            'zakat_fitr' => Donation::where('category', 'zakat_fitr')->confirmed()->sum('amount') - $zakatFitrOut,
            'sadaqah' => Donation::voluntary()->confirmed()->sum('amount') - $sadaqahOut,
            'waqf' => Donation::endowment()->confirmed()->sum('amount') - $waqfOut,
        ];
        
        return view('withdrawals.index', compact(
            'requests', 'sort', 'direction',
            'pending', 'approved', 'rejected',
            'zakatOut', 'zakatFitrOut', 'sadaqahOut', 'waqfOut',
            'typeBalances'
        ));
    }

    public function store(WithdrawalRequestForm $request)
    {
        $validated = $request->validated();

        $categoryMap = [
            'zakat' => fn($q) => $q->where('category', 'zakat'),
            'zakat_fitr' => fn($q) => $q->where('category', 'zakat_fitr'),
            'sadaqah' => fn($q) => $q->voluntary(),
            'waqf' => fn($q) => $q->endowment(),
        ];

        $donationQuery = $categoryMap[$validated['type']] ?? fn($q) => $q;
        $confirmedTotal = $donationQuery(Donation::query())->confirmed()->sum('amount');
        $approvedOut = WithdrawalRequest::where('type', $validated['type'])->where('status', 'approved')->sum('amount');
        $available = $confirmedTotal - $approvedOut;

        if ($validated['amount'] > $available) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => "Insufficient balance. Available for " . ucfirst(str_replace('_', ' ', $validated['type'])) . ": RM " . number_format($available, 2)]);
        }

        $withdrawal = WithdrawalRequest::create([
            'requested_by' => auth()->id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);

        // STEP 3: Notify all treasurers about new withdrawal request
        $treasurers = User::where('role', 'treasurer')->get();
        foreach ($treasurers as $treasurer) {
            $treasurer->notify(new WithdrawalRequestNotification($withdrawal, 'created'));
        }

        return redirect()->back()->with('success', 'Request submitted successfully! Treasurer has been notified.');
    }

    public function approve($id)
    {
        // STEP 1: Find the withdrawal request
        $withdrawalRequest = WithdrawalRequest::find($id);

        if ($withdrawalRequest) {
            // STEP 2: Update status to approved
            $withdrawalRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // STEP 3: Notify the requester about approval
            if ($withdrawalRequest->requester) {
                $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'approved'));
            }

            return redirect()->back()->with('success', 'Request Approved!');
        }

        return redirect()->back()->with('error', 'Request not found.');
    }

    public function reject(Request $request, $id)
    {
        // STEP 1: Find the withdrawal request
        $withdrawalRequest = WithdrawalRequest::find($id);

        if ($withdrawalRequest) {
            // STEP 2: Update status to rejected with reason
            $withdrawalRequest->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // STEP 3: Notify the requester about rejection
            if ($withdrawalRequest->requester) {
                $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'rejected'));
            }

            return redirect()->back()->with('success', 'Request Rejected.');
        }

        return redirect()->back()->with('error', 'Request not found.');
    }
}