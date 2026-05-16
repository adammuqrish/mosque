<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Donation;
use App\Models\WithdrawalRequest;
use App\Policies\EventPolicy;
use App\Policies\DonationPolicy;
use App\Policies\WithdrawalRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class,
        Donation::class => DonationPolicy::class,
        WithdrawalRequest::class => WithdrawalRequestPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
