<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'file_path',
        'file_name',
        'uploaded_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    // Polymorphic relationship
    public function entity()
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function getFullPath()
    {
        return storage_path('app/' . $this->file_path);
    }

    public function getUrl()
    {
        return url('storage/' . $this->file_path);
    }
}
