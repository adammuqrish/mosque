<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class WithdrawalRequestPolicy
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

    public function approve(User $user): bool
    {
        return $user->role === 'treasurer';
    }

    public function reject(User $user): bool
    {
        return $user->role === 'treasurer';
    }
}
