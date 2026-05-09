<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'category', 'source', 'description', 'donation_date'];

    protected $casts = [
        'donation_date' => 'datetime', // Bagitahu Laravel ni adalah Tarikh
    ];

    // Relationship: Belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
