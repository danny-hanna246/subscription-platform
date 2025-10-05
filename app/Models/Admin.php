<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'last_login_at',
        'two_factor_enabled',
        'meta',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Override getAuthPassword
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // العلاقات
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'performed_by');
    }

    public function blockedLicenses()
    {
        return $this->hasMany(BlockedLicense::class, 'revoked_by');
    }

    public function prorations()
    {
        return $this->hasMany(SubscriptionProration::class, 'performed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSuperadmin($query)
    {
        return $query->where('role', 'superadmin');
    }

    // Helper Methods
    public function isSuperadmin()
    {
        return $this->role === 'superadmin';
    }

    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}
