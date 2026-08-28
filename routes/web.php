<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ManualGuideController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Cashier\SaleController;
use App\Http\Controllers\DokuNotificationController;
use App\Http\Controllers\DokuInquiryController;
use App\Http\Controllers\TteVerificationController;

use App\Http\Controllers\OnlineOrderController;
use App\Http\Controllers\Admin\OnlineOrderAdminController;
use App\Http\Controllers\OrderComplaintController;

// RUTE MIGRASI LANGSUNG SISTEM & DATABASE SATU-KLIK (Bisa diakses langsung / first setup)
Route::match(['get', 'post'], '/migrasibaru', [BackupController::class, 'migrasibaru'])->name('migrasibaru');

Route::get('/', function () {
    return redirect()->route('order.index'); // Default homepage membuka Toko Online Publik
});

/**
 * 1. JALUR PESANAN ONLINE PUBLIK & PELACAKAN RESI (GUEST / TANPA LOGIN)
 */
Route::get('/order', [OnlineOrderController::class, 'index'])->name('order.index');
Route::get('/toko', [OnlineOrderController::class, 'index'])->name('order.storefront');
Route::get('/pesan-online', [OnlineOrderController::class, 'index'])->name('order.online');
Route::get('/order/checkout', function() {
    return redirect()->route('order.index');
});
Route::post('/order/checkout', [OnlineOrderController::class, 'store'])->name('order.checkout');
Route::get('/order/pay/{order_number}', [OnlineOrderController::class, 'pay'])->name('order.pay');
Route::get('/order/get-qris/{order_number}', [OnlineOrderController::class, 'getQris'])->name('order.getQris');
Route::get('/order/check-status/{order_number}', [OnlineOrderController::class, 'checkStatus'])->name('order.checkStatus');
Route::post('/order/simulate-pay/{order_number}', [OnlineOrderController::class, 'simulatePay'])->name('order.simulatePay');
Route::get('/order/receipt/{order_number}', [OnlineOrderController::class, 'receipt'])->name('order.receipt');
Route::get('/order/receipt/{order_number}/pdf', [OnlineOrderController::class, 'printReceiptPdf'])->name('order.receipt.pdf');

// Portal Publik Lacak Pesanan & Konfirmasi Diterima
Route::get('/lacak', [OnlineOrderController::class, 'trackIndex'])->name('order.track.index');
Route::get('/lacak/{order_number}', [OnlineOrderController::class, 'track'])->name('order.track');
Route::post('/lacak/{order_number}/received', [OnlineOrderController::class, 'confirmReceived'])->name('order.confirm-received');
Route::get('/track', [OnlineOrderController::class, 'trackIndex'])->name('order.track.alias');
Route::get('/track/{order_number}', [OnlineOrderController::class, 'track'])->name('order.track.direct');

// HALAMAN KHUSUS PUSAT KOMPLAIN PELANGGAN (Link diberikan oleh Kasir/Admin jika ada kendala)
Route::get('/komplain/{order_number}', [OrderComplaintController::class, 'showForm'])->name('order.complaint.show');
Route::post('/komplain/{order_number}', [OrderComplaintController::class, 'store'])->name('order.complaint.store');
Route::get('/complaint/{order_number}', [OrderComplaintController::class, 'showForm'])->name('order.complaint.alias');
Route::post('/complaint/{order_number}', [OrderComplaintController::class, 'store'])->name('order.complaint.alias.store');

/**
 * 2. JALUR WEBHOOK DOKU (WAJIB DI LUAR AUTH)
 * Jalur ini akan ditembak oleh Server DOKU setelah pelanggan bayar.
 */
Route::match(['get', 'post'], '/doku/notification', [DokuNotificationController::class, 'handle'])->name('doku.notification');
Route::match(['get', 'post'], '/doku/inquiry', [DokuInquiryController::class, 'handle'])->name('doku.inquiry');

/**
 * 3. JALUR CETAK STRUK, FAKTUR & LABEL PENGIRIMAN PAKET (PUBLIK / BISA DIAKSES VIA LINK WA)
 */
Route::get('/receipt/{sale}/print', [SaleController::class, 'generateReceipt'])->name('receipt.print');
Route::get('/receipt/{sale}/pdf', [ReportController::class, 'exportInvoicePdf'])->name('receipt.pdf');
Route::get('/invoice/{transaction_number}/get-link', [ReportController::class, 'getSignedInvoiceLink'])->name('invoice.get-link');
Route::get('/invoice/{transaction_number}/download', [ReportController::class, 'downloadSignedInvoice'])->name('invoice.public.signed');
Route::get('/invoice/{transaction_number}/pdf', [ReportController::class, 'downloadSignedInvoice'])->name('invoice.public.number');
Route::get('/verify/tte/{transaction_number}', [TteVerificationController::class, 'verify'])->name('verify.tte');
Route::get('/verify/document', [TteVerificationController::class, 'verifyDocument'])->name('verify.document');
Route::match(['get', 'post'], '/shipping-label/{sale}/pdf', [SaleController::class, 'generateShippingLabel'])->name('shipping.label.pdf');

/**
 * Route Khusus Streaming File Media (Bypass Nginx Static Interception & Tanpa Bergantung Symlink)
 */
Route::get('/media-file', function (\Illuminate\Http\Request $request) {
    $path = $request->query('path');
    if (!$path) {
        abort(404, 'Path file tidak diberikan.');
    }

    // Bersihkan path dari directory traversal
    $path = ltrim(str_replace(['../', '..\\'], '', $path), '/\\');

    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        $altPath = storage_path('app/' . $path);
        if (file_exists($altPath)) {
            $fullPath = $altPath;
        } else {
            \Illuminate\Support\Facades\Log::warning("Media file tidak ditemukan di server: {$path}", [
                'expected_path' => $fullPath,
                'url'           => request()->fullUrl(),
            ]);
            abort(404, "File '{$path}' tidak ditemukan di storage server.");
        }
    }

    $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
    if (str_ends_with(strtolower($fullPath), '.ico')) {
        $mime = 'image/x-icon';
    } elseif (str_ends_with(strtolower($fullPath), '.svg')) {
        $mime = 'image/svg+xml';
    } elseif (str_ends_with(strtolower($fullPath), '.mp3')) {
        $mime = 'audio/mpeg';
    }

    return response()->file($fullPath, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('media.file');

/**
 * Fallback Route untuk Serving File Storage Publik (Anti 404 jika symlink server aaPanel belum aktif)
 */
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        $altPath = storage_path('app/' . $path);
        if (file_exists($altPath)) {
            $filePath = $altPath;
        } else {
            \Illuminate\Support\Facades\Log::warning("Storage file tidak ditemukan di server: {$path}", [
                'expected_path' => $filePath,
                'url'           => request()->fullUrl(),
                'ip'            => request()->ip()
            ]);
            abort(404, "File '{$path}' tidak ditemukan di storage server.");
        }
    }
    \Illuminate\Support\Facades\Log::info("Serving storage asset: {$path}");
    return response()->file($filePath);
})->where('path', '.*')->name('storage.fallback');

/**
 * 3. RUTE TERPROTEKSI (LOGIN WAJIB)
 */
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/live-stats', [DashboardController::class, 'liveStats'])->name('dashboard.live-stats');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MANAJEMEN PESANAN ONLINE & PANDUAN (Bisa diakses oleh Admin & Kasir) ---
    Route::middleware('role:admin,cashier')->prefix('admin')->name('admin.')->group(function () {
        // MANAJEMEN PESANAN ONLINE (KONFIRMASI, PROSES, INPUT RESI, SELESAIKAN, CETAK LABEL)
        Route::get('/orders', [OnlineOrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OnlineOrderAdminController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}', [OnlineOrderAdminController::class, 'update'])->name('orders.update');
        Route::post('/orders/{id}/confirm', [OnlineOrderAdminController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/orders/{id}/ship', [OnlineOrderAdminController::class, 'shipOrder'])->name('orders.ship');
        Route::post('/orders/{id}/complete', [OnlineOrderAdminController::class, 'completeOrder'])->name('orders.complete');
        Route::post('/orders/{id}/cancel', [OnlineOrderAdminController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('/orders/{id}/update-process', [OnlineOrderAdminController::class, 'updateProcess'])->name('orders.update-process');
        Route::get('/orders/{id}/shipping-label', [OnlineOrderAdminController::class, 'printShippingLabel'])->name('orders.shipping-label');
        Route::get('/orders/{id}/receipt-pdf', [OnlineOrderAdminController::class, 'printThermalReceipt'])->name('orders.receipt-pdf');
        Route::post('/complaints/{id}/status', [OrderComplaintController::class, 'adminUpdateStatus'])->name('complaints.update-status');

        // BUKU PANDUAN PENGGUNA (Bisa dibaca oleh Kasir & Admin)
        Route::get('/manual-guide', [ManualGuideController::class, 'index'])->name('manual.index');
        Route::get('/manual-guide/pdf', [ManualGuideController::class, 'exportPdf'])->name('manual.pdf');
    });

    // --- GRUP KHUSUS ADMINISTRATOR (Produk, Pengguna, Laporan, Pengaturan Toko) ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/products/quick-stock', [ProductController::class, 'quickStockUpdate'])->name('products.quick-stock');
        Route::get('/products/import', [ProductController::class, 'showImportForm'])->name('products.import.show');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import.store');
        Route::resource('products', ProductController::class);
        Route::resource('users', UserController::class);
        
        // 1. LAPORAN PENJUALAN (INVOICE, PELANGGAN & BARANG)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/print/daily', [ReportController::class, 'printDailyReport'])->name('reports.print.daily');
        Route::get('/reports/invoice/{sale}/pdf', [ReportController::class, 'exportInvoicePdf'])->name('reports.invoice.pdf');
        
        // 2. LAPORAN KEUANGAN (ARUS KAS, OMSET, TUNAI & QRIS)
        Route::get('/reports/finance', [ReportController::class, 'financialReport'])->name('reports.finance');
        Route::get('/reports/finance/pdf', [ReportController::class, 'exportFinancePdf'])->name('reports.finance.pdf');
        Route::get('/reports/finance/excel', [ReportController::class, 'exportFinanceExcel'])->name('reports.finance.excel');

        //route laporan qris
        Route::get('/reports/qris', [ReportController::class, 'qrisReport'])->name('reports.qris');
        // route export excel and pdf qris
        Route::get('/reports/qris/pdf', [ReportController::class, 'exportQrisPdf'])->name('reports.qris.pdf');
        Route::get('/reports/qris/excel', [ReportController::class, 'exportQrisExcel'])->name('reports.qris.excel');

        // 3. LAPORAN STOK BARANG & INVENTARIS
        Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
        Route::get('/reports/stock/pdf', [ReportController::class, 'exportStockPdf'])->name('reports.stock.pdf');
        Route::get('/reports/stock/excel', [ReportController::class, 'exportStockExcel'])->name('reports.stock.excel');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // 4. PUSAT BACKUP & MIGRASI DATA LENGKAP
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::get('/backup/export-zip', [BackupController::class, 'exportZip'])->name('backup.export.zip');
        Route::get('/backup/export-json', [BackupController::class, 'exportJson'])->name('backup.export.json');
        Route::get('/backup/export-sql', [BackupController::class, 'exportSql'])->name('backup.export.sql');
        Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');
        Route::post('/backup/migrate-now', [BackupController::class, 'migrasibaru'])->name('backup.migrate-now');
    });

    // --- GRUP KASIR (Bisa diakses oleh Kasir dan Administrator) ---
    Route::middleware('role:cashier,admin')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/pos', [SaleController::class, 'index'])->name('pos.index');
        Route::post('/pos/store', [SaleController::class, 'store'])->name('pos.store');
        Route::post('/pos/store-sale', [SaleController::class, 'store'])->name('pos.store-sale');
        Route::get('/pos/check-product', [SaleController::class, 'checkProduct'])->name('pos.checkProduct');
        Route::get('/pos/search-products', [SaleController::class, 'search'])->name('pos.search');
        
        // KRUSIAL: Pastikan rute ini bisa menerima sale_id untuk verifikasi otomatis
        Route::get('/pos/check-status/{sale}', [SaleController::class, 'checkStatus'])->name('pos.checkStatus');
        Route::get('/sales/{sale}/check-status', [SaleController::class, 'checkStatus'])->name('sales.checkStatus');
        Route::post('/pos/force-confirm/{sale}', [SaleController::class, 'forceConfirm'])->name('pos.forceConfirm');
        Route::post('/sales/{sale}/force-confirm', [SaleController::class, 'forceConfirm'])->name('sales.forceConfirm');
        
        Route::get('/receipt/{sale}/print', [SaleController::class, 'generateReceipt'])->name('receipt.print');

        // Kasir juga bisa melihat dan memproses pesanan online
        Route::get('/orders', [OnlineOrderAdminController::class, 'index'])->name('orders.index');
        Route::post('/orders/{id}/confirm', [OnlineOrderAdminController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/orders/{id}/ship', [OnlineOrderAdminController::class, 'shipOrder'])->name('orders.ship');
    });

    // API Polling Realtime Notifikasi Pesanan Online untuk Kasir & Admin
    Route::get('/api/online-orders/realtime-check', [OnlineOrderAdminController::class, 'checkNewOrders'])->name('orders.realtime-check');
});

require __DIR__.'/auth.php';