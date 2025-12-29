<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductToCartRequest;
use App\Http\Requests\ApplyCouponRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    private function basketDiscountValue(): int
    {
        return (int)Setting::where('key', 'basket_discount_value')->value('value');
    }

    private function basketDiscountIsActive(): bool
    {
        return Setting::where('key', 'basket_discount_is_active')->value('value') === "1";
    }

    private function basketDiscountReachedThreshold(int $cartSubTotal): bool
    {
        $threshold = Setting::where('key', 'basket_discount_threshold')->value('value');
        return $cartSubTotal >= (int)$threshold;
    }

    private function calculateBasketDiscount(int $cartSubTotal): int
    {
        if (!$this->basketDiscountIsActive()) {
            return 0;
        }
        if (!$this->basketDiscountReachedThreshold($cartSubTotal)) {
            return 0;
        }
        return $this->basketDiscountValue();
    }

    public function getCart(): JsonResponse
    {
        $discountedProductPrice = 0;
        $cartSubTotal = 0;
        $couponDiscountAmount = 0;
        $totalProductDiscount = 0;
        $cartItemsData = [];

        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $cartItems = $cart->items;
        foreach ($cartItems as $cartItem) {
            $productPrice = $cartItem->product->price;
            $discountedProductPrice = $productPrice;
            $product = $cartItem->product;
            $discountAmount = 0;
            if ($product->is_active_discount == 1) {
                if ($product->discount_type == 'percent') {
                    $discountAmount = floor(($productPrice * $product->discount_value) / 100);
                } else {
                    $discountAmount = min($product->discount_value, $productPrice);
                }
                $discountedProductPrice-= $discountAmount;
                $totalProductDiscount += $discountAmount * $cartItem->quantity;
            }
            $cartSubTotal += $discountedProductPrice * $cartItem->quantity;
            $cartItemsData[] = [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $discountedProductPrice,
            ];
        }
        $coupon = Coupon::find($cart->coupon_id);
        if ($coupon) {
            if ($coupon->discount_type == 'percent') {
                $couponDiscountAmount = floor(($cartSubTotal * $coupon->value) / 100);
            } else {
                $couponDiscountAmount = $coupon->value;
                $couponDiscountAmount = min($couponDiscountAmount, $cartSubTotal);
            }
        } else {
            $basketDiscount = $this->calculateBasketDiscount($cartSubTotal);
            $couponDiscountAmount = $basketDiscount;
        }

        $totalProfit = $totalProductDiscount + $couponDiscountAmount;
        $total = $cartSubTotal - $couponDiscountAmount;

        return response()->json([
            'items' => $cartItemsData,
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
