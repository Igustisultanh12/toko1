<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Cashier\SaleController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rute Publik untuk Login & Webhook DOKU
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/doku/notification', [\App\Http\Controllers\DokuNotificationController::class, 'handle']);
Route::match(['get', 'post'], '/doku/inquiry', [\App\Http\Controllers\DokuInquiryController::class, 'handle']);

// Rute yang Dilindungi (Harus login & mengirim token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rute default untuk mendapatkan data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute untuk POS
    Route::get('/products/check', [SaleController::class, 'checkProductByBarcode']);
    Route::get('/products/search', [SaleController::class, 'searchProductByName']);
    Route::post('/sales', [SaleController::class, 'store']);

    // Rute untuk Manajemen Produk (jika diperlukan di aplikasi)
    Route::get('/products', [ProductController::class, 'index']);
});