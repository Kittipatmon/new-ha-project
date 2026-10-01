<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('welcome', ['login' => 1]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if (class_exists(\App\Services\AuditLogService::class) && $user) {
            try {
                \App\Services\AuditLogService::log(
                    action: 'login',
                    description: "เข้าสู่ระบบสำเร็จ (" . ($user->fullname ?: $user->name) . ")",
                    module: 'auth',
                    moduleName: 'ระบบยืนยันตัวตน',
                    user: $user
                );
            } catch (\Throwable $e) {
                logger()->error('AuditLog login error: ' . $e->getMessage());
            }
        }

        $intended = $request->session()->get('url.intended');
        if ($intended && (str_contains($intended, '/data') || str_contains($intended, '/api/') || str_ends_with($intended, '.json'))) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended(route('welcome', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (class_exists(\App\Services\AuditLogService::class) && $user) {
            try {
                \App\Services\AuditLogService::log(
                    action: 'logout',
                    description: "ออกจากระบบ (" . ($user->fullname ?: $user->name) . ")",
                    module: 'auth',
                    moduleName: 'ระบบยืนยันตัวตน',
                    user: $user
                );
            } catch (\Throwable $e) {
                logger()->error('AuditLog logout error: ' . $e->getMessage());
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
