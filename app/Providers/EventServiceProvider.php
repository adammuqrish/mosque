<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\EventVolunteer;
use App\Observers\EventObserver;
use App\Observers\EventVolunteerObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event as EventFacade;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot()
    {
        Event::observe(EventObserver::class);
        EventVolunteer::observe(EventVolunteerObserver::class);
    }
}
