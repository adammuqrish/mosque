<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = 8;

        $recommendedEvents = collect();
        $hasCriteria = true;

        if ($user && $user->role === 'member') {
            $recommendedEvents = $this->recommendationService->getRecommendations($user, $limit);

            $hasCriteria = $recommendedEvents->isNotEmpty()
                ? ($recommendedEvents->first()['hasCriteria'] ?? true)
                : false;
        }

        $sort = $request->get('sort', 'event_date');
        $direction = $request->get('direction', 'asc');
        
        $allowedSorts = ['event_date', 'title', 'status', 'created_at', 'max_volunteers'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'event_date';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $openEvents = Event::query()
            ->where('status', 'open')
            ->where('event_date', '>', now())
            ->where('max_volunteers', '>', function ($sub) {
                $sub->selectRaw('COUNT(*)')
                    ->from('event_volunteer')
                    ->whereColumn('event_id', 'events.id');
            })
            ->orderBy($sort, $direction)
            ->paginate(10);

        $donationStats = [
            'zakat' => Donation::where('category', 'zakat')->sum('amount'),
            'zakat_fitr' => Donation::where('category', 'zakat_fitr')->sum('amount'),
            'sadaqah' => Donation::voluntary()->sum('amount'),
            'waqf' => Donation::endowment()->sum('amount'),
            'pending' => Donation::pending()->count(),
            'pendingAmount' => Donation::pending()->sum('amount'),
            'thisMonth' => Donation::whereMonth('donation_date', now()->month)
                ->whereYear('donation_date', now()->year)
                ->sum('amount'),
            'onlineCount' => Donation::where('source', 'online')->count(),
            'cashCount' => Donation::where('source', 'cash')->count(),
        ];

        return view('dashboard', compact(
            'recommendedEvents', 'openEvents', 'hasCriteria', 'sort', 'direction',
            'donationStats'
        ));
    }
}
