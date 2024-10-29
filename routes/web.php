<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::post('/', function () {
    return [];
});

Route::get('get-products', [ProductController::class, 'getProducts']);
Route::post('upload-img-product', [ProductController::class, 'uploadImageProduct']);
Route::get('search-product/{text}', [ProductController::class, 'searchProducts']);

Route::post('momo', [CheckoutController::class, 'Momo']);
Route::post('vnpay', [CheckoutController::class, 'VNPay']);
Route::get('vnpay', [CheckoutController::class, 'VNPay']);
Route::get('getvnpay', [CheckoutController::class, 'getVNPay']);

Route::post('next-order-id', [OrderController::class, 'getNextOrderID']);

Route::get('google/redirect', [GoogleController::class, 'handleRedirect']);
Route::get('google/callback', [GoogleController::class, 'handleCallback']);

Route::get('auth/google', [GoogleController::class,'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class,'handleGoogleCallback']);

// require __DIR__.'/auth.php';
