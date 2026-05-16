<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $donationCount = Donation::count();
        $zakatTotal = Donation::where('category', 'zakat')->sum('amount');
        $zakatFitrTotal = Donation::where('category', 'zakat_fitr')->sum('amount');
        $sadaqahTotal = Donation::voluntary()->sum('amount');
        $waqfTotal = Donation::endowment()->sum('amount');
        $volunteerCount = User::where('role', 'member')->count();
        $eventCount = Event::where('status', 'closed')->count();
        $upcomingEvents = Event::where('status', 'open')
            ->where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->limit(3)
            ->get();

        $distributions = [
            'zakat' => [
                'label' => 'Zakat',
                'collected' => Donation::where('category', 'zakat')->sum('amount'),
                'distributed' => WithdrawalRequest::where('type', 'zakat')->where('status', 'approved')->sum('amount'),
            ],
            'zakat_fitr' => [
                'label' => 'Zakat Fitr',
                'collected' => Donation::where('category', 'zakat_fitr')->sum('amount'),
                'distributed' => WithdrawalRequest::where('type', 'zakat_fitr')->where('status', 'approved')->sum('amount'),
            ],
            'sadaqah' => [
                'label' => 'Sadaqah',
                'collected' => Donation::voluntary()->sum('amount'),
                'distributed' => WithdrawalRequest::where('type', 'sadaqah')->where('status', 'approved')->sum('amount'),
            ],
            'waqf' => [
                'label' => 'Waqf',
                'collected' => Donation::endowment()->sum('amount'),
                'distributed' => WithdrawalRequest::where('type', 'waqf')->where('status', 'approved')->sum('amount'),
            ],
        ];
        foreach ($distributions as &$d) {
            $d['percent'] = $d['collected'] > 0 ? min(round(($d['distributed'] / $d['collected']) * 100), 100) : 0;
        }
        unset($d);

        return view('landing', compact(
            'donationCount',
            'zakatTotal',
            'zakatFitrTotal',
            'sadaqahTotal',
            'waqfTotal',
            'volunteerCount',
            'eventCount',
            'upcomingEvents',
            'distributions'
        ));
    }
}
