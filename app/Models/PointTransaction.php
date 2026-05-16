<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'points',
        'balance_after',
        'reason',
        'source_type',
        'source_id',
        'admin_id',
        'admin_notes',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    public function scopeAdjusted($query)
    {
        return $query->whereIn('type', ['adjusted', 'revoked']);
    }

    public function isPositive(): bool
    {
        return $this->points > 0;
    }
}
