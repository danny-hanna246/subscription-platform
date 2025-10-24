<?php
// app/Http/Middleware/LogApiAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiAccessLog;
use Illuminate\Support\Facades\Log;

class LogApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // بالميلي ثانية

        try {
            $apiClient = $request->get('api_client');

            ApiAccessLog::logAccess([
                'api_key_id' => $apiClient ? $apiClient->id : null,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
                'response_time' => round($responseTime, 2),
                'request_data' => $this->sanitizeData($request->except(['password', 'api_key', 'secret'])),
                'response_data' => $this->sanitizeData($response->getData()),
                'error_message' => $response->getStatusCode() >= 400 ? $response->getContent() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API access', [
                'error' => $e->getMessage(),
                'endpoint' => $request->path(),
            ]);
        }

        return $response;
    }

    /**
     * تنظيف البيانات الحساسة
     */
    private function sanitizeData($data)
    {
        if (!is_array($data)) {
            return null;
        }

        $sanitized = $data;

        // إزالة الحقول الحساسة
        $sensitiveFields = ['password', 'api_key', 'secret', 'token'];

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '***REDACTED***';
            }
        }

        return $sanitized;
    }
}
