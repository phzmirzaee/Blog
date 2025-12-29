<?php

use App\Http\Controllers\Admin\Discount\CouponsController;
use App\Http\Controllers\Admin\Discount\GlobalCartDiscountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('jwt.auth')->group(function () {
    Route::get('/cart', [CartController::class, 'getCart'])->name('cart.show');
    Route::post('/cart', [CartController::class, 'addProductToCart'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'removeProductFromCart'])->name('cart.remove');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store'])->name('posts.store');
        Route::put('{id}', [PostController::class, 'update'])->name('posts.update');
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductsController::class, 'getProducts'])->name('admin.products.getProducts');
            Route::get('/getProduct/{id}', [AdminProductsController::class, 'getProduct'])->name('admin.products.getProduct');
            Route::post('', [AdminProductsController::class, 'addProduct'])->name('admin.products.add');
            Route::put('{id}', [AdminProductsController::class, 'update'])->name('admin.products.update');
            Route::delete('{id}', [AdminProductsController::class, 'delete'])->name('admin.products.delete');
            Route::post('discount/{id}', [AdminProductsController::class, 'addProductDiscount'])->name('admin.products.discount.add');
        });
        Route::prefix('discounts')->group(function () {
            Route::post('/setting', [GlobalCartDiscountController::class, 'configure'])->name('discounts.cart');
            Route::post('/coupons', [CouponsController::class, 'addCoupon'])->name('discounts.couponsStore');
        });
    });
});

Route::get('/post/product/{id}', [ProductsController::class, 'getProduct'])->name('products.getProduct');
Route::get('/post/product', [ProductsController::class, 'getAllProducts'])->name('products.getAllProducts');


Route::get('posts/', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');

Route::get('/verify-email/{user}', [VerifyController::class, 'verifyEmail'])->name('verify.email')->middleware('signed');
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot.password');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('reset.password');


