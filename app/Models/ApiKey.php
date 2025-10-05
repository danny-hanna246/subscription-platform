<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    ];

    protected $casts = [
        'scopes' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'secret_hash',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper Methods
    public static function generateApiKey()
    {
        return 'sk_' . Str::random(32);
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
}
