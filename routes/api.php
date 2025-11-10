<?php

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('jwt.auth')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store'])->name('posts.store');
        Route::put('{id}', [PostController::class, 'update'])->name('posts.update');
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);


});

    Route::get('posts/', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/verify-email',[VerifyController::class,'verifyEmail'])->name('verify.email');
    Route::post('/forgot-password',[ForgotPasswordController::class,'forgotPassword'])->name('forgot.password');
    Route::post('/reset-password',[ResetPasswordController::class,'resetPassword'])->name('reset.password');



