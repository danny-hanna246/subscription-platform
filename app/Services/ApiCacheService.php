<?php
// app/Services/ApiCacheService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ApiCacheService
{
    /**
     * مدة الـ Cache الافتراضية (بالدقائق)
     */
    protected $defaultTTL = 60;

    /**
     * Cache المنتجات
     */
    public function cacheProducts()
    {
        return Cache::remember('api:products', $this->defaultTTL, function () {
            return \App\Models\Product::with(['plans' => function ($query) {
                $query->where('active', true);
            }])->get();
        });
    }

    /**
     * Cache الخطط
     */
    public function cachePlans($productId = null)
    {
        $key = $productId ? "api:plans:product:{$productId}" : 'api:plans:all';

        return Cache::remember($key, $this->defaultTTL, function () use ($productId) {
            $query = \App\Models\Plan::with('product')->where('active', true);

            if ($productId) {
                $query->where('product_id', $productId);
            }

            return $query->get();
        });
    }

    /**
     * مسح Cache المنتجات
     */
    public function clearProductsCache()
    {
        Cache::forget('api:products');
        Cache::forget('api:plans:all');

        // مسح cache الخطط لكل منتج
        $productIds = \App\Models\Product::pluck('id');
        foreach ($productIds as $id) {
            Cache::forget("api:plans:product:{$id}");
        }
    }

    /**
     * Cache العميل
     */
    public function cacheCustomer($email)
    {
        $key = "api:customer:{$email}";

        return Cache::remember($key, 30, function () use ($email) {
            return \App\Models\Customer::where('email', $email)->first();
        });
    }

    /**
     * مسح cache العميل
     */
    public function clearCustomerCache($email)
    {
        Cache::forget("api:customer:{$email}");
    }
}
