<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\RegisterController;

Route::group(['middleware' => ['cors', 'json.response']], function () {
Route::post('register', [RegisterController::class,'register']);
Route::post('login', [RegisterController::class,'login']);
});

Route::group(['middleware' => ['cors', 'json.response','auth:api']], function () {
    Route::post('logout', [RegisterController::class,'logout']);
    Route::get('homeApi', [HomeController::class,'index']);
    Route::resource('products', \App\Http\Controllers\API\ProductController::class);
});
