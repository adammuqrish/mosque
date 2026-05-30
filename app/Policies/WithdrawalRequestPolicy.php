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

    public function approve(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        if ($user->role !== 'treasurer') return false;
        if ($withdrawalRequest->requested_by === $user->id) return false;
        if ($withdrawalRequest->status === 'maker_checked' && $withdrawalRequest->maker_checked_by === $user->id) return false;
        return true;
    }

    public function reject(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->role === 'treasurer' && $withdrawalRequest->requested_by !== $user->id;
    }
}
