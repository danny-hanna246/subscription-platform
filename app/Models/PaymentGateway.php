<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'config',
        'is_active',
        'is_test_mode',
        'supported_currencies',
        'settings',
    ];

    protected $casts = [
        'config' => 'encrypted:array',
        'supported_currencies' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInternational($query)
    {
        return $query->where('type', 'international');
    }

    public function scopeLocal($query)
    {
        return $query->where('type', 'local');
    }

    public function supportsCurrency($currency)
    {
        if (empty($this->supported_currencies)) {
            return true;
        }

        return in_array($currency, $this->supported_currencies);
    }
}
