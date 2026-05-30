<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DonationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'treasurer']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function confirm(User $user, Donation $donation): bool
    {
        return $user->role === 'treasurer' && $donation->user_id !== $user->id;
    }

    public function dispute(User $user, Donation $donation): bool
    {
        return $user->role === 'treasurer' && $donation->user_id !== $user->id;
    }
}
