<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // إذا لم يتم تحديد guards، استخدم null (default)
        $guards = empty($guards) ? [null] : $guards;

        // التحقق من كل guard
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // إذا المستخدم مسجل دخول، استمر
                return $next($request);
            }
        }

        // المستخدم غير مسجل دخول
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // إعادة التوجيه حسب الـ guard
        if (in_array('admin', $guards)) {
            return redirect()->guest(route('admin.login'));
        }

        // الافتراضي للـ users
        return redirect()->guest(route('login'));
    }
}
