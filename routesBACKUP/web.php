<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Cashier\SaleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute
| ini dimuat oleh RouteServiceProvider dan semuanya akan
| ditugaskan ke grup middleware "web". Buat sesuatu yang hebat!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rute yang memerlukan otentikasi
Route::middleware(['auth'])->group(function () {
    
    // Rute Dashboard utama, akan diarahkan berdasarkan peran pengguna
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute untuk manajemen profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GRUP RUTE KHUSUS UNTUK ADMIN ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        
        // TAMBAHKAN RUTE INI
        Route::post('/products/quick-stock', [ProductController::class, 'quickStockUpdate'])->name('products.quick-stock');

        // Rute untuk Impor Produk dari Excel
        Route::get('/products/import', [ProductController::class, 'showImportForm'])->name('products.import.show');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import.store');
        
        // Rute untuk Manajemen Produk (CRUD)
        Route::resource('products', ProductController::class);

        // Rute untuk Laporan Penjualan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print/daily', [ReportController::class, 'printDailyReport'])->name('reports.print.daily');
    });

    // --- GRUP RUTE KHUSUS UNTUK KASIR ---
    Route::middleware('role:cashier')->prefix('cashier')->name('cashier.')->group(function () {
        
        // Rute untuk halaman POS (Mode Biasa dan Mode PC)
        Route::get('/pos', [SaleController::class, 'index'])->name('pos.index');
        Route::get('/pos-pc', [SaleController::class, 'pcIndex'])->name('pos.pcIndex');
        
        // Rute untuk memproses transaksi
        Route::post('/pos/store', [SaleController::class, 'store'])->name('pos.store');
        
        // Rute AJAX untuk mencari produk
        Route::get('/pos/check-product', [SaleController::class, 'checkProduct'])->name('pos.checkProduct');
        Route::get('/pos/search-products', [SaleController::class, 'searchProducts'])->name('pos.search');
        
        // Rute untuk menampilkan dan mencetak struk
        Route::get('/receipt/{sale}', [SaleController::class, 'showReceipt'])->name('receipt.show');
        Route::get('/receipt/{sale}/print', [SaleController::class, 'printReceipt'])->name('receipt.print');
    });
});

// Memuat rute otentikasi (login, register, dll.)
require __DIR__.'/auth.php';
