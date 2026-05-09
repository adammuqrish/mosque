<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventVolunteer extends Model
{
    use HasFactory;

    protected $table = 'event_volunteer'; // Explicitly state table name

    protected $fillable = ['event_id', 'user_id', 'status'];
}
