<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'performed_by',
        'performed_via',
        'meta',
        'ip_address',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    // العلاقات
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'performed_by');
    }

    // Helper Methods
    public static function log($entityType, $entityId, $action, $meta = null)
    {
        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'performed_by' => auth('admin')->id(),
            'performed_via' => request()->is('api/*') ? 'api' : 'web',
            'meta' => $meta,
            'ip_address' => request()->ip(),
        ]);
    }
}
