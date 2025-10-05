<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'license_key',
        'issued_at',
        'expires_at',
        'status',
        'meta',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // العلاقات
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function devices()
    {
        return $this->hasMany(LicensedDevice::class);
    }

    public function validationLogs()
    {
        return $this->hasMany(ValidationLog::class);
    }

    public function blockedLicense()
    {
        return $this->hasOne(BlockedLicense::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    // Helper Methods
    public static function generateLicenseKey($prefix = 'LIC')
    {
        return strtoupper($prefix . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
    }

    public function isValid()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }

        if ($this->blockedLicense) {
            return false;
        }

        return true;
    }

    public function canAddDevice()
    {
        $deviceLimit = $this->subscription->plan->device_limit;
        $currentDevices = $this->devices()->count();

        return $currentDevices < $deviceLimit;
    }
}
