<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Auth
Route::prefix('auth')->controller(App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/profile', 'profile')->middleware('auth:sanctum');
    Route::put('/profile/edit', 'editprofile')->middleware('auth:sanctum');
    Route::post('/profile/changepassword', 'changepassword')->middleware('auth:sanctum');
});

// User
Route::apiResource('user', App\Http\Controllers\UserController::class)->middleware('auth:sanctum');

// Address
Route::apiResource('address', App\Http\Controllers\AddressController::class)->middleware('auth:sanctum');

// Category
Route::apiResource('category', App\Http\Controllers\CategoryController::class)->middleware('auth:sanctum');
