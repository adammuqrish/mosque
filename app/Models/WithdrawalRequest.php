<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = ['requested_by', 'amount', 'purpose', 'status', 'approved_by', 'approved_at'];

    protected $casts = [
        'approved_at' => 'datetime', //supaya boleh guna format('d M Y') kat blade
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Who made the request?
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Relationship: Who approved it?
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
