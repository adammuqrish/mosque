<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\WithdrawalRequest;
use App\Models\Donation;
use App\Http\Requests\VolunteerProfileRequest;

class VolunteerController extends Controller
{
    public function profile()
    {
        $profile = VolunteerProfile::where('user_id', Auth::id())->first();
        return view('volunteer.profile', compact('profile'));
    }

    public function updateProfile(VolunteerProfileRequest $request)
    {
        // STEP 1: Get validated and sanitized data
        $validated = $request->validated();

        // STEP 2: Update or create volunteer profile
        VolunteerProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'skills' => $validated['skills'] ?? [],
                'availability' => $validated['availability'] ?? [],
                'hobbies' => $validated['hobbies'] ?? [],
                'interests' => $validated['interests'] ?? [],
                'languages' => $validated['languages'] ?? [],
                'experience' => $validated['experience'] ?? null,
                'location' => $validated['location'] ?? null,
                'health_status' => $validated['health_status'] ?? null,
                'long_term_availability' => $validated['long_term_availability'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function joinEvent(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return $this->joinResponse($request, false, 'Event not found.');
        }

        $user = Auth::user();

        if ($user->events()->where('event_id', $eventId)->exists()) {
            return $this->joinResponse($request, false, 'You have already joined this event.');
        }

        if ($event->status === 'closed') {
            return $this->joinResponse($request, false, 'This event is no longer accepting volunteers.');
        }

        if ($event->status === 'cancelled') {
            return $this->joinResponse($request, false, 'This event has been cancelled.');
        }

        if ($event->isPast()) {
            return $this->joinResponse($request, false, 'This event has already passed.');
        }

        if ($event->isFull()) {
            $priorityRedemption = $user->rewardRedemptions()
                ->whereHas('reward', function ($q) {
                    $q->where('code', 'PRIORITY_EVENT_REG');
                })
                ->where('status', 'claimed')
                ->whereNull('used_for_event_id')
                ->first();

            if ($priorityRedemption) {
                $priorityRedemption->consumeForEvent($eventId);
            } else {
                return $this->joinResponse($request, false, 'This event is full.');
            }
        }

        $user->events()->attach($eventId, ['status' => 'confirmed']);

        $event->refresh();
        $event->updateStatusBasedOnCapacity();

        return $this->joinResponse($request, true, 'Successfully joined the event!');
    }

    protected function joinResponse(Request $request, $success, $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ]);
        }

        return $success
            ? redirect()->back()->with('success', $message)
            : redirect()->back()->with('error', $message);
    }

    public function myEvents(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        $sort = $request->get('sort', 'newest');
        
        $query = $user->events();
        
        if ($filter === 'upcoming') {
            $query->where('event_date', '>=', now()->toDateString());
        } elseif ($filter === 'past') {
            $query->where('event_date', '<', now()->toDateString());
        }
        
        if ($sort === 'oldest') {
            $query->orderBy('event_date', 'asc');
        } else {
            $query->orderBy('event_date', 'desc');
        }
        
        $myEvents = $query->get();
        
        $stats = [
            'total' => $user->events()->count(),
            'confirmed' => $user->events()->wherePivot('attendance_status', 'confirmed')->count(),
            'completed' => $user->events()->wherePivot('attendance_status', 'completed')->count(),
            'absent' => $user->events()->wherePivot('attendance_status', 'absent')->count(),
        ];
        
        return view('volunteer.my-events', compact('myEvents', 'filter', 'sort', 'stats'));
    }

    public function leaveEvent($eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        $user = Auth::user();

        if (!$user->events()->where('event_id', $eventId)->exists()) {
            return redirect()->back()->with('error', 'You have not joined this event.');
        }

        if ($event->isPast()) {
            return redirect()->back()->with('error', 'Cannot leave an event that has already passed.');
        }

        $user->events()->detach($eventId);

        if ($event->status === 'closed' && !$event->isFull()) {
            $event->update(['status' => 'open']);
        }

        return redirect()->back()->with('success', 'You have left the event successfully!');
    }

    public function transparency(Request $request)
    {
        $baseMonth = Donation::whereMonth('donation_date', now()->month)->whereYear('donation_date', now()->year);
        $baseYear = Donation::whereYear('donation_date', now()->year);

        $zakatMonth = (clone $baseMonth)->where('category', 'zakat')->sum('amount');
        $zakatFitrMonth = (clone $baseMonth)->where('category', 'zakat_fitr')->sum('amount');
        $sadaqahMonth = (clone $baseMonth)->voluntary()->sum('amount');
        $waqfMonth = (clone $baseMonth)->endowment()->sum('amount');
        $zakatYear = (clone $baseYear)->where('category', 'zakat')->sum('amount');
        $zakatFitrYear = (clone $baseYear)->where('category', 'zakat_fitr')->sum('amount');
        $sadaqahYear = (clone $baseYear)->voluntary()->sum('amount');
        $waqfYear = (clone $baseYear)->endowment()->sum('amount');

        $expensesByType = WithdrawalRequest::where('status', 'approved')
            ->whereYear('approved_at', now()->year)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');
        $zakatSpentYear = $expensesByType->get('zakat', 0);
        $zakatFitrSpentYear = $expensesByType->get('zakat_fitr', 0);
        $sadaqahSpentYear = $expensesByType->get('sadaqah', 0);
        $waqfSpentYear = $expensesByType->get('waqf', 0);

        $expenses = WithdrawalRequest::where('status', 'approved')
            ->whereYear('approved_at', now()->year)
            ->orderBy('approved_at', 'desc')
            ->paginate(10);

        return view('transparency.index', compact(
            'zakatMonth', 'zakatFitrMonth', 'sadaqahMonth', 'waqfMonth',
            'zakatYear', 'zakatFitrYear', 'sadaqahYear', 'waqfYear',
            'zakatSpentYear', 'zakatFitrSpentYear', 'sadaqahSpentYear', 'waqfSpentYear',
            'expenses'
        ));
    }
}