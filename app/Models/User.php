<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'age',
        'address',
        'avatar',
        'referred_code',
        'referred_by',
        'hide_from_leaderboard',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hide_from_leaderboard' => 'boolean',
    ];

    public function volunteerProfile()
    {
        return $this->hasOne(VolunteerProfile::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'requested_by');
    }

    public function approvedWithdrawals()
    {
        return $this->hasMany(WithdrawalRequest::class, 'approved_by');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_volunteer')
            ->withPivot('status', 'joined_at', 'attendance_status', 'points_awarded', 'points_earned');
    }

    // Gamification Relationships
    public function memberPoints()
    {
        return $this->hasOne(MemberPoints::class);
    }

    public function badgeEarnings()
    {
        return $this->hasMany(BadgeEarning::class);
    }

    public function earnedBadges()
    {
        return $this->belongsToMany(Badge::class, 'badge_earnings')
            ->withPivot('earned_at', 'source_event_id')
            ->withTimestamps();
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    // Accessors
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->name ?? '';
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            if (strlen($initials) >= 2) break;
            $initials .= strtoupper(substr($part, 0, 1));
        }
        return $initials ?: 'U';
    }

    public function getGamificationStatsAttribute(): array
    {
        $points = $this->memberPoints;
        $tier = $points ? $points->tier : null;
        
        return [
            'total_points' => $points ? $points->total_points : 0,
            'available_points' => $points ? $points->available_points : 0,
            'redeemed_points' => $points ? $points->redeemed_points : 0,
            'tier' => $tier ? $tier->tier : null,
            'tier_name' => $tier ? $tier->name : null,
            'tier_icon' => $tier ? $tier->icon_svg : null,
            'current_streak' => $points ? $points->current_streak : 0,
            'longest_streak' => $points ? $points->longest_streak : 0,
            'badge_count' => $this->badgeEarnings()->count(),
            'tier_progress' => $points ? $points->progress_to_next_tier : null,
        ];
    }

    // Role Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTreasurer(): bool
    {
        return $this->role === 'treasurer';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isVolunteer(): bool
    {
        return $this->role === 'member';
    }

    public function hasPermission(string $permission): bool
    {
        $roleConfig = config("roles.roles.{$this->role}.permissions", []);
        return in_array($permission, $roleConfig);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
