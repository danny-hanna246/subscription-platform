<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من تسجيل الدخول
        if (!Auth::guard('admin')->check()) {
            // إذا كان الطلب من API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'Please login as admin to access this resource.'
                ], 401);
            }

            // إعادة التوجيه لصفحة تسجيل الدخول
            return redirect()->route('admin.login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // الحصول على المدير
        $admin = Auth::guard('admin')->user();

        // التحقق من أن الحساب نشط
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Account suspended.',
                    'error' => 'Your account has been suspended. Please contact support.'
                ], 403);
            }

            return redirect()->route('admin.login')
                ->with('error', 'حسابك موقوف. يرجى التواصل مع الإدارة.');
        }

        // السماح بالمتابعة
        return $next($request);
    }
}
