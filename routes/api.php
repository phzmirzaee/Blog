<?php

use App\Http\Controllers\Admin\Discount\CouponsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\Admin\Discount\DiscountController;
use App\Http\Controllers\Admin\Discount\ProductsDiscountController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('jwt.auth')->group(function () {
    Route::get('/cartItem',[CartItemController::class, 'index'])->name('cart.index');
    Route::post('/cartItem',[CartItemController::class, 'store'])->name('cart.store');
    Route::get('/cart',[CartController::class, 'getCart'])->name('getCart');
    Route::post('/cart',[CartController::class, 'addProductToCart'])->name('addProductToCart');
    Route::delete('/cart/{id}',[CartController::class, 'removeProductFromCart'])->name('removeProductFromCart');
    Route::post('/cart/coupon',[CartController::class, 'applyCoupon'])->name('applyCoupon');
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store'])->name('posts.store');
        Route::put('{id}', [PostController::class, 'update'])->name('posts.update');
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::prefix('products')->group(function () {
            Route::post('', [ProductsController::class, 'store'])->name('products.store');
            Route::put('{id}', [ProductsController::class, 'update'])->name('products.update');
            Route::delete('{id}', [ProductsController::class, 'delete'])->name('products.delete');
        });
        Route::prefix('discounts')->group(function () {
            Route::post('/setting',[DiscountController::class, 'configure'])->name('discount.setting');
            Route::post('/product',[ProductsDiscountController::class, 'configure'])->name('products.discount.store');
            Route::post('/coupons',[CouponsController::class, 'configure'])->name('coupons.store');
        });
    });
});

Route::get('/post/product/{id}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/post/product', [ProductsController::class, 'index'])->name('products.index');


Route::get('posts/', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');

Route::get('/verify-email/{user}', [VerifyController::class, 'verifyEmail'])->name('verify.email')->middleware('signed');
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot.password');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('reset.password');


