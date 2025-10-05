<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'source',
        'event_type',
        'payload',
        'received_at',
        'processed',
        'processed_at',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // Scopes
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    // Helper Methods
    public function markAsProcessed()
    {
        $this->processed = true;
        $this->processed_at = now();
        $this->save();
    }

    public function markAsFailed($error)
    {
        $this->error = $error;
        $this->processed_at = now();
        $this->save();
    }
}
