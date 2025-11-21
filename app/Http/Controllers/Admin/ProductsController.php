<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreProductRequest;
use App\Http\Requests\Admin\Products\UpdateProductRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json([
            "products" => $products
        ]);
    }

    public function show(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        return response()->json([
            "product" => $product
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = $request->file('image')->store('productsImage', 'public');

        $createdProduct = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'image' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'محصول با موفقیت ایجاد شد.',
            'product' => $createdProduct
        ]);
    }

    public function update(UpdateProductRequest $request, int $productId): JsonResponse
    {
        $validated = $request->validated();
        $product = Product::findOrFail($productId);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            "success" => true,
            "message" => "محصول با موفقیت بروزرسانی شد.",
            "product" => $product
        ]);
    }

    public function delete(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $product->delete();
        return response()->json([
            "message" => "محصول با موفقیت حذف شد."
        ]);
    }
}
