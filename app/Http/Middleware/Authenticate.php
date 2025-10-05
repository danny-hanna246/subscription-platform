<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // لو كان الحارس المطلوب admin نعيد للتسمية admin.login
        if (in_array('admin', $guards)) {
            return redirect()->guest(route('admin.login'));
        }

        // الافتراضي
        return redirect()->guest(route('login'));
    }
}
