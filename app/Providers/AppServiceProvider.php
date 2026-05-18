<?php

namespace App\Providers;

use App\Transports\ResendTransport;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->bound('swift.transport')) {
            $this->app->make('swift.transport')->extend('resend', function ($config) {
                return new ResendTransport(
                    new Client(),
                    $config['api_key'] ?? config('services.resend.api_key')
                );
            });
        }
    }
}
