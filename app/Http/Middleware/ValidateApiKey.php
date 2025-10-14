<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next, $scope = null): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is required',
                'message' => 'Please provide X-API-Key header'
            ], 401);
        }

        $key = ApiKey::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$key) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or inactive'
            ], 401);
        }

        // التحقق من IP
        if (!$key->isIpAllowed($request->ip())) {
            return response()->json([
                'error' => 'IP address not allowed',
                'message' => 'Your IP address is not authorized'
            ], 403);
        }

        // التحقق من الصلاحيات (إذا تم تحديدها)
        if ($scope && !$key->hasScope($scope)) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => "This API key doesn't have '{$scope}' permission"
            ], 403);
        }

        // إضافة معلومات API Key للـ request
        $request->merge(['api_client' => $key]);

        return $next($request);
    }
}
