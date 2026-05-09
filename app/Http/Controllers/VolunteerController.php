<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WithdrawalRequest;
use App\Models\Donation;

class VolunteerController extends Controller
{
    // 1. Tunjuk Profile & Form untuk Update
    public function profile()
    {
        // Ambil profile user yang tengah login, kalau tak ada create default null
        $profile = VolunteerProfile::where('user_id', Auth::id())->first();

        return view('volunteer.profile', compact('profile'));
    }

    // 2. Simpan/Update Profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'skills' => 'required|string', // Input text contoh: "Cooking, Cleaning"
            'availability' => 'array',     // Array dari checkbox
        ]);

        // Process Skills: Tukar string "Cooking, Cleaning" jadi Array ["Cooking", "Cleaning"]
        $skillsArray = array_map('trim', explode(',', $request->skills));

        // Update atau Create Profile (Berguna jika user tak ada profile lagi)
        VolunteerProfile::updateOrCreate(
            ['user_id' => Auth::id()], // Cari ikut ID user
            [
                'skills' => json_encode($skillsArray),
                'availability' => json_encode($request->availability),
            ]
        );

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // 3. Join Event
    public function joinEvent($eventId)
    {
        $event = Event::find($eventId);
        $user = Auth::user();

        // Cek kalau user dah join
        if ($user->events()->where('event_id', $eventId)->exists()) {
            return redirect()->back()->with('error', 'You have already joined this event.');
        }

        // Attach user ke event (Masuk dalam table `event_volunteer`)
        $user->events()->attach($eventId, ['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Successfully joined the event!');
    }

    // 4. Paparkan Event yang User dah join
    public function myEvents()
    {
        $user = Auth::user();

        // Ambil event yang user join, dengan pivot data (status join_at)
        // orderBy('event_date') supaya event terdekat di atas
        $myEvents = $user->events()->orderBy('event_date')->get();

        return view('volunteer.my-events', compact('myEvents'));
    }

    // 5. Paparkan Transparansi Kewangan untuk Jemaah
    public function transparency()
    {
        // 1. Calculate Defaults (Today, Month, Year)
        $donationToday = Donation::whereDate('donation_date', now()->toDateString())->sum('amount');
        $donationMonth = Donation::whereMonth('donation_date', now()->month)
            ->whereYear('donation_date', now()->year)
            ->sum('amount');
        $donationYear = Donation::whereYear('donation_date', now()->year)->sum('amount');

        // 2. Handle Custom Date Range Filter
        $expenses = WithdrawalRequest::where('status', 'approved');
        $customRangeTotal = 0;
        $isFilterActive = false;

        if (request()->has('start_date') && request()->has('end_date')) {
            $isFilterActive = true;
            $start = request()->start_date;
            $end = request()->end_date;

            // Filter Expenses for the selected range (Agaknya pengeluaran juga ikut tarikh request)
            $expenses->whereBetween('created_at', [$start, $end]);

            // Calculate Donation for selected range
            $customRangeTotal = Donation::whereBetween('donation_date', [$start, $end])->sum('amount');
        }

        $expenses = $expenses->orderBy('approved_at', 'desc')->get();
        $totalSpent = $expenses->sum('amount');

        return view('transparency.index', compact(
            'donationToday',
            'donationMonth',
            'donationYear',
            'expenses',
            'totalSpent',
            'customRangeTotal',
            'isFilterActive'
        ));
    }
}