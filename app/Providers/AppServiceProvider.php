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

        // Auto-heal missing interview_id / application_id on interview_evaluations table if migration was not executed
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('interview_evaluations')) {
                $needsInterviewId = !\Illuminate\Support\Facades\Schema::hasColumn('interview_evaluations', 'interview_id');
                $needsAppId = !\Illuminate\Support\Facades\Schema::hasColumn('interview_evaluations', 'application_id');
                if ($needsInterviewId || $needsAppId) {
                    \Illuminate\Support\Facades\Schema::table('interview_evaluations', function (\Illuminate\Database\Schema\Blueprint $table) use ($needsInterviewId, $needsAppId) {
                        if ($needsInterviewId) {
                            $table->unsignedBigInteger('interview_id')->nullable()->after('user_id')->index();
                        }
                        if ($needsAppId) {
                            $table->unsignedBigInteger('application_id')->nullable()->after('interview_id')->index();
                        }
                    });
                }
            }
        } catch (\Throwable $e) {
            // Silently continue if permissions or lock
        }

        // Automatic Audit Logging for Authentication Events (Login, Logout, Failed Login)
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            try {
                $user = $event->user;
                if ($user && class_exists(\App\Services\AuditLogService::class)) {
                    $name = $user->fullname ?: ($user->firstname ? trim($user->firstname . ' ' . ($user->lastname ?? '')) : null) ?: $user->name ?: 'ผู้ใช้งาน';
                    $role = $user->role ?: ($user->hr_role ?: '-');
                    \App\Services\AuditLogService::log(
                        action: 'login',
                        description: "เข้าสู่ระบบสำเร็จ ({$name}) [สิทธิ์: {$role}]",
                        module: 'auth',
                        moduleName: 'ระบบยืนยันตัวตน',
                        user: $user
                    );
                }
            } catch (\Throwable $e) {
                logger()->error('AuditLog Login event error: ' . $e->getMessage());
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function (\Illuminate\Auth\Events\Logout $event) {
            try {
                $user = $event->user;
                if ($user && class_exists(\App\Services\AuditLogService::class)) {
                    $name = $user->fullname ?: ($user->firstname ? trim($user->firstname . ' ' . ($user->lastname ?? '')) : null) ?: $user->name ?: 'ผู้ใช้งาน';
                    \App\Services\AuditLogService::log(
                        action: 'logout',
                        description: "ออกจากระบบ ({$name})",
                        module: 'auth',
                        moduleName: 'ระบบยืนยันตัวตน',
                        user: $user
                    );
                }
            } catch (\Throwable $e) {
                logger()->error('AuditLog Logout event error: ' . $e->getMessage());
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function (\Illuminate\Auth\Events\Failed $event) {
            try {
                if (class_exists(\App\Services\AuditLogService::class)) {
                    $identifier = $event->credentials['username'] ?? $event->credentials['email'] ?? $event->credentials['emp_code'] ?? 'ไม่ระบุ';
                    \App\Services\AuditLogService::log(
                        action: 'warning',
                        description: "พยายามเข้าสู่ระบบไม่สำเร็จ (รหัสผ่านหรือข้อมูลไม่ถูกต้อง): บัญชี {$identifier}",
                        module: 'auth',
                        moduleName: 'ระบบยืนยันตัวตน',
                        user: $event->user
                    );
                }
            } catch (\Throwable $e) {
                logger()->error('AuditLog Failed login event error: ' . $e->getMessage());
            }
        });
    }
}
