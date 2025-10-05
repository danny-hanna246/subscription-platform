<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next, $scope = null): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is required',
            ], 401);
        }

        $key = ApiKey::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$key) {
            return response()->json([
                'error' => 'Invalid API key',
            ], 401);
        }

        // التحقق من IP إن كان محدداً
        if (!$key->isIpAllowed($request->ip())) {
            return response()->json([
                'error' => 'IP address not allowed',
            ], 403);
        }

        // التحقق من الصلاحيات
        if ($scope && !$key->hasScope($scope)) {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        $request->merge(['api_client' => $key]);

        return $next($request);
    }
}
