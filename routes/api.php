<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [\App\Http\Controllers\API\LoginController::class, 'register']);
Route::post('login', [\App\Http\Controllers\API\LoginController::class, 'login']);
Route::get('homeApi', [\App\Http\Controllers\API\HomeController::class,'index']);

Route::middleware('auth:api')->group( function () {
    Route::resource('products', \App\Http\Controllers\API\ProductController::class);
});
