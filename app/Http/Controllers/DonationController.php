<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\FundPurpose;
use App\Models\User;
use App\Models\ZakatAkad;
use App\Http\Requests\DonationRequest;
use App\Http\Requests\BatchDonationRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'donation_date');
        $direction = $request->get('direction', 'desc');
        $typeFilter = $request->get('type_filter', 'all');
        $statusFilter = $request->get('status_filter', 'all');

        $allowedSorts = ['donation_date', 'amount', 'category', 'source', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'donation_date';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = Donation::with('user', 'zakatAkad');

        if ($typeFilter !== 'all') {
            $query->ofType($typeFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $donations = $query->orderBy($sort, $direction)->paginate(10);

        $cashCount = Donation::where('source', 'cash')->count();
        $onlineCount = Donation::where('source', 'online')->count();
        $pendingCount = Donation::pending()->count();
        $pendingTotal = Donation::pending()->sum('amount');
        $confirmedTotal = Donation::confirmed()->sum('amount');
        $disputedTotal = Donation::disputed()->sum('amount');

        $zakatTotal = Donation::where('category', 'zakat')->sum('amount');
        $zakatFitrTotal = Donation::where('category', 'zakat_fitr')->sum('amount');
        $sadaqahTotal = Donation::voluntary()->sum('amount');
        $waqfTotal = Donation::endowment()->sum('amount');

        $fundPurposeBreakdown = Donation::whereNotNull('fund_purpose')
            ->selectRaw('fund_purpose, SUM(amount) as total')
            ->groupBy('fund_purpose')
            ->orderByDesc('total')
            ->pluck('total', 'fund_purpose');

        $suggestedPurposes = Donation::getSuggestedPurposes();
        $amilUsers = User::whereIn('role', ['admin', 'treasurer', 'member'])->orderBy('name')->get(['id', 'name']);

        return view('donations.index', compact(
            'donations', 'sort', 'direction', 'typeFilter', 'statusFilter',
            'cashCount', 'onlineCount', 'pendingCount', 'pendingTotal',
            'confirmedTotal', 'disputedTotal',
            'zakatTotal', 'zakatFitrTotal', 'sadaqahTotal', 'waqfTotal',
            'fundPurposeBreakdown', 'suggestedPurposes', 'amilUsers'
        ));
    }

    public function store(DonationRequest $request)
    {
        $validated = $request->validated();

        $donation = Donation::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'type' => $this->getDonationType($validated['category']),
            'fund_purpose' => in_array($validated['category'], ['zakat', 'zakat_fitr', 'waqf']) ? 'General Fund' : ($validated['fund_purpose'] ?? null),
            'source' => $validated['source'],
            'status' => $validated['status'] ?? 'pending',
            'reference' => $validated['reference'] ?? null,
            'donation_date' => $validated['donation_date'],
            'description' => $validated['description'] ?? null,
            'donor_name' => $validated['donor_name'] ?? null,
            'donor_ic' => $validated['donor_ic'] ?? null,
            'donor_phone' => $validated['donor_phone'] ?? null,
            'donor_email' => $validated['donor_email'] ?? null,
            'donor_address' => $validated['donor_address'] ?? null,
        ]);

        if (in_array($donation->category, ['zakat', 'zakat_fitr']) && $validated['amil_name']) {
            ZakatAkad::create([
                'donation_id' => $donation->id,
                'muzakki_name' => $validated['donor_name'] ?? $donation->donor_display_name,
                'muzakki_ic' => $validated['donor_ic'] ?? null,
                'amil_name' => $validated['amil_name'],
                'amil_user_id' => $validated['amil_user_id'] ?? null,
                'akad_date' => $validated['akad_date'] ?? $validated['donation_date'],
                'amount' => $validated['amount'],
                'notes' => $validated['akad_notes'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', __('islamic.flash_messages.recorded'));
    }

    public function confirm($id)
    {
        $donation = Donation::findOrFail($id);

        if ($donation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending donations can be confirmed.');
        }

        $donation->update([
            'status' => 'confirmed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Donation confirmed successfully.');
    }

    public function dispute($id)
    {
        $donation = Donation::findOrFail($id);

        if ($donation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending donations can be disputed.');
        }

        $donation->update([
            'status' => 'disputed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Donation marked as disputed.');
    }

    public function batchForm()
    {
        $suggestedPurposes = Donation::getSuggestedPurposes();
        return view('donations.batch', compact('suggestedPurposes'));
    }

    public function batchStore(BatchDonationRequest $request)
    {
        $validated = $request->validated();
        $count = 0;

        foreach ($validated['donations'] as $data) {
            Donation::create([
                'user_id' => auth()->id(),
                'amount' => $data['amount'],
                'category' => 'sadaqah',
                'type' => 'voluntary',
                'fund_purpose' => $data['fund_purpose'] ?? null,
                'source' => $data['source'],
                'status' => 'pending',
                'donation_date' => $data['donation_date'],
                'donor_name' => $data['donor_name'] ?? null,
                'description' => 'Batch entry',
            ]);
            $count++;
        }

        return redirect()->route('donations.batch.form')
            ->with('success', $count . ' donation(s) recorded successfully.');
    }

    public function printAkad($id)
    {
        $donation = Donation::with('zakatAkad.amilUser')->findOrFail($id);

        if (!$donation->zakatAkad) {
            return redirect()->back()->with('error', 'No akad record found for this donation.');
        }

        $akad = $donation->zakatAkad;

        $pdf = PDF::loadView('donations.akad-pdf', compact('donation', 'akad'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'akad_' . $akad->akad_reference . '.pdf';

        return $pdf->download($filename);
    }

    public function fundPurposeIndex()
    {
        $purposes = FundPurpose::ordered()->get();
        return view('admin.fund-purposes', compact('purposes'));
    }

    public function fundPurposeStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:fund_purposes,name']);
        FundPurpose::create(['name' => strip_tags(trim($data['name']))]);
        return redirect()->route('donations.fund-purposes')->with('success', 'Fund purpose added.');
    }

    public function fundPurposeUpdate(Request $request, FundPurpose $fundPurpose)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:fund_purposes,name,' . $fundPurpose->id]);
        $fundPurpose->update(['name' => strip_tags(trim($data['name']))]);
        return redirect()->route('donations.fund-purposes')->with('success', 'Fund purpose updated.');
    }

    public function fundPurposeDestroy(FundPurpose $fundPurpose)
    {
        $fundPurpose->delete();
        return redirect()->route('donations.fund-purposes')->with('success', 'Fund purpose deleted.');
    }

    private function getDonationType(string $category): string
    {
        $obligatory = ['zakat', 'zakat_fitr'];
        $endowment  = ['waqf'];

        if (in_array($category, $obligatory)) {
            return 'obligatory';
        }

        if (in_array($category, $endowment)) {
            return 'endowment';
        }

        return 'voluntary';
    }
}