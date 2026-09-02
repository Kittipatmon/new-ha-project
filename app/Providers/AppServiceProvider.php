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
        if ($this->app->environment('production')) {
            // ตรวจสอบว่า request มาจาก HTTPS (ผ่าน proxy/CDN) หรือไม่
            // แทนที่จะ forceScheme('https') ตลอด
            URL::macro('currentScheme', function () {
                return request()->isSecure() ? 'https' : 'http';
            });

            // Force HTTPS เฉพาะเมื่อ request จริงเป็น HTTPS เท่านั้น
            if (
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            ) {
                URL::forceScheme('https');
            }
        }
    }
}
