<?php

namespace App\Providers;

use App\Transports\ResendTransport;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->afterResolving('mail.manager', function ($mailManager) {
            $mailManager->extend('resend', function ($config) {
                return new ResendTransport(
                    new \GuzzleHttp\Client(),
                    $config['api_key']
                );
            });
        });
    }
}
