<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIctOrAdmin
{
    /**
     * Handle an incoming request for ICT personnel only.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user || !method_exists($user, 'canAccessDatabaseBackups') || !$user->canAccessDatabaseBackups()) {
            $msg = 'คุณไม่มีสิทธิ์เข้าถึงระบบสำรองฐานข้อมูล (เฉพาะผู้ดูแลระบบฝ่ายเทคโนโลยีสารสนเทศ - ICT เท่านั้น)';

            if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Forbidden',
                    'message' => $msg
                ], 403);
            }

            abort(403, $msg);
        }

        return $next($request);
    }
}
