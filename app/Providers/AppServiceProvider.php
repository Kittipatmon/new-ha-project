<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * รองรับทั้ง HTTP และ HTTPS โดยตรวจสอบจาก request จริง
     */
    public function boot(): void
    {
        $host = request()->getHost();
        $appUrl = config('app.url', '');

        $isHttps = request()->isSecure()
            || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || str_starts_with($appUrl, 'https://');

        $isProductionHost = str_contains($host, 'appkumwell.com')
            || str_contains($appUrl, 'appkumwell.com')
            || $this->app->environment('production');

        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1'])
            && !str_starts_with($appUrl, 'https://');

        if (($isProductionHost || $isHttps) && !$isLocalhost) {
            URL::forceScheme('https');
        }
    }
}
