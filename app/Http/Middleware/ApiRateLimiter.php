<?php
// app/Http/Middleware/ApiRateLimiter.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * عدد المحاولات المسموح بها
     */
    protected $maxAttempts = [
        'default' => 60,        // 60 طلب في الدقيقة
        'integration' => 60,    // 60 طلب في الدقيقة
        'admin' => 100,         // 100 طلب في الدقيقة
        'public' => 10,         // 10 طلبات في الدقيقة
    ];

    /**
     * مدة النافذة بالدقائق
     */
    protected $decayMinutes = 1;

    public function handle(Request $request, Closure $next, $scope = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->maxAttempts[$scope] ?? $this->maxAttempts['default'];

        // الحصول على عدد المحاولات الحالية
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            // تسجيل تجاوز الحد
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
                'attempts' => $attempts,
                'limit' => $maxAttempts,
            ]);

            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'You have exceeded the rate limit. Please try again later.',
                'retry_after' => $this->decayMinutes * 60,
                'limit' => $maxAttempts,
                'remaining' => 0,
            ], 429);
        }

        // زيادة العداد
        if ($attempts == 0) {
            Cache::put($key, 1, now()->addMinutes($this->decayMinutes));
        } else {
            Cache::increment($key);
        }

        $response = $next($request);

        // إضافة Headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts - 1));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($this->decayMinutes)->timestamp);

        return $response;
    }

    /**
     * إنشاء مفتاح فريد للطلب
     */
    protected function resolveRequestSignature(Request $request)
    {
        $apiClient = $request->get('api_client');

        if ($apiClient) {
            // إذا كان هناك API Key، استخدم ID المفتاح
            return 'api_rate_limit:key:' . $apiClient->id;
        }

        // وإلا استخدم IP
        return 'api_rate_limit:ip:' . sha1($request->ip());
    }
}
