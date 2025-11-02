<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;

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



