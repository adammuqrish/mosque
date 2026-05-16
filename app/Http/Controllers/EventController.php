<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $sortEvent = $request->get('sort_event', 'event_date');
        $directionEvent = $request->get('direction_event', 'desc');
        
        $allowedSorts = ['event_date', 'title', 'status', 'created_at'];
        if (!in_array($sortEvent, $allowedSorts)) {
            $sortEvent = 'event_date';
        }
        if (!in_array($directionEvent, ['asc', 'desc'])) {
            $directionEvent = 'asc';
        }

        $events = Event::orderBy($sortEvent, $directionEvent)->paginate(10);
        return view('events.index', compact('events', 'sortEvent', 'directionEvent'));
    }

    public function store(EventRequest $request)
    {
        // STEP 1: Get validated and sanitized data
        $validated = $request->validated();

        // STEP 2: Parse comma-separated strings to arrays
        $parse = function ($str) {
            return array_filter(array_map('trim', explode(',', $str ?? '')));
        };

        $skills = $parse($validated['required_skills'] ?? '');
        $hobbies = $parse($validated['required_hobbies'] ?? '');
        $languages = $parse($validated['required_languages'] ?? '');

        // STEP 3: Create event with default status 'open'
        Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
            'max_volunteers' => $validated['max_volunteers'],
            'required_skills' => $skills,
            'required_hobbies' => $hobbies,
            'required_languages' => $languages,
            'event_location' => $validated['event_location'],
            'health_requirement' => $validated['health_requirement'] ?? null,
            'status' => 'open',
            'gamification_category' => $validated['gamification_category'],
        ]);

        return redirect()->route('events.manage')->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        if (!$event->canEdit()) {
            return redirect()->route('events.manage')->with('error', 'Cannot edit events that have already passed.');
        }

        $sortEvent = request()->get('sort_event', 'event_date');
        $directionEvent = request()->get('direction_event', 'desc');
        $allowedSorts = ['event_date', 'title', 'status', 'created_at'];
        if (!in_array($sortEvent, $allowedSorts)) {
            $sortEvent = 'event_date';
        }
        if (!in_array($directionEvent, ['asc', 'desc'])) {
            $directionEvent = 'asc';
        }

        $events = Event::orderBy($sortEvent, $directionEvent)->paginate(10);
        
        return view('events.index', compact('event', 'events', 'sortEvent', 'directionEvent'));
    }

    public function update(UpdateEventRequest $request, $id)
    {
        // STEP 1: Find the event
        $event = Event::find($id);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        // STEP 2: Check if event can be edited
        if (!$event->canEdit()) {
            return redirect()->route('events.manage')->with('error', 'Cannot edit events that have already passed.');
        }

        // STEP 3: Get validated data
        $validated = $request->validated();

        // STEP 4: Parse comma-separated strings to arrays
        $parse = function ($str) {
            return array_filter(array_map('trim', explode(',', $str ?? '')));
        };

        $skills = $parse($validated['required_skills'] ?? '');
        $hobbies = $parse($validated['required_hobbies'] ?? '');
        $languages = $parse($validated['required_languages'] ?? '');

        // STEP 5: Update event
        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
            'max_volunteers' => $validated['max_volunteers'],
            'required_skills' => $skills,
            'required_hobbies' => $hobbies,
            'required_languages' => $languages,
            'event_location' => $validated['event_location'],
            'health_requirement' => $validated['health_requirement'] ?? null,
            'gamification_category' => $validated['gamification_category'],
        ]);

        // STEP 6: Refresh to get updated volunteer count
        $event->refresh();

        // STEP 7: Auto-update status based on capacity
        $event->updateStatusBasedOnCapacity();

        return redirect()->route('events.manage')->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        // STEP 1: Find and delete the event
        $event = Event::find($id);
        if ($event) {
            $event->delete();
            return redirect()->route('events.manage')->with('success', 'Event deleted successfully!');
        }
        return redirect()->route('events.manage')->with('error', 'Event not found.');
    }

    // STEP 8: Change event status (open/close/cancel)
    public function changeStatus(Request $request, $id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        // STEP 9: Validate status change
        $newStatus = $request->input('status');
        $validStatuses = ['open', 'closed', 'cancelled'];
        
        if (!in_array($newStatus, $validStatuses)) {
            return redirect()->route('events.manage')->with('error', 'Invalid status.');
        }

        // STEP 10: Check if opening a full event (must increase capacity first)
        if ($newStatus === 'open' && $event->isFull()) {
            return redirect()->route('events.manage')->with('error', 'Cannot open event - volunteers are at maximum capacity. Please increase max volunteers first.');
        }

        // STEP 11: Cannot reopen cancelled events
        if ($newStatus !== 'cancelled' && $event->status === 'cancelled') {
            return redirect()->route('events.manage')->with('error', 'Cannot reopen a cancelled event. Create a new event instead.');
        }

        // STEP 12: Update status
        $event->update(['status' => $newStatus]);

        $statusMessages = [
            'open' => 'Event opened successfully!',
            'closed' => 'Event closed successfully!',
            'cancelled' => 'Event cancelled successfully!',
        ];

        return redirect()->route('events.manage')->with('success', $statusMessages[$newStatus] ?? 'Status updated.');
    }

    // STEP 13: Remove volunteer from event
    public function removeVolunteer(Request $request, $eventId, $userId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        // STEP 14: Check if user is enrolled
        if (!$event->volunteers()->where('user_id', $userId)->exists()) {
            return redirect()->route('events.manage')->with('error', 'User is not enrolled in this event.');
        }

        // STEP 15: Remove volunteer
        $event->volunteers()->detach($userId);

        // STEP 16: If event was full, try to reopen it
        $event->refresh();
        if ($event->status === 'closed' && !$event->isFull()) {
            $event->update(['status' => 'open']);
            return redirect()->route('events.manage')->with('success', 'Volunteer removed. Event has been reopened as spots are now available.');
        }

        return redirect()->route('events.manage')->with('success', 'Volunteer removed successfully!');
    }

    // STEP 17: View event volunteers
    public function volunteers($id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        $volunteers = $event->volunteers()->get();
        
        return view('events.volunteers', compact('event', 'volunteers'));
    }

    // STEP 18: Update volunteer attendance status
    public function updateAttendance(Request $request, $eventId, $userId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        // STEP 19: Validate attendance status
        $request->validate([
            'attendance_status' => 'required|in:confirmed,pending_review,completed,absent',
            'absence_reason' => 'nullable|string|max:500',
        ]);

        // STEP 20: Check if user is enrolled
        if (!$event->volunteers()->where('user_id', $userId)->exists()) {
            return redirect()->route('events.manage')->with('error', 'Volunteer not found in this event.');
        }

        // STEP 21: Update pivot with attendance status and optional reason
        $updateData = [
            'attendance_status' => $request->input('attendance_status'),
        ];

        // Only save reason if marking as absent
        if ($request->input('attendance_status') === 'absent') {
            $updateData['absence_reason'] = $request->input('absence_reason');
        } else {
            $updateData['absence_reason'] = null;
        }

        // Update using syncWithPivotValues for pivot table
        $event->volunteers()->updateExistingPivot($userId, $updateData);

        // STEP 22: Manually trigger gamification if status changed to completed
        if ($request->input('attendance_status') === 'completed') {
            try {
                $volunteer = \App\Models\EventVolunteer::where('event_id', $eventId)
                    ->where('user_id', $userId)
                    ->first();
                if ($volunteer && !$volunteer->points_awarded) {
                    $gamificationService = app(\App\Services\GamificationService::class);
                    $result = $gamificationService->awardPointsForEventCompletion($volunteer);

                    \Illuminate\Support\Facades\Log::info("Manual gamification: Points awarded", [
                        'user_id' => $userId,
                        'event_id' => $eventId,
                        'total_points' => $result['points_earned'] ?? 0,
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Manual gamification failed", [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('events.manage')->with('success', 'Attendance updated successfully!');
    }

    // STEP 22: Bulk mark all pending_review volunteers as completed
    public function bulkApproveAttendance(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        // STEP 23: Get all volunteers with pending_review status and mark as completed
        $pendingVolunteers = $event->volunteers()->wherePivot('attendance_status', 'pending_review')->get();
        $count = $pendingVolunteers->count();

        foreach ($pendingVolunteers as $volunteer) {
            $event->volunteers()->updateExistingPivot($volunteer->id, [
                'attendance_status' => 'completed',
                'absence_reason' => null,
            ]);

            // STEP 24: Manually trigger gamification for each volunteer marked as completed
            try {
                $updatedVolunteer = \App\Models\EventVolunteer::where('event_id', $eventId)
                    ->where('user_id', $volunteer->id)
                    ->first();
                if ($updatedVolunteer && !$updatedVolunteer->points_awarded) {
                    $gamificationService = app(\App\Services\GamificationService::class);
                    $result = $gamificationService->awardPointsForEventCompletion($updatedVolunteer);

                    \Illuminate\Support\Facades\Log::info("Bulk gamification: Points awarded", [
                        'user_id' => $volunteer->id,
                        'event_id' => $eventId,
                        'total_points' => $result['points_earned'] ?? 0,
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bulk gamification failed", [
                    'user_id' => $volunteer->id,
                    'event_id' => $eventId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($count > 0) {
            return redirect()->route('events.manage')->with('success', "{$count} volunteers marked as completed!");
        }

        return redirect()->route('events.manage')->with('info', 'No pending volunteers to approve.');
    }

    // STEP 24: Mark all pending_review volunteers as absent
    public function bulkMarkAbsent(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return redirect()->route('events.manage')->with('error', 'Event not found.');
        }

        $request->validate([
            'absence_reason' => 'nullable|string|max:500',
        ]);

        $pendingVolunteers = $event->volunteers()->wherePivot('attendance_status', 'pending_review')->get();
        $count = $pendingVolunteers->count();

        foreach ($pendingVolunteers as $volunteer) {
            $event->volunteers()->updateExistingPivot($volunteer->id, [
                'attendance_status' => 'absent',
                'absence_reason' => $request->input('absence_reason'),
            ]);
        }

        if ($count > 0) {
            return redirect()->route('events.manage')->with('success', "{$count} volunteers marked as absent!");
        }

        return redirect()->route('events.manage')->with('info', 'No pending volunteers to mark absent.');
    }
}
