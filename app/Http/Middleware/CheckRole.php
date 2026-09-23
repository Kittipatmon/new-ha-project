<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request based on user role.
     *
     * Roles:
     * - admin: Full access (All permissions)
     * - editor: View, create, edit (No delete, no user management)
     * - viewer: View only
     *
     * Usage in routes:
     * - middleware('role:admin')
     * - middleware('role:admin,editor')
     * - middleware('role:admin,editor,viewer')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = strtolower($user->role_normalized ?? 'viewer');

        // Normalize expected roles
        $allowedRoles = array_map('strtolower', $roles);

        // If no specific roles supplied, ensure at least authenticated
        if (empty($allowedRoles)) {
            return $next($request);
        }

        // ADMIN always has full access to any role protected route
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Check if user's role is in the allowed list
        if (!in_array($userRole, $allowedRoles)) {
            $msg = 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (สิทธิ์ปัจจุบันของคุณ: ' . strtoupper($userRole) . ')';

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
