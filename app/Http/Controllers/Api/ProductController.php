<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\AuditLog;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['plans' => function ($query) {
            $query->where('active', true);
        }])
            ->withCount('plans')
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        AuditLog::log('Product', $product->id, 'create', $request->validated());

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        $product->load(['plans' => function ($query) {
            $query->where('active', true);
        }]);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $oldData = $product->toArray();
        $product->update($request->validated());

        AuditLog::log('Product', $product->id, 'update', [
            'old' => $oldData,
            'new' => $request->validated(),
        ]);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        AuditLog::log('Product', $product->id, 'delete', null);

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
