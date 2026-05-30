<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = ['requested_by', 'type', 'fund_purpose', 'amount', 'purpose', 'status', 'approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'rejection_reason', 'maker_checked_by', 'maker_checked_at'];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'maker_checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function makerChecker()
    {
        return $this->belongsTo(User::class, 'maker_checked_by');
    }

    public function documents()
    {
        return $this->hasMany(WithdrawalDocument::class);
    }

    public function needsMakerChecker(): bool
    {
        return $this->amount > 1000;
    }

    public function isFullyApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isMakerChecked(): bool
    {
        return $this->status === 'maker_checked';
    }
}
