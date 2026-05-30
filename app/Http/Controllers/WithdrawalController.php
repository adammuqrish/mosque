<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\FundPurpose;
use App\Models\User;
use App\Models\WithdrawalDocument;
use App\Models\WithdrawalRequest;
use App\Http\Requests\WithdrawalRequestForm;
use App\Notifications\WithdrawalRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'amount', 'status', 'purpose', 'fund_purpose'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $requests = WithdrawalRequest::with('requester', 'approver', 'makerChecker')->orderBy($sort, $direction)->paginate(10);
        
        $pending = WithdrawalRequest::where('status', 'pending')->count();
        $makerChecked = WithdrawalRequest::where('status', 'maker_checked')->count();
        $approved = WithdrawalRequest::where('status', 'approved')->count();
        $rejected = WithdrawalRequest::where('status', 'rejected')->count();

        $zakatOut = WithdrawalRequest::where('type', 'zakat')->where('status', 'approved')->sum('amount');
        $zakatFitrOut = WithdrawalRequest::where('type', 'zakat_fitr')->where('status', 'approved')->sum('amount');
        $sadaqahOut = WithdrawalRequest::where('type', 'sadaqah')->where('status', 'approved')->sum('amount');
        $waqfOut = WithdrawalRequest::where('type', 'waqf')->where('status', 'approved')->sum('amount');

        $zakatReserved = WithdrawalRequest::where('type', 'zakat')->whereIn('status', ['pending', 'maker_checked'])->sum('amount');
        $zakatFitrReserved = WithdrawalRequest::where('type', 'zakat_fitr')->whereIn('status', ['pending', 'maker_checked'])->sum('amount');
        $sadaqahReserved = WithdrawalRequest::where('type', 'sadaqah')->whereIn('status', ['pending', 'maker_checked'])->sum('amount');
        $waqfReserved = WithdrawalRequest::where('type', 'waqf')->whereIn('status', ['pending', 'maker_checked'])->sum('amount');

        $typeBalances = [
            'zakat' => Donation::where('category', 'zakat')->confirmed()->sum('amount') - $zakatOut - $zakatReserved,
            'zakat_fitr' => Donation::where('category', 'zakat_fitr')->confirmed()->sum('amount') - $zakatFitrOut - $zakatFitrReserved,
            'sadaqah' => Donation::voluntary()->confirmed()->sum('amount') - $sadaqahOut - $sadaqahReserved,
            'waqf' => Donation::endowment()->confirmed()->sum('amount') - $waqfOut - $waqfReserved,
        ];

        $fundPurposes = FundPurpose::active()->ordered()->get();

        $purposeBalances = [];
        $purposeTypeBalances = [];
        $types = ['zakat', 'zakat_fitr', 'sadaqah', 'waqf'];
        foreach ($fundPurposes as $fp) {
            foreach ($types as $type) {
                $typeCategoryMap = [
                    'zakat' => 'zakat',
                    'zakat_fitr' => 'zakat_fitr',
                    'sadaqah' => 'sadaqah',
                    'waqf' => 'waqf',
                ];
                $cat = $typeCategoryMap[$type];
                $in = Donation::where('fund_purpose', $fp->name)->where('category', $cat)->confirmed()->sum('amount');
                $out = WithdrawalRequest::where('fund_purpose', $fp->name)->where('type', $type)->where('status', 'approved')->sum('amount');
                $reserved = WithdrawalRequest::where('fund_purpose', $fp->name)->where('type', $type)->whereIn('status', ['pending', 'maker_checked'])->sum('amount');
                $purposeTypeBalances[$fp->name][$type] = $in - $out - $reserved;
            }
            $purposeBalances[$fp->name] = max(0, collect($purposeTypeBalances[$fp->name])->sum());
        }
        
        return view('withdrawals.index', compact(
            'requests', 'sort', 'direction',
            'pending', 'makerChecked', 'approved', 'rejected',
            'zakatOut', 'zakatFitrOut', 'sadaqahOut', 'waqfOut',
            'zakatReserved', 'zakatFitrReserved', 'sadaqahReserved', 'waqfReserved',
            'typeBalances', 'fundPurposes', 'purposeBalances', 'purposeTypeBalances'
        ));
    }

    public function store(WithdrawalRequestForm $request)
    {
        $validated = $request->validated();

        $categoryMap = [
            'zakat' => function ($q) { return $q->where('category', 'zakat'); },
            'zakat_fitr' => function ($q) { return $q->where('category', 'zakat_fitr'); },
            'sadaqah' => function ($q) { return $q->voluntary(); },
            'waqf' => function ($q) { return $q->endowment(); },
        ];

        $donationQuery = $categoryMap[$validated['type']] ?? function ($q) { return $q; };
        $confirmedTotal = $donationQuery(Donation::query())->confirmed()->sum('amount');

        // Layer 1: Count ALL non-rejected withdrawals (pending + maker_checked + approved)
        $committedOut = WithdrawalRequest::where('type', $validated['type'])
            ->whereIn('status', ['pending', 'maker_checked', 'approved'])->sum('amount');
        $available = $confirmedTotal - $committedOut;

        if ($validated['amount'] > $available) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => "Insufficient balance. Available for " . ucfirst(str_replace('_', ' ', $validated['type'])) . ": RM " . number_format($available, 2)]);
        }

        $purposeIn = Donation::where('fund_purpose', $validated['fund_purpose'])->where('category', $validated['type'])->confirmed()->sum('amount');
        $purposeCommittedOut = WithdrawalRequest::where('fund_purpose', $validated['fund_purpose'])->where('type', $validated['type'])
            ->whereIn('status', ['pending', 'maker_checked', 'approved'])->sum('amount');
        $purposeAvailable = $purposeIn - $purposeCommittedOut;

        if ($validated['amount'] > $purposeAvailable) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => "Insufficient balance for fund purpose '{$validated['fund_purpose']}' in " . ucfirst(str_replace('_', ' ', $validated['type'])) . ". Available: RM " . number_format($purposeAvailable, 2)]);
        }

        $withdrawal = WithdrawalRequest::create([
            'requested_by' => auth()->id(),
            'type' => $validated['type'],
            'fund_purpose' => $validated['fund_purpose'],
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);

        $this->handleDocumentUploads($withdrawal, $request, 'admin');

        // STEP 3: Notify all treasurers about new withdrawal request
        $treasurers = User::where('role', 'treasurer')->get();
        foreach ($treasurers as $treasurer) {
            $treasurer->notify(new WithdrawalRequestNotification($withdrawal, 'created'));
        }

        return redirect()->back()->with('success', 'Request submitted successfully! Treasurer has been notified.');
    }

    public function approve(Request $request, $id)
    {
        // Layer 3: Database transaction with row-level locking to prevent race conditions
        return DB::transaction(function () use ($request, $id) {
            $withdrawalRequest = WithdrawalRequest::with('makerChecker')->lockForUpdate()->find($id);

            if (!$withdrawalRequest) {
                return redirect()->back()->with('error', 'Request not found.');
            }

            if ($withdrawalRequest->requested_by === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak boleh meluluskan permintaan sendiri.');
            }

            if ($withdrawalRequest->status === 'approved') {
                return redirect()->back()->with('error', 'This request is already fully approved.');
            }

            if ($withdrawalRequest->status === 'rejected') {
                return redirect()->back()->with('error', 'This request has been rejected.');
            }

            $needsDual = $withdrawalRequest->needsMakerChecker();

            if ($needsDual && $withdrawalRequest->status === 'maker_checked') {
                if ($withdrawalRequest->maker_checked_by === auth()->id()) {
                    return redirect()->back()->with('error', 'Anda tidak boleh meluluskan permintaan yang telah disemak oleh diri sendiri.');
                }

                // Layer 2: Re-validate balance before final approval
                $categoryMap = [
                    'zakat' => function ($q) { return $q->where('category', 'zakat'); },
                    'zakat_fitr' => function ($q) { return $q->where('category', 'zakat_fitr'); },
                    'sadaqah' => function ($q) { return $q->voluntary(); },
                    'waqf' => function ($q) { return $q->endowment(); },
                ];
                $donationQuery = $categoryMap[$withdrawalRequest->type] ?? function ($q) { return $q; };
                $confirmedTotal = $donationQuery(Donation::query())->confirmed()->sum('amount');

                // Count all approved + pending/maker_checked EXCEPT this one (it's currently maker_checked)
                $otherCommittedOut = WithdrawalRequest::where('type', $withdrawalRequest->type)
                    ->whereIn('status', ['pending', 'maker_checked', 'approved'])
                    ->where('id', '!=', $withdrawalRequest->id)
                    ->sum('amount');
                $available = $confirmedTotal - $otherCommittedOut;

                if ($withdrawalRequest->amount > $available) {
                    return redirect()->back()->with('error', "Insufficient balance at time of approval. Available: RM " . number_format($available, 2));
                }

                $purposeIn = Donation::where('fund_purpose', $withdrawalRequest->fund_purpose)->confirmed()->sum('amount');
                $otherPurposeOut = WithdrawalRequest::where('fund_purpose', $withdrawalRequest->fund_purpose)
                    ->whereIn('status', ['pending', 'maker_checked', 'approved'])
                    ->where('id', '!=', $withdrawalRequest->id)
                    ->sum('amount');
                $purposeAvailable = $purposeIn - $otherPurposeOut;

                if ($withdrawalRequest->amount > $purposeAvailable) {
                    return redirect()->back()->with('error', "Insufficient balance for fund purpose '{$withdrawalRequest->fund_purpose}'. Available: RM " . number_format($purposeAvailable, 2));
                }

                $withdrawalRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                $this->handleDocumentUploads($withdrawalRequest, $request, 'treasurer');

                if ($withdrawalRequest->requester) {
                    $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'approved'));
                }

                return redirect()->back()->with('success', 'Request fully approved!');
            }

            if ($needsDual) {
                $withdrawalRequest->update([
                    'status' => 'maker_checked',
                    'maker_checked_by' => auth()->id(),
                    'maker_checked_at' => now(),
                ]);

                $this->handleDocumentUploads($withdrawalRequest, $request, 'treasurer');

                $treasurers = User::where('role', 'treasurer')->where('id', '!=', auth()->id())->get();
                foreach ($treasurers as $treasurer) {
                    $treasurer->notify(new WithdrawalRequestNotification($withdrawalRequest, 'maker_checked'));
                }
                if ($withdrawalRequest->requester) {
                    $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'maker_checked'));
                }

                return redirect()->back()->with('success', 'First check approved! A second treasurer must approve this high-value request.');
            }

            // Layer 2: Re-validate balance before approving (for non-dual requests)
            $categoryMap = [
                'zakat' => function ($q) { return $q->where('category', 'zakat'); },
                'zakat_fitr' => function ($q) { return $q->where('category', 'zakat_fitr'); },
                'sadaqah' => function ($q) { return $q->voluntary(); },
                'waqf' => function ($q) { return $q->endowment(); },
            ];
            $donationQuery = $categoryMap[$withdrawalRequest->type] ?? function ($q) { return $q; };
            $confirmedTotal = $donationQuery(Donation::query())->confirmed()->sum('amount');

            $otherCommittedOut = WithdrawalRequest::where('type', $withdrawalRequest->type)
                ->whereIn('status', ['pending', 'maker_checked', 'approved'])
                ->where('id', '!=', $withdrawalRequest->id)
                ->sum('amount');
            $available = $confirmedTotal - $otherCommittedOut;

            if ($withdrawalRequest->amount > $available) {
                return redirect()->back()->with('error', "Insufficient balance at time of approval. Available: RM " . number_format($available, 2));
            }

            $purposeIn = Donation::where('fund_purpose', $withdrawalRequest->fund_purpose)->confirmed()->sum('amount');
            $otherPurposeOut = WithdrawalRequest::where('fund_purpose', $withdrawalRequest->fund_purpose)
                ->whereIn('status', ['pending', 'maker_checked', 'approved'])
                ->where('id', '!=', $withdrawalRequest->id)
                ->sum('amount');
            $purposeAvailable = $purposeIn - $otherPurposeOut;

            if ($withdrawalRequest->amount > $purposeAvailable) {
                return redirect()->back()->with('error', "Insufficient balance for fund purpose '{$withdrawalRequest->fund_purpose}'. Available: RM " . number_format($purposeAvailable, 2));
            }

            $withdrawalRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->handleDocumentUploads($withdrawalRequest, $request, 'treasurer');

            if ($withdrawalRequest->requester) {
                $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'approved'));
            }

            return redirect()->back()->with('success', 'Request Approved!');
        });
    }

    public function reject(Request $request, $id)
    {
        $withdrawalRequest = WithdrawalRequest::find($id);

        if (!$withdrawalRequest) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        if ($withdrawalRequest->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot reject an already approved request.');
        }

        if ($withdrawalRequest->requested_by === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak boleh menolak permintaan sendiri.');
        }

        $withdrawalRequest->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        if ($withdrawalRequest->requester) {
            $withdrawalRequest->requester->notify(new WithdrawalRequestNotification($withdrawalRequest, 'rejected'));
        }

        return redirect()->back()->with('success', 'Request Rejected.');
    }

    public function uploadDocuments(Request $request, $id)
    {
        $withdrawalRequest = WithdrawalRequest::find($id);

        if (!$withdrawalRequest) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        $this->handleDocumentUploads($withdrawalRequest, $request, auth()->user()->role);

        return redirect()->back()->with('success', 'Document(s) uploaded successfully.');
    }

    protected function handleDocumentUploads(WithdrawalRequest $withdrawal, Request $request, string $uploaderRole)
    {
        if (!$request->hasFile('documents')) {
            return;
        }

        $files = $request->file('documents');
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $maxFiles = 5;

        $existingCount = $withdrawal->documents()->count();
        $uploadCount = 0;

        foreach ($files as $file) {
            if ($uploadCount >= $maxFiles) {
                break;
            }

            if ($existingCount + $uploadCount >= 10) {
                break;
            }

            if (!$file->isValid()) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedTypes)) {
                continue;
            }

            if ($file->getSize() > $maxSize) {
                continue;
            }

            $subDir = $uploaderRole === 'admin' ? 'invoices' : 'proofs';
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $baseName);
            $timestamp = time() . '_' . uniqid();
            $filename = "{$timestamp}_{$safeName}.{$extension}";
            $path = $file->storeAs("withdrawals/{$withdrawal->id}/{$subDir}", $filename, 'public');

            $withdrawal->documents()->create([
                'uploaded_by' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'description' => $request->input('document_descriptions.' . $uploadCount),
            ]);

            $uploadCount++;
        }
    }
}