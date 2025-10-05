<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'license_key_attempted',
        'license_id',
        'status',
        'ip_address',
        'user_agent',
        'device_id',
        'response_code',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    // العلاقات
    public function license()
    {
        return $this->belongsTo(License::class);
    }

    // Helper Methods
    public static function logValidation($data)
    {
        return self::create([
            'license_key_attempted' => $data['license_key'] ?? null,
            'license_id' => $data['license_id'] ?? null,
            'status' => $data['status'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_id' => $data['device_id'] ?? null,
            'response_code' => $data['response_code'] ?? 200,
            'meta' => $data['meta'] ?? null,
        ]);
    }
}
