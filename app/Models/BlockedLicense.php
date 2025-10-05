<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'reason',
        'revoked_by',
        'revoked_at',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'revoked_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'revoked_by');
    }

    // Helper Methods
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
}
