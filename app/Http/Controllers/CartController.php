<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductToCartRequest;
use App\Http\Requests\ApplyCouponRequest;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function getCart(): JsonResponse
    {
        $cartSubTotal = 0;
        $totalProfit = 0;
        $couponDiscountAmount = 0;
        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }
        $cartItems = $cart->items;
        foreach ($cartItems as $cartItem) {
            $cartSubTotal += $cartItem->product->price * $cartItem->quantity;
        }
        $coupon = Coupon::find($cart->coupon_id);
        if ($coupon) {
            if ($coupon->discount_type == 'percent') {
                $couponDiscountAmount = ($cartSubTotal * $coupon->value) / 100;
            } else {
                $couponDiscountAmount = $coupon->value;
                $couponDiscountAmount = min($couponDiscountAmount, $cartSubTotal);
            }
        }

        $isActiveSetting = Setting::where('key', 'basket_discount_is_active')->first();
        $threshold = Setting::where('key', 'basket_discount_threshold')->first();
        $value = Setting::where('key', 'basket_discount_value')->first();
        $total = 0;
        if ($isActiveSetting && $isActiveSetting->value == "1") {
            if ($cartSubTotal >= (int)$threshold->value) {
                $total -= (int)$value->value;
                $couponDiscountAmount += (int)$value->value;
            }
        }

        $total = $cartSubTotal - $couponDiscountAmount;
        $totalProfit += $couponDiscountAmount;

        return response()->json([
            'product_id'=>$cartItem->product_id,
            'quantity'=>$cartItem->quantity,
            'total_price' => $total,
            'total_profit' => $totalProfit,
            'coupon_code' => $coupon?->code,
        ]);
    }

    public function addProductToCart(AddProductToCartRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $validated = $request->validated();
        $cart = Cart::query()->where('user_id', $userId)->first();
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
            ]);
        }

        $item = $cart->items()->where('product_id', $validated['product_id'])->first();
        if ($item) {
            $item->increment('quantity', $validated['quantity']);
        } else {
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);
        }
        return response()->json('محصول مورد نظر به سبد اضافه شد.');
    }

    public function removeProductFromCart(int $productId): JsonResponse
    {
        $cart = Cart::query()
            ->where('user_id', auth()->id())
            ->first();
        $item = $cart->items()
            ->where('product_id', $productId)
            ->first();
        if (!$item) {
            return response()->json(['message' => 'محصول مورد نظر شما یافت نشد'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'محصول از سبد شما حذف شد.']);
    }

    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $coupon = Coupon::where('code', $validated['code'])
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'کوپن نامعتبر است'], 422);
        }


        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $usedCount = Cart::where('coupon_id', $coupon->id)
            ->whereNotNull('coupon_applied_at')
            ->count();

        if ($usedCount >= $coupon->usage_limit) {
            return response()->json([
                'message' => 'ظرفیت استفاده از این کد پر شده است.'
            ]);
        }
        $cart->update([
            'coupon_id' => $coupon->id,
            'coupon_applied_at' => now(),
        ]);

        return response()->json([
            'message' => 'کد تخفیف شما اعمال شد',
        ]);
    }
}
