<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHrOrAdmin
{
    /**
     * Handle an incoming request.
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

        // อนุญาตให้หัวหน้าแผนก (Manager) เข้าดูรายละเอียดผู้สมัคร (ดูประวัติ) และตัดสินใจพิจารณาได้
        $isApplicantReview = $request->routeIs('backend.recruitment.applications.show') 
            || $request->routeIs('backend.recruitment.applications.update-status');
        if ($isApplicantReview && $user && ($user->canViewDeptCandidates() || $user->canAccessBackend())) {
            return $next($request);
        }

        // จะเข้าหลังบ้านได้ ต้องให้ มีการกำหนดสิทธิ์ให้เป็น admin หรือ editor เท่านั้น
        if (!$user->canAccessBackend()) {
            $msg = 'คุณไม่มีสิทธิ์เข้าถึงระบบหลังบ้าน (เฉพาะสิทธิ์ ADMIN และ EDITOR เท่านั้น)';

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => $msg
                ], 403);
            }

            $previousUrl = url()->previous();
            if ($previousUrl && $previousUrl !== $request->fullUrl()) {
                return redirect()->to($previousUrl)->with('error', $msg)->with('swal_error', $msg);
            }

            abort(403, $msg);
        }

        return $next($request);
    }
}
