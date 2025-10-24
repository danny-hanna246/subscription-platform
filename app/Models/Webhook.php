<?php
// app/Models/Webhook.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = [
        'api_key_id',
        'url',
        'events',
        'secret',
        'status',
        'last_triggered_at',
        'failed_attempts',
    ];

    protected $casts = [
        'events' => 'array',
        'last_triggered_at' => 'datetime',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function hasEvent($event)
    {
        return in_array($event, $this->events ?? []);
    }
}
