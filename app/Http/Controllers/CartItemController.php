<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function index(): JsonResponse
    {
        $cartItems = auth()->user()->cartItems()->get();
        $items = [];
        $cartSubTotal = 0;
        foreach ($cartItems as $cartItem) {
            $items[] = [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'total_price' => $cartItem->product->price * $cartItem->quantity,
            ];
            $cartSubTotal += $cartItem->product->price * $cartItem->quantity;
        }
        $isActiveSetting = Setting::where('key', 'basket_discount_is_active')->first();
        $threshold = Setting::where('key', 'basket_discount_threshold')->first();
        $value = Setting::where('key', 'basket_discount_value')->first();

        $isDiscountApplied = false;
        if ($isActiveSetting->value == "1") {
            if ($cartSubTotal >= (int)$threshold->value) {
                $total = $cartSubTotal - (int)$value->value;
                $isDiscountApplied = true;
            } else {
                $total = $cartSubTotal;
            }
        } else {
            $total = $cartSubTotal;
        }
        return response()->json(
            [
                "user_id" => auth()->id(),
                "items" => $items,
                "total_price" => $total,
                "total_profit" => $isDiscountApplied ? (int)$value->value : 0,
            ]

        );

    }

    public function store(CartItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::findOrFail($validated['product_id']);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];
        } else {
            $newQuantity = $validated['quantity'];
        }


        if ($newQuantity > $product->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'محصول موجود نیست',
            ], 422);
        }

        if (!$cartItem) {
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'محصول به سبد خرید شما اضافه شد.',
                'cartItem' => $cartItem,
            ], 201);
        }

        $cartItem->update([
            'quantity' => $newQuantity,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'سبد خرید بروزرسانی شد.',
            'cartItem' => $cartItem,
        ]);

    }

}
