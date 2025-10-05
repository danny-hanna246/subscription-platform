<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\ApplyCouponRequest;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::when(request('active'), function ($query) {
            $query->active();
        })
            ->latest()
            ->paginate(15);

        return response()->json($coupons);
    }

    public function store(StoreCouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());

        AuditLog::log('Coupon', $coupon->id, 'create', $request->validated());

        return response()->json([
            'message' => 'Coupon created successfully',
            'data' => $coupon,
        ], 201);
    }

    public function show(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:percent,fixed',
            'value' => 'sometimes|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after:valid_from',
            'applicable_plans' => 'nullable|array',
        ]);

        $coupon->update($validated);

        AuditLog::log('Coupon', $coupon->id, 'update', $validated);

        return response()->json([
            'message' => 'Coupon updated successfully',
            'data' => $coupon,
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        AuditLog::log('Coupon', $coupon->id, 'delete', null);

        return response()->json([
            'message' => 'Coupon deleted successfully',
        ]);
    }

    public function apply(ApplyCouponRequest $request)
    {
        $coupon = Coupon::where('code', $request->coupon_code)->firstOrFail();
        $plan = Plan::findOrFail($request->plan_id);

        if (!$coupon->isValid($plan->id)) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon is not valid or has expired',
            ], 400);
        }

        $originalPrice = $plan->price;
        $discount = $coupon->calculateDiscount($originalPrice);
        $finalPrice = $originalPrice - $discount;

        return response()->json([
            'valid' => true,
            'data' => [
                'original_price' => (float) $originalPrice,
                'discount' => (float) $discount,
                'final_price' => (float) $finalPrice,
                'coupon_type' => $coupon->type,
                'coupon_value' => (float) $coupon->value,
            ],
        ]);
    }
}
