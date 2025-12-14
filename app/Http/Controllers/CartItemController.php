<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cartItems = auth()->user()->cartItems()->get();
        $items = [];
        $cartSubTotal = 0;
        $totalProfit = 0;

        foreach ($cartItems as $cartItem) {
            $productPrice = $cartItem->product->price;
            $originalPrice = $productPrice;

            $productDiscount = ProductDiscount::where('product_id', $cartItem->product_id)
                ->where('is_active', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($productDiscount) {
                if ($productDiscount->discount_type == 'percent') {
                    $productPrice -= ($productPrice * $productDiscount->value) / 100;
                } else {
                    $productPrice -= $productDiscount->value;
                }
            }

            $productPrice = max(0, $productPrice);
            $totalProfit +=($originalPrice - $productPrice)*$cartItem->quantity;

            $items[] = [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'total_price' => $productPrice * $cartItem->quantity,
            ];

            $cartSubTotal += $productPrice * $cartItem->quantity;
        }


        $isActiveSetting = Setting::where('key', 'basket_discount_is_active')->first();
        $threshold = Setting::where('key', 'basket_discount_threshold')->first();
        $value = Setting::where('key', 'basket_discount_value')->first();

        $total = $cartSubTotal;

        $couponCode = $request->query('code');
        $couponDiscountAmount = 0;
        $appliedCoupon = null;

        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
        }

        if ($coupon) {
            $appliedCoupon = $coupon->code;

            if ($coupon->discount_type == 'percent') {
                $couponDiscountAmount = ($cartSubTotal * $coupon->value) / 100;
            } else {
                $couponDiscountAmount = $coupon->value;
            }

            $couponDiscountAmount = min($couponDiscountAmount, $cartSubTotal);
            $total = $cartSubTotal - $couponDiscountAmount;
            $totalProfit += $couponDiscountAmount;

        } else {
            if ($isActiveSetting && $isActiveSetting->value == "1") {
                if ($cartSubTotal >= (int)$threshold->value) {
                    $total -= (int)$value->value;
                    $totalProfit += (int)$value->value;
                }
            }
        }

        return response()->json([
            "user_id" => auth()->id(),
            "items" => $items,
            "total_price" => $total,
            "total_profit" => $totalProfit,
            "coupon_code" => $appliedCoupon,
        ]);
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
