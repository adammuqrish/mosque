<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'age',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationship: One User has One Volunteer Profile
    public function volunteerProfile()
    {
        return $this->hasOne(VolunteerProfile::class);
    }

    // Relationship: One User (Staff) has Many Donations
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    // Relationship: Requests made by this user (Admin)
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'requested_by');
    }

    // Relationship: Requests approved by this user (Treasurer)
    public function approvedWithdrawals()
    {
        return $this->hasMany(WithdrawalRequest::class, 'approved_by');
    }

    // Relationship: Many-to-Many with Events (User joins events)
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_volunteer')
            ->withPivot('status', 'joined_at');
    }
}
