<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    public static function logAction($entityType, $entityId, $action, $meta = null)
    {
        try {
            AuditLog::create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'action' => $action,
                'performed_by' => auth()->check() ? auth()->id() : null,
                'performed_via' => request()->is('api/*') ? 'api' : 'web',
                'meta' => $meta,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create audit log: ' . $e->getMessage());
        }
    }

    public static function logApiRequest($endpoint, $status, $duration = null)
    {
        Log::channel('api')->info('API Request', [
            'endpoint' => $endpoint,
            'method' => request()->method(),
            'status' => $status,
            'duration' => $duration,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public static function logSecurityEvent($event, $details = [])
    {
        Log::channel('security')->warning($event, array_merge($details, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]));
    }
}
