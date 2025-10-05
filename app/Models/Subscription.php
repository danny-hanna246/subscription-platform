<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_request_id',
        'customer_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'gateway_transaction_id',
        'meta',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // العلاقات
    public function subscriptionRequest()
    {
        return $this->belongsTo(SubscriptionRequest::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function license()
    {
        return $this->hasOne(License::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function prorations()
    {
        return $this->hasMany(SubscriptionProration::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '<=', Carbon::now()->addDays($days));
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === 'active' &&
            Carbon::now()->between($this->starts_at, $this->ends_at);
    }

    public function isExpired()
    {
        return $this->status === 'expired' || Carbon::now()->greaterThan($this->ends_at);
    }

    public function remainingDays()
    {
        return Carbon::now()->diffInDays($this->ends_at, false);
    }
}
