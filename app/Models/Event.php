<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'end_time',
        'location',
        'max_volunteers',
        'required_skills',
        'required_hobbies',
        'required_languages',
        'event_location',
        'location_radius',
        'health_requirement',
        'status',
        'gamification_category',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'required_hobbies' => 'array',
        'required_languages' => 'array',
        'event_date' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Default status
    protected $attributes = [
        'status' => 'open',
    ];

    // Relationship: Many-to-Many with Volunteers (Users)
    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'event_volunteer')
            ->withPivot('status', 'joined_at', 'attendance_status', 'absence_reason');
    }

    // STEP 1: Check if event needs review (event passed + 24 hours)
    public function needsReview(): bool
    {
        // Original delayed review logic:
        // return $this->event_date->addHours(24)->isPast() && $this->status !== 'cancelled';

        // Immediate review logic for testing: show yellow button as soon as the event is past.
        return $this->isPast() && $this->status !== 'cancelled';
    }

    // STEP 2: Get volunteers by attendance status
    public function getVolunteersByStatus(string $status)
    {
        return $this->volunteers()->wherePivot('attendance_status', $status)->get();
    }

    // STEP 3: Get count of pending review volunteers
    public function getPendingReviewCountAttribute(): int
    {
        return $this->volunteers()->wherePivot('attendance_status', 'pending_review')->count();
    }

    // STEP 4: Determine if there are reviewable volunteers for UI prompt
    public function hasReviewableAttendance(): bool
    {
        return $this->pendingReviewCount > 0 || ($this->confirmedCount > 0 && $this->isPast());
    }

    // STEP 4: Get count of completed volunteers
    public function getCompletedCountAttribute(): int
    {
        return $this->volunteers()->wherePivot('attendance_status', 'completed')->count();
    }

    // STEP 5: Get count of absent volunteers
    public function getAbsentCountAttribute(): int
    {
        return $this->volunteers()->wherePivot('attendance_status', 'absent')->count();
    }

    // STEP 6: Get count of confirmed (not yet reviewed) volunteers
    public function getConfirmedCountAttribute(): int
    {
        return $this->volunteers()->wherePivot('attendance_status', 'confirmed')->count();
    }

    // STEP 7: Scope for events that need attendance review
    public function scopeNeedsAttendanceReview($query)
    {
        return $query->where('event_date', '<=', now()->subHours(24))
                     ->where('status', '!=', 'cancelled');
    }

    // STEP 1: Get current volunteer count (only active volunteers)
    public function getVolunteerCountAttribute(): int
    {
        return $this->volunteers()
            ->wherePivotIn('attendance_status', ['confirmed', 'pending_review', 'completed'])
            ->count();
    }

    // STEP 2: Check if event is full
    public function isFull(): bool
    {
        return $this->volunteerCount >= $this->max_volunteers;
    }

    // STEP 3: Check if event is open for joining
    public function canJoin(): bool
    {
        return $this->status === 'open' && !$this->isFull() && !$this->isPast();
    }

    // STEP 4: Check if event is in the past
    public function isPast(): bool
    {
        if (!$this->event_date) {
            return false;
        }
        return $this->event_date->isPast();
    }

    public static function hasLocationConflict(string $location, string $eventLocation, $start, $end, ?int $excludeId = null): bool
    {
        $query = static::where('location', $location)
            ->where('event_location', $eventLocation)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('end_time')
            ->where(function ($q) use ($start, $end) {
                $q->where('event_date', '<', $end)
                  ->where('end_time', '>', $start);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // STEP 4b: Get effective status (real-time, no DB write needed)
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }
        if ($this->isPast()) {
            return 'closed';
        }
        if ($this->isFull()) {
            return 'closed';
        }
        return $this->status;
    }

    // STEP 5: Check if event can be edited
    public function canEdit(): bool
    {
        return !$this->isPast();
    }

    // STEP 6: Check if event can be opened (capacity must be increased if full)
    public function canOpen(): bool
    {
        // Cannot open if cancelled
        if ($this->status === 'cancelled') {
            return false;
        }
        
        // Cannot open if past
        if ($this->isPast()) {
            return false;
        }
        
        // Cannot open if full - must increase capacity first
        if ($this->isFull()) {
            return false;
        }
        
        return true;
    }

    // STEP 7: Auto-determine status based on capacity
    public function updateStatusBasedOnCapacity(): void
    {
        if ($this->status !== 'closed' && $this->status !== 'cancelled') {
            if ($this->isFull()) {
                $this->update(['status' => 'closed']);
            } elseif ($this->status === 'closed') {
                // Only reopen if it was auto-closed due to capacity
                // Keep closed if manually closed
            }
        }
    }

    // STEP 8: Scope for active/upcoming events
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    // STEP 9: Scope for events that can be joined
    public function scopeJoinable($query)
    {
        return $query->where('status', 'open')
            ->where('event_date', '>', now())
            ->whereColumn('max_volunteers', '>', function ($sub) {
                $sub->selectRaw('COUNT(*)')
                    ->from('event_volunteer')
                    ->whereColumn('event_id', 'events.id');
            });
    }
}
