<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionRequestController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\LicenseValidationController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\IntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes - منصة إدارة الاشتراكات
|--------------------------------------------------------------------------
*/

// =====================================================
// 1. Public Route - License Validation (بدون حماية)
// =====================================================
// للتطبيقات الخارجية للتحقق من التراخيص
// لا يحتاج API Key
Route::post('/v1/licenses/validate', [LicenseValidationController::class, 'validate'])
    ->middleware('throttle:120,1'); // 120 طلب في الدقيقة

// =====================================================
// 2. Integration API (محمي بـ API Key)
// =====================================================
// للمواقع الخارجية لإنشاء عملاء واشتراكات
Route::middleware(['api.key:integration', 'throttle:60,1'])
    ->prefix('integration/v1')
    ->group(function () {

        // Products & Plans (للعرض فقط)
        Route::get('/products', [IntegrationController::class, 'getProducts']);
        Route::get('/plans', [IntegrationController::class, 'getPlans']);
        Route::get('/plans/{id}', [IntegrationController::class, 'getPlan']);

        // Customers
        Route::post('/customers', [IntegrationController::class, 'createCustomer']);
        Route::get('/customers/{email}', [IntegrationController::class, 'getCustomer']);
        Route::get('/customers/{email}/subscriptions', [IntegrationController::class, 'getCustomerSubscriptions']);

        // Coupons
        Route::post('/coupons/validate', [IntegrationController::class, 'validateCoupon']);

        // Subscriptions
        Route::post('/subscriptions', [IntegrationController::class, 'createSubscription']);
        Route::get('/subscriptions/{id}/status', [IntegrationController::class, 'getSubscriptionStatus']);
    });

// =====================================================
// 3. Admin API (محمي بـ Sanctum + API Key)
// =====================================================
// للاستخدام من Dashboard أو تطبيقات إدارية
Route::middleware(['auth:sanctum', 'api.key:admin', 'throttle:100,1'])
    ->prefix('admin/v1')
    ->group(function () {

        // Products
        Route::apiResource('products', ProductController::class);

        // Plans
        Route::apiResource('plans', PlanController::class);

        // Customers
        Route::apiResource('customers', CustomerController::class);
        Route::get('customers/{customer}/subscriptions', [CustomerController::class, 'subscriptions']);

        // Subscription Requests
        Route::get('subscription-requests', [SubscriptionRequestController::class, 'index']);
        Route::post('subscription-requests', [SubscriptionRequestController::class, 'store']);
        Route::get('subscription-requests/{subscriptionRequest}', [SubscriptionRequestController::class, 'show']);
        Route::post('subscription-requests/{subscriptionRequest}/approve', [SubscriptionRequestController::class, 'approve']);
        Route::post('subscription-requests/{subscriptionRequest}/reject', [SubscriptionRequestController::class, 'reject']);

        // Subscriptions
        Route::get('subscriptions', [SubscriptionController::class, 'index']);
        Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show']);
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);

        // Coupons
        Route::apiResource('coupons', CouponController::class);
        Route::post('coupons/apply', [CouponController::class, 'apply']);

        // Payments
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payments/{payment}', [PaymentController::class, 'show']);
    });
