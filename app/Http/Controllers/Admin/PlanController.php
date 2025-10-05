<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('product')->latest()->paginate(12);
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:80',
            'slug' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'duration_days' => 'required|integer|min:1',
            'user_limit' => 'required|integer|min:1',
            'device_limit' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'active' => 'boolean',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active');

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم إنشاء الخطة بنجاح');
    }

    public function edit(Plan $plan)
    {
        $products = Product::all();
        return view('admin.plans.edit', compact('plan', 'products'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:80',
            'slug' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'duration_days' => 'required|integer|min:1',
            'user_limit' => 'required|integer|min:1',
            'device_limit' => 'required|integer|min:1',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active');

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم تحديث الخطة بنجاح');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم حذف الخطة بنجاح');
    }
}
