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
        'location',
        'max_volunteers',
        'required_skills',

        // BARU NI WAJIB ADA:
        'required_hobbies',
        'required_languages',
        'event_location',
        'location_radius',
        'health_requirement'
    ];

    // Automatically cast JSON fields to Array
    protected $casts = [
        'required_skills' => 'array',
        'required_hobbies' => 'array',
        'required_languages' => 'array',
        'event_date' => 'datetime',
    ];

    // Relationship: Many-to-Many with Volunteers (Users)
    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'event_volunteer')
            ->withPivot('status', 'joined_at');
    }
}
