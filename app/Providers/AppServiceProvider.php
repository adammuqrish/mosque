<?php

namespace App\Providers;

use App\Transports\BrevoTransport;
use App\Transports\ResendTransport;
use Illuminate\Support\Facades\Blade;
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

            $mailManager->extend('brevo', function ($config) {
                return new BrevoTransport(
                    new \GuzzleHttp\Client(),
                    $config['api_key']
                );
            });
        });

        Blade::directive('safe_svg', function ($expression) {
            return "<?php echo App\\Providers\\AppServiceProvider::safeSvg($expression); ?>";
        });
    }

    public static function safeSvg($svg)
    {
        if (empty($svg)) return '';
        $svg = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg);
        $svg = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg);
        $svg = preg_replace('/<foreignObject\b[^>]*>(.*?)<\/foreignObject>/is', '', $svg);
        $svg = preg_replace('/<\/?(?:object|embed|iframe|frame|meta|link|style)[^>]*>/i', '', $svg);
        return $svg;
    }
}
