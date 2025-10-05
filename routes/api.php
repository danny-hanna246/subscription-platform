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
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------


Route::middleware('api.key')->prefix('integration/v1')->group(function () {
    Route::post('/customers', [IntegrationController::class, 'createCustomer']);
    Route::get('/plans', [IntegrationController::class, 'getPlans']);
    Route::post('/subscriptions', [IntegrationController::class, 'createSubscription']);
});*/

// Public Routes - License Validation (مهم جداً للتطبيقات)
Route::post('/v1/licenses/validate', [LicenseValidationController::class, 'validate']);

Route::middleware('api.key:validate_license')->group(function () {
    Route::post('/v1/licenses/validate', [LicenseValidationController::class, 'validate']);
});
// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});

// Protected Admin Routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Products
    Route::apiResource('products', ProductController::class);

    // Plans
    Route::apiResource('plans', PlanController::class);

    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/{customer}/subscriptions', [CustomerController::class, 'subscriptions']);

    // Subscription Requests
    Route::apiResource('subscription-requests', SubscriptionRequestController::class)
        ->only(['index', 'store', 'show']);
    Route::post('subscription-requests/{subscriptionRequest}/approve', [SubscriptionRequestController::class, 'approve']);
    Route::post('subscription-requests/{subscriptionRequest}/reject', [SubscriptionRequestController::class, 'reject']);

    // Subscriptions
    Route::apiResource('subscriptions', SubscriptionController::class)
        ->only(['index', 'show']);
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);

    // Coupons
    Route::apiResource('coupons', CouponController::class);
    Route::post('coupons/apply', [CouponController::class, 'apply']);

    // Payments
    Route::apiResource('payments', PaymentController::class)
        ->only(['index', 'show']);
});
