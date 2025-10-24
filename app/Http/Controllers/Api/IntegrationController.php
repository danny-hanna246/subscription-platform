<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\SubscriptionRequest;
use App\Services\ApiCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IntegrationController extends Controller
{

    protected $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * الحصول على جميع المنتجات المتاحة
     * GET /api/integration/v1/products
     */
    // في IntegrationController
    public function getProducts()
    {
        $products = $this->cacheService->cacheProducts();

        return response()->json([
            'success' => true,
            'data' => $products,
            'cached' => true,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * الحصول على جميع الخطط المتاحة
     * GET /api/integration/v1/plans
     * Query params: ?product_id=1
     */
    public function getPlans(Request $request)
    {
        $productId = $request->filled('product_id') ? $request->product_id : null;
        $plans = $this->cacheService->cachePlans($productId);

        return response()->json([
            'success' => true,
            'data' => $plans,
            'cached' => true,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * الحصول على تفاصيل خطة معينة
     * GET /api/integration/v1/plans/{id}
     */
    public function getPlan($id)
    {
        $plan = Plan::with('product')->where('active', true)->find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    /**
     * إنشاء عميل جديد
     * POST /api/integration/v1/customers
     */
    public function createCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:200',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Customer::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * الحصول على معلومات عميل
     * GET /api/integration/v1/customers/{email}
     */
    public function getCustomer($email)
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    /**
     * التحقق من صحة كوبون
     * POST /api/integration/v1/coupons/validate
     */
    public function validateCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|exists:coupons,code',
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
        $plan = Plan::find($request->plan_id);

        if (!$coupon->isValid($plan->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid or has expired',
            ], 400);
        }

        $originalPrice = $plan->price;
        $discount = $coupon->calculateDiscount($originalPrice);
        $finalPrice = $originalPrice - $discount;

        return response()->json([
            'success' => true,
            'data' => [
                'coupon_code' => $coupon->code,
                'coupon_type' => $coupon->type,
                'coupon_value' => (float) $coupon->value,
                'original_price' => (float) $originalPrice,
                'discount' => (float) $discount,
                'final_price' => (float) $finalPrice,
                'currency' => $plan->currency,
            ],
        ]);
    }

    /**
     * إنشاء طلب اشتراك جديد
     * POST /api/integration/v1/subscriptions
     */
    public function createSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:online,cash',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Customer::where('email', $request->email)->first();
        $plan = Plan::with('product')->findOrFail($request->plan_id);
        $amount = $plan->price;

        // تطبيق الكوبون إن وجد
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if ($coupon && $coupon->isValid($plan->id)) {
                $discount = $coupon->calculateDiscount($amount);
                $amount -= $discount;
                $coupon->incrementUsage();
            }
        }

        $subscriptionRequest = SubscriptionRequest::create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'payment_method' => $request->payment_method,
            'status' => $request->payment_method === 'online' ? 'processing' : 'pending',
            'amount' => $amount,
            'currency' => $plan->currency,
            'payment_token' => $request->payment_method === 'online'
                ? 'PAY_' . \Illuminate\Support\Str::random(32)
                : null,
            'coupon_code' => $request->coupon_code ? strtoupper($request->coupon_code) : null,
        ]);

        $response = [
            'success' => true,
            'message' => 'Subscription request created successfully',
            'data' => [
                'subscription_request_id' => $subscriptionRequest->id,
                'customer' => $customer->only(['id', 'name', 'email']),
                'plan' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'product' => $plan->product->name,
                ],
                'amount' => (float) $amount,
                'currency' => $plan->currency,
                'payment_method' => $subscriptionRequest->payment_method,
                'status' => $subscriptionRequest->status,
            ],
        ];

        // إذا كان الدفع إلكتروني، نرجع رابط الدفع
        if ($request->payment_method === 'online') {
            // هنا سيتم استدعاء بوابة الدفع لاحقاً
            $response['data']['payment_url'] = route('payment.process', ['token' => $subscriptionRequest->payment_token]);
            $response['data']['payment_token'] = $subscriptionRequest->payment_token;
        }

        return response()->json($response, 201);
    }

    /**
     * التحقق من حالة طلب اشتراك
     * GET /api/integration/v1/subscriptions/{id}/status
     */
    public function getSubscriptionStatus($id)
    {
        $subscriptionRequest = SubscriptionRequest::with(['subscription.license'])
            ->findOrFail($id);

        $data = [
            'subscription_request_id' => $subscriptionRequest->id,
            'status' => $subscriptionRequest->status,
            'payment_method' => $subscriptionRequest->payment_method,
            'amount' => (float) $subscriptionRequest->amount,
            'currency' => $subscriptionRequest->currency,
        ];

        if ($subscriptionRequest->subscription) {
            $data['subscription'] = [
                'id' => $subscriptionRequest->subscription->id,
                'status' => $subscriptionRequest->subscription->status,
                'starts_at' => $subscriptionRequest->subscription->starts_at->toDateTimeString(),
                'ends_at' => $subscriptionRequest->subscription->ends_at->toDateTimeString(),
            ];

            if ($subscriptionRequest->subscription->license) {
                $data['license_key'] = $subscriptionRequest->subscription->license->license_key;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * الحصول على اشتراكات العميل
     * GET /api/integration/v1/customers/{email}/subscriptions
     */
    public function getCustomerSubscriptions($email)
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $subscriptions = $customer->subscriptions()
            ->with(['plan.product', 'license'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }
}
