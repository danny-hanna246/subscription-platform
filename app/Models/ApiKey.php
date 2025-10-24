<?php
// app/Models/ApiKey.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_name',
        'api_key',
        'secret_hash',
        'allowed_ips',
        'scopes',
        'status',
        'meta',
        'expires_at',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'scopes' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'secret_hash',
    ];

    // العلاقات
    public function accessLogs()
    {
        return $this->hasMany(ApiAccessLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    // Helper Methods
    public static function generateApiKey()
    {
        return 'sk_' . Str::random(40);
    }

    public static function generateSecret()
    {
        return Str::random(64);
    }

    public function hasScope($scope)
    {
        return in_array($scope, $this->scopes ?? []);
    }

    public function isIpAllowed($ip)
    {
        if (empty($this->allowed_ips)) {
            return true;
        }

        $allowedIps = explode(',', $this->allowed_ips);
        return in_array($ip, array_map('trim', $allowedIps));
    }

    /**
     * التحقق من صلاحية المفتاح
     */
    public function isValid()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * التحقق من قرب انتهاء الصلاحية
     */
    public function isExpiringSoon($days = 7)
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->between(now(), now()->addDays($days));
    }

    /**
     * تسجيل استخدام المفتاح
     */
    public function recordUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * الحصول على عدد الأيام المتبقية
     */
    public function daysUntilExpiry()
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }
}
