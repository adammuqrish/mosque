<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skills',
        'availability',
        // Baru
        'hobbies',
        'interests',
        'languages',
        'experience',
        'location',
        'health_status',
        'long_term_availability'
    ];

    // Automatically cast JSON fields to Array
    protected $casts = [
        'skills' => 'array',
        'availability' => 'array',
        'hobbies' => 'array',
        'interests' => 'array',
        'languages' => 'array',
    ];

    // Relationship: Belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
