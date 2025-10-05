<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionProration extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'type',
        'amount',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'performed_by');
    }
}
