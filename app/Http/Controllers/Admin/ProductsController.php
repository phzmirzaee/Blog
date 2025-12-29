<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\AddProductRequest;
use App\Http\Requests\Admin\Products\UpdateProductRequest;
use App\Http\Requests\Admin\StoreProductDiscountRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function getProducts(): JsonResponse
    {
        $products = Product::all();
        return response()->json([
            'Products' => $products,
        ]);
    }

    public function getProduct(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        return response()->json([
            "product" => $product
        ]);
    }
    public function addProduct(AddProductRequest $request): JsonResponse
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
    public function addProductDiscount(StoreProductDiscountRequest $request, int $productId): JsonResponse
    {
        $validated = $request->validated();
        $product = Product::findOrFail($productId);
        Product::updateOrCreate(

            ['id' => $product->id],
            [
                'discount_type' => $validated['discount_type'],
                'is_active_discount' => $validated['is_active_discount'],
                'discount_value' => $validated['discount_value'],
                'start_date_discount' => $validated['start_date_discount'],
                'end_date_discount' => $validated['end_date_discount'],
            ]
        );
        return response()->json([
            'message' => 'تخفیف محصولات با موفقیت تنظیم شد.',
        ]);

    }


}
