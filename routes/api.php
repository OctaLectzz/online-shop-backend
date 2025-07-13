<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Auth
Route::prefix('auth')->controller(App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
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

// Shipment
Route::apiResource('shipment', App\Http\Controllers\ShipmentController::class);

// Faq
Route::apiResource('faq', App\Http\Controllers\FaqController::class);

// Contact
Route::apiResource('contact', App\Http\Controllers\ContactController::class)->except(['update']);
Route::put('/contact', [App\Http\Controllers\ContactController::class, 'update']);

// Setting
Route::bind('setting', function ($value) {
    return Setting::where('key', $value)->firstOrFail();
});
Route::apiResource('setting', App\Http\Controllers\SettingController::class);

// Log
Route::get('/log', [App\Http\Controllers\LogController::class, 'index']);
Route::get('/log/{log}', [App\Http\Controllers\LogController::class, 'show']);
Route::post('/log/read-all', [App\Http\Controllers\LogController::class, 'markAllAsRead']);
Route::delete('/log/{log}', [App\Http\Controllers\LogController::class, 'destroy']);
Route::delete('/log', [App\Http\Controllers\LogController::class, 'destroyAll']);
