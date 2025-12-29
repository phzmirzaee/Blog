<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Products\AddProductRequest;
use App\Http\Requests\Admin\Products\UpdateProductRequest;
use App\Http\Requests\Admin\StoreProductDiscountRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    private function productWithDiscount(Product $product): array
    {
        $productPrice = $product->price;
        $productDiscount = Product::where('id', $product->id)
            ->where('is_active_discount', 1)
            ->where('start_date_discount', '<=', now())
            ->where('end_date_discount', '>=', now())
            ->first();
        $discountedPrice = null;
        $discountAmount = null;
        if (isset($productDiscount)) {
            if ($productDiscount->discount_type == 'percent') {
                $discountAmount = ($productPrice * $productDiscount->discount_value) / 100;
            } else {
                $discountAmount = $productDiscount->discount_value;
                $discountAmount = max(0, $discountAmount);
            }
            $discountedPrice = $productPrice - $discountAmount;
        }
        $discountedPrice = $productPrice - $discountAmount;
        return [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $productPrice,
            'quantity' => $product->quantity,
            'image' => $product->image,
            'amount' => $discountAmount,
            'discountedPrice' => $discountedPrice,
        ];
    }

    public function getAllProducts(): JsonResponse
    {
        $productsData = [];
        $products = Product::all();
        foreach ($products as $product) {
            $productsData[] = $this->productWithDiscount($product);
        }
        return response()->json([
            'Products' => $productsData,
        ]);
    }

    public function getProduct(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        return response()->json([
            "product" => $this->productWithDiscount($product),
        ]);
    }
    }
