<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'device_id',
        'device_info',
        'activated_at',
        'last_seen_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function license()
    {
        return $this->belongsTo(License::class);
    }

    // Helper Methods
    public function updateLastSeen()
    {
        $this->last_seen_at = now();
        $this->save();
    }
}
