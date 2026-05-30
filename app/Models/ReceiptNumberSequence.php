<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptNumberSequence extends Model
{
    protected $fillable = ['prefix', 'year', 'last_number'];

    protected $casts = [
        'last_number' => 'integer',
    ];
}
