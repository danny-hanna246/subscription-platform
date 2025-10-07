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
            AuditLogService::logSecurityEvent('Missing API Key', [
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'error' => 'API key is required',
            ], 401);
        }

        $key = ApiKey::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$key) {
            AuditLogService::logSecurityEvent('Invalid API Key', [
                'api_key' => substr($apiKey, 0, 10) . '...',
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'error' => 'Invalid API key',
            ], 401);
        }

        // التحقق من IP
        if (!$key->isIpAllowed($request->ip())) {
            AuditLogService::logSecurityEvent('Unauthorized IP', [
                'api_key_id' => $key->id,
                'client_name' => $key->client_name,
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'error' => 'IP address not allowed',
            ], 403);
        }

        // التحقق من الصلاحيات
        if ($scope && !$key->hasScope($scope)) {
            AuditLogService::logSecurityEvent('Insufficient Permissions', [
                'api_key_id' => $key->id,
                'required_scope' => $scope,
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        $request->merge(['api_client' => $key]);

        $startTime = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        AuditLogService::logApiRequest(
            $request->path(),
            $response->status(),
            $duration
        );

        return $response;
    }
}
