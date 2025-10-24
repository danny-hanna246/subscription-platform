<?php
// app/Models/ApiAccessLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key_id',
        'endpoint',
        'method',
        'ip_address',
        'user_agent',
        'status_code',
        'response_time',
        'request_data',
        'response_data',
        'error_message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'response_time' => 'float',
        'created_at' => 'datetime',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * تسجيل محاولة وصول
     */
    public static function logAccess(array $data)
    {
        return self::create($data);
    }

    /**
     * الحصول على محاولات الوصول الفاشلة
     */
    public static function getFailedAttempts($apiKeyId = null, $hours = 24)
    {
        $query = self::where('status_code', '>=', 400)
            ->where('created_at', '>=', now()->subHours($hours));

        if ($apiKeyId) {
            $query->where('api_key_id', $apiKeyId);
        }

        return $query->count();
    }
}
