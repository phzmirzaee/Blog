<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function index(): JsonResponse
    {
        $cartItems = auth()->user()->cartItems()->get();
        return response()->json([
            'cartItems' => $cartItems,
        ]);

    }

    public function Store(CartItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::findOrFail($validated['product_id']);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $totalPrice = $product->price * $validated['quantity'];

        if (!$cartItem) {
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
                'total_price' => $totalPrice,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'محصول به سبد خرید شما اضافه شد.',
                'cartItem' => $cartItem,
            ], 201);
        }

        $newQuantity = $cartItem->quantity + $validated['quantity'];

        if ($newQuantity > $product->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'محصول موجود نیست',
            ], 422);
        }

        $cartItem->update([
            'quantity' => $newQuantity,
            'total_price' => $newQuantity * $product->price,
        ]);

        return response()->json([
            'status' => 'success',
            'cartItem' => $cartItem,
        ]);
    }

}
