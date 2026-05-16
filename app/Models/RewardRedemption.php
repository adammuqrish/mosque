<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'used_for_event_id',
        'points_spent',
        'status',
        'redeemed_at',
        'claimed_at',
        'expires_at',
        'claim_code',
        'fulfillment_notes',
        'fulfilled_by',
        'fulfilled_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function usedForEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'used_for_event_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function isPriorityRegistration(): bool
    {
        return $this->reward && $this->reward->code === 'PRIORITY_EVENT_REG';
    }

    public function isCertificate(): bool
    {
        return $this->reward && $this->reward->code === 'APPRECIATION_CERT';
    }

    public function isConsumed(): bool
    {
        return $this->used_for_event_id !== null;
    }

    public function consumeForEvent(int $eventId): void
    {
        $this->update(['used_for_event_id' => $eventId]);
    }

    public function markAsFulfilled(int $adminId, ?string $notes = null): self
    {
        $this->update([
            'status' => 'claimed',
            'fulfilled_by' => $adminId,
            'fulfilled_at' => now(),
            'claimed_at' => now(),
            'fulfillment_notes' => $notes,
        ]);

        return $this;
    }

    public function markAsRejected(int $adminId, ?string $notes = null): self
    {
        $this->update([
            'status' => 'rejected',
            'fulfilled_by' => $adminId,
            'fulfilled_at' => now(),
            'fulfillment_notes' => $notes,
        ]);

        return $this;
    }
}
