<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Auth
Route::prefix('auth')->controller(App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout');
    Route::get('/profile', 'profile');
    Route::put('/profile/edit', 'editprofile');
    Route::post('/profile/changepassword', 'changepassword');
});

// User
Route::apiResource('user', App\Http\Controllers\UserController::class);

// Address
Route::apiResource('address', App\Http\Controllers\AddressController::class);

// Category
Route::apiResource('category', App\Http\Controllers\CategoryController::class);

// Tag
Route::apiResource('tag', App\Http\Controllers\TagController::class);

// Product
Route::apiResource('product', App\Http\Controllers\ProductController::class);

// Review
Route::apiResource('review', App\Http\Controllers\ReviewController::class);

// Promo
Route::apiResource('promo', App\Http\Controllers\PromoController::class);

// Cart
Route::get('cart/getbyuser', [App\Http\Controllers\CartController::class, 'getByUser']);
Route::apiResource('cart', App\Http\Controllers\CartController::class);

// Payment
Route::apiResource('payment', App\Http\Controllers\PaymentController::class);

// Order
Route::apiResource('order', App\Http\Controllers\OrderController::class);

// Pay
Route::apiResource('pay', App\Http\Controllers\PayController::class);
