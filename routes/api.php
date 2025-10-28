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
use App\Mail\LicenseKeyMail;
use App\Mail\SubscriptionApprovedMail;
use App\Models\License;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;

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
    ->middleware('throttle:10,1'); // 120 طلب في الدقيقة

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


Route::prefix('test')->group(function () {

    // 1. اختبار بسيط
    Route::post('/email/simple', function () {
        try {
            Mail::raw('هذا اختبار بسيط لإرسال البريد الإلكتروني', function ($message) {
                $message->to(request('email'))
                    ->subject('اختبار البريد الإلكتروني');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully to ' . request('email'),
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    });

    // 2. اختبار إرسال بريد الموافقة على الاشتراك
    Route::post('/email/subscription-approved', function () {
        try {
            $subscription = Subscription::with(['customer', 'plan.product', 'license'])->latest()->first();

            if (!$subscription || !$subscription->license) {
                return response()->json([
                    'success' => false,
                    'error' => 'No valid subscription with license found',
                ], 404);
            }

            Mail::to(request('email', $subscription->customer->email))
                ->send(new SubscriptionApprovedMail($subscription, $subscription->license));

            return response()->json([
                'success' => true,
                'message' => 'Subscription approved email sent successfully',
                'sent_to' => request('email', $subscription->customer->email),
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // 3. اختبار إرسال مفتاح الترخيص
    Route::post('/email/license-key', function () {
        try {
            $license = License::with(['subscription.customer', 'subscription.plan.product'])->latest()->first();

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'error' => 'No licenses found',
                ], 404);
            }

            Mail::to(request('email', $license->subscription->customer->email))
                ->send(new LicenseKeyMail($license));

            return response()->json([
                'success' => true,
                'message' => 'License key email sent successfully',
                'sent_to' => request('email', $license->subscription->customer->email),
                'license_key' => $license->license_key,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // 4. اختبار الاتصال بـ SMTP
    Route::get('/email/connection', function () {
        try {
            $transport = Mail::getSwiftMailer()->getTransport();
            $transport->start();

            return response()->json([
                'success' => true,
                'message' => 'SMTP connection successful',
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // 5. عرض الإعدادات الحالية
    Route::get('/email/config', function () {
        return response()->json([
            'mail_config' => [
                'default_mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
        ]);
    });
});
