<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // منتج 1: نظام ERP
        $erpProduct = Product::create([
            'name' => 'ERP System',
            'slug' => 'erp-system',
            'description' => 'Complete ERP solution for managing your business',
            'metadata' => [
                'version' => '1.0',
                'type' => 'desktop',
            ],
        ]);

        // خطط للمنتج الأول
        Plan::create([
            'product_id' => $erpProduct->id,
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'price' => 10.00,
            'currency' => 'USD',
            'duration_days' => 30,
            'user_limit' => 1,
            'device_limit' => 1,
            'features' => ['Basic Support', 'Core Features'],
            'active' => true,
        ]);

        Plan::create([
            'product_id' => $erpProduct->id,
            'name' => 'Professional Plan',
            'slug' => 'professional-plan',
            'price' => 25.00,
            'currency' => 'USD',
            'duration_days' => 30,
            'user_limit' => 5,
            'device_limit' => 5,
            'features' => ['24/7 Support', 'Advanced Features', 'Priority Updates'],
            'active' => true,
        ]);

        // منتج 2: تطبيق موبايل
        $mobileProduct = Product::create([
            'name' => 'Mobile Inventory App',
            'slug' => 'mobile-inventory-app',
            'description' => 'Manage your inventory on the go',
            'metadata' => [
                'version' => '2.0',
                'type' => 'mobile',
            ],
        ]);

        Plan::create([
            'product_id' => $mobileProduct->id,
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 5.00,
            'currency' => 'USD',
            'duration_days' => 30,
            'user_limit' => 1,
            'device_limit' => 2,
            'features' => ['Basic Inventory', 'Email Support'],
            'active' => true,
        ]);
    }
}
