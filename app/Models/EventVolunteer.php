<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventVolunteer extends Model
{
    use HasFactory;

    protected $table = 'event_volunteer'; // Explicitly state table name

    public $timestamps = false;

    protected $fillable = ['event_id', 'user_id', 'status', 'attendance_status', 'absence_reason', 'points_awarded', 'points_earned'];

    protected $casts = [
        'joined_at' => 'datetime',
        'points_awarded' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
