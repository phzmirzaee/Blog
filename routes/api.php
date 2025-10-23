<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'index'])->name('posts.index');
    Route::post('', [PostController::class, 'store'])->name('posts.store');
    Route::put('{id}', [PostController::class, 'update'])->name('posts.update');
    Route::get('{id}', [PostController::class, 'show'])->name('posts.show');
});

