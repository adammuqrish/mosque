<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeEarning extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'source_event_id',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function sourceEvent()
    {
        return $this->belongsTo(Event::class, 'source_event_id');
    }
}
