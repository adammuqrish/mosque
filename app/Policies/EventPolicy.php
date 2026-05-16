<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Event $event): bool
    {
        // Only admin can update
        if ($user->role !== 'admin') {
            return false;
        }
        
        // Cannot update past events
        return !$event->isPast();
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function changeStatus(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function manageVolunteers(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function removeVolunteer(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function join(User $user, Event $event): bool
    {
        // Cannot join if not a member
        if ($user->role !== 'member') {
            return false;
        }
        
        // Cannot join closed events
        if ($event->status === 'closed') {
            return false;
        }
        
        // Cannot join cancelled events
        if ($event->status === 'cancelled') {
            return false;
        }
        
        // Cannot join past events
        if ($event->isPast()) {
            return false;
        }
        
        // Cannot join full events
        return !$event->isFull();
    }

    public function markAttendance(User $user, Event $event): bool
    {
        // Only admin can mark attendance
        if ($user->role !== 'admin') {
            return false;
        }
        
        // Can only mark attendance for past events that need review
        return $event->needsReview();
    }

    public function bulkAttendance(User $user, Event $event): bool
    {
        // Only admin can do bulk attendance operations
        return $user->role === 'admin';
    }
}
