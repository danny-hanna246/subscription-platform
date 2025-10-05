<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\AuditLog;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount(['subscriptions', 'activeSubscriptions'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        AuditLog::log('Customer', $customer->id, 'create', $request->validated());

        return new CustomerResource($customer);
    }

    public function show(Customer $customer)
    {
        $customer->loadCount(['subscriptions', 'activeSubscriptions']);

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $oldData = $customer->toArray();
        $customer->update($request->validated());

        AuditLog::log('Customer', $customer->id, 'update', [
            'old' => $oldData,
            'new' => $request->validated(),
        ]);

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        AuditLog::log('Customer', $customer->id, 'delete', null);

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }

    public function subscriptions(Customer $customer)
    {
        $subscriptions = $customer->subscriptions()
            ->with(['plan.product', 'license'])
            ->latest()
            ->paginate(10);

        return response()->json($subscriptions);
    }
}
