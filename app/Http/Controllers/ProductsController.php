<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Products\AddProductRequest;
use App\Http\Requests\Admin\Products\UpdateProductRequest;
use App\Http\Requests\Admin\AddProductDiscountRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    private function calculateProductDiscount(Product $product): array
    {
        $originalPrice = $product->price;
        $discountAmount=0;
        if (
            $product->is_active_discount &&
            $product->start_date_discount <= now() &&
            $product->end_date_discount >= now()
        ) {
            if ($product->discount_type == 'percent') {
                $discountAmount = floor(( $originalPrice * $product->discount_value) / 100);
            } else {
                $discountAmount =$product->discount_value;
            }
        }
        $finalPrice = $originalPrice - $discountAmount;
        return [
            'name' => $product->name,
            'description' => $product->description,
            'originalPrice' => $originalPrice,
            'quantity' => $product->quantity,
            'image' => $product->image,
            'discount_amount' => $discountAmount,
            'finalPrice' => $finalPrice,
        ];
    }

    public function getAllProducts(): JsonResponse
    {
        $productsData = [];
        $products = Product::all();
        foreach ($products as $product) {
            $productsData[] = $this->calculateProductDiscount($product);
        }
        return response()->json([
            'Products' => $productsData,
        ]);
    }

    public function getProduct(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        return response()->json([
            "product" => $this->calculateProductDiscount($product),
        ]);
    }
    }
