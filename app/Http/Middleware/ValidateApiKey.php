<?php
// app/Http/Middleware/ValidateApiKey.php (المحسّن)

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Exceptions\ApiKeyInvalidException;
use App\Exceptions\ApiKeyExpiredException;
use App\Exceptions\IpNotAllowedException;
use App\Exceptions\InsufficientPermissionsException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next, $scope = null): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            Log::warning('API access attempt without key', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'API_KEY_REQUIRED',
                'message' => 'Please provide X-API-Key header'
            ], 401);
        }

        $key = ApiKey::where('api_key', $apiKey)->first();

        if (!$key) {
            Log::warning('Invalid API key attempt', [
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            throw new ApiKeyInvalidException();
        }

        // التحقق من حالة المفتاح
        if ($key->status !== 'active') {
            return response()->json([
                'error' => 'API_KEY_INACTIVE',
                'message' => 'This API key is not active'
            ], 401);
        }

        // التحقق من انتهاء الصلاحية
        if ($key->expires_at && $key->expires_at < now()) {
            Log::warning('Expired API key used', [
                'api_key_id' => $key->id,
                'client_name' => $key->client_name,
                'expired_at' => $key->expires_at->toDateTimeString(),
            ]);

            throw new ApiKeyExpiredException();
        }

        // التحقق من IP
        if (!$key->isIpAllowed($request->ip())) {
            Log::warning('API access from unauthorized IP', [
                'client_name' => $key->client_name,
                'ip' => $request->ip(),
                'allowed_ips' => $key->allowed_ips
            ]);

            throw new IpNotAllowedException($request->ip());
        }

        // التحقق من الصلاحيات
        if ($scope && !$key->hasScope($scope)) {
            Log::warning('Insufficient API permissions', [
                'client_name' => $key->client_name,
                'required_scope' => $scope,
                'available_scopes' => $key->scopes,
            ]);

            throw new InsufficientPermissionsException($scope);
        }

        // تسجيل الاستخدام
        $key->recordUsage();

        // إضافة معلومات API Key للـ request
        $request->merge(['api_client' => $key]);

        return $next($request);
    }
}
