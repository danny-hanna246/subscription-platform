<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Models\AuditLog;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('product')
            ->when(request('product_id'), function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when(request('active'), function ($query, $active) {
                $query->where('active', $active);
            })
            ->paginate(15);

        return PlanResource::collection($plans);
    }

    public function store(StorePlanRequest $request)
    {
        $plan = Plan::create($request->validated());

        AuditLog::log('Plan', $plan->id, 'create', $request->validated());

        return new PlanResource($plan->load('product'));
    }

    public function show(Plan $plan)
    {
        return new PlanResource($plan->load('product'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $oldData = $plan->toArray();
        $plan->update($request->validated());

        AuditLog::log('Plan', $plan->id, 'update', [
            'old' => $oldData,
            'new' => $request->validated(),
        ]);

        return new PlanResource($plan->load('product'));
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        AuditLog::log('Plan', $plan->id, 'delete', null);

        return response()->json([
            'message' => 'Plan deleted successfully',
        ]);
    }
}
