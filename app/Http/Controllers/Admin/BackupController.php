<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderComplaint;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Tampilan Utama Pusat Backup & Migrasi Data (Vue 3 Inertia di Admin Panel)
     */
    public function index()
    {
        $shop = Setting::pluck('value', 'key')->all();

        $stats = [
            'total_products'    => Product::count(),
            'total_sales'       => Sale::count(),
            'total_sale_items'  => SaleDetail::count(),
            'total_orders'      => Schema::hasTable('orders') ? Order::count() : 0,
            'total_order_items' => Schema::hasTable('order_items') ? OrderItem::count() : 0,
            'total_complaints'  => Schema::hasTable('order_complaints') ? OrderComplaint::count() : 0,
            'total_users'       => User::count(),
            'total_settings'    => Setting::count(),
            'storage_size'      => $this->getStorageSizeReadable(),
        ];

        return Inertia::render('Admin/Backup/Index', [
            'shop'  => $shop,
            'stats' => $stats,
        ]);
    }

    /**
     * Ekspor Paket Lengkap Migrasi (.ZIP)
     */
    public function exportZip()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $appName = Setting::where('key', 'app_name')->value('value') ?: 'SIBALOG';
        $cleanAppName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $appName));
        $timestamp = Carbon::now()->format('Ymd_His');
        $zipFileName = "BACKUP_MIGRASI_{$cleanAppName}_{$timestamp}.zip";

        $tempDir = storage_path("app/temp_backup_{$timestamp}");
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        try {
            $jsonData = $this->collectAllData();
            $jsonFilePath = "{$tempDir}/backup_data.json";
            File::put($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            $sqlContent = $this->generateSqlDump($jsonData);
            $sqlFilePath = "{$tempDir}/database.sql";
            File::put($sqlFilePath, $sqlContent);

            $readmeContent = $this->generateReadme($cleanAppName, $timestamp, $jsonData['metadata']['counts']);
            File::put("{$tempDir}/README_MIGRASI.txt", $readmeContent);

            $zipPath = storage_path("app/{$zipFileName}");
            
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $zip->addFile($jsonFilePath, 'backup_data.json');
                    $zip->addFile($sqlFilePath, 'database.sql');
                    $zip->addFile("{$tempDir}/README_MIGRASI.txt", 'README_MIGRASI.txt');

                    $publicStoragePath = storage_path('app/public');
                    if (File::exists($publicStoragePath)) {
                        $files = File::allFiles($publicStoragePath);
                        foreach ($files as $file) {
                            $relativePath = 'storage/' . str_replace('\\', '/', $file->getRelativePathname());
                            $zip->addFile($file->getRealPath(), $relativePath);
                        }
                    }

                    $extraFolders = ['products', 'favicons', 'logos', 'audio', 'complaints'];
                    foreach ($extraFolders as $folder) {
                        $extraPath = storage_path("app/{$folder}");
                        if (File::exists($extraPath)) {
                            $files = File::allFiles($extraPath);
                            foreach ($files as $file) {
                                $relativePath = 'storage/' . $folder . '/' . str_replace('\\', '/', $file->getRelativePathname());
                                $zip->addFile($file->getRealPath(), $relativePath);
                            }
                        }
                    }

                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuat arsip ZIP.');
                }
            } else {
                File::deleteDirectory($tempDir);
                return response()->json($jsonData, 200, [
                    'Content-Disposition' => "attachment; filename=\"BACKUP_DATA_{$cleanAppName}_{$timestamp}.json\"",
                ]);
            }

            File::deleteDirectory($tempDir);

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            Log::error("Gagal mengekspor data backup: " . $e->getMessage());
            return back()->with('error', 'Gagal membuat file backup: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor Cepat Format JSON Saja
     */
    public function exportJson()
    {
        $appName = Setting::where('key', 'app_name')->value('value') ?: 'SIBALOG';
        $cleanAppName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $appName));
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "BACKUP_DATA_{$cleanAppName}_{$timestamp}.json";

        $data = $this->collectAllData();

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Ekspor Cepat Format SQL Saja
     */
    public function exportSql()
    {
        $appName = Setting::where('key', 'app_name')->value('value') ?: 'SIBALOG';
        $cleanAppName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $appName));
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "DATABASE_DUMP_{$cleanAppName}_{$timestamp}.sql";

        $jsonData = $this->collectAllData();
        $sql = $this->generateSqlDump($jsonData);

        return response($sql, 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Proses Impor & Pemulihan (Restore) Data dari File Backup via Admin Panel
     */
    public function import(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $request->validate([
            'backup_file' => 'required|file|max:204800',
            'mode'        => 'required|in:replace,merge',
        ], [
            'backup_file.required' => 'Pilih file backup (.zip atau .json) yang ingin dipulihkan.',
            'backup_file.max'      => 'Ukuran file backup maksimal adalah 200MB.',
        ]);

        $uploadedFile = $request->file('backup_file');
        $mode = $request->input('mode', 'replace');

        $result = $this->processRestoreFile($uploadedFile, $mode);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Portal Upload & Migrasi Terpadu (/migrasibaru)
     * - GET: Menampilkan form upload file migrasi & opsi mode
     * - POST: Memproses migrasi database, storage symlink, dan restore data dari file yang diunggah
     */
    public function migrasibaru(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        // Jika request GET: Tampilkan Form Upload Migrasi
        if ($request->isMethod('get')) {
            return response()->view('migrasibaru', [
                'hasSubmitted' => false,
                'results'      => [],
                'hasError'     => false,
                'stats'        => [
                    'products' => Schema::hasTable('products') ? Product::count() : 0,
                    'users'    => Schema::hasTable('users') ? User::count() : 0,
                    'orders'   => Schema::hasTable('orders') ? Order::count() : 0,
                ],
            ]);
        }

        // Jika request POST: Jalankan proses migrasi & pemulihan
        $results = [];
        $hasError = false;
        $mode = $request->input('mode', 'replace');

        try {
            // 1. Eksekusi Migrasi Struktur Database
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();
            $results[] = [
                'title'   => 'Struktur Database (Migration)',
                'status'  => 'SUCCESS',
                'message' => trim($migrateOutput) ?: 'Semua skema tabel database siap dan ter-update.',
            ];

            // 2. Buat Storage Symlink
            try {
                Artisan::call('storage:link');
                $storageOutput = Artisan::output();
                $results[] = [
                    'title'   => 'Folder Media Storage (Symlink)',
                    'status'  => 'SUCCESS',
                    'message' => trim($storageOutput) ?: 'Symlink folder media publik terhubung.',
                ];
            } catch (\Exception $se) {
                $results[] = [
                    'title'   => 'Folder Media Storage (Symlink)',
                    'status'  => 'INFO',
                    'message' => 'Symlink storage sudah siap.',
                ];
            }

            // 3. Jika Ada File Backup yang Diunggah (.ZIP / .JSON / .SQL)
            if ($request->hasFile('backup_file')) {
                $uploadedFile = $request->file('backup_file');
                $restoreRes = $this->processRestoreFile($uploadedFile, $mode);

                if ($restoreRes['success']) {
                    $results[] = [
                        'title'   => 'Pemulihan Data & Media dari File Backup',
                        'status'  => 'SUCCESS',
                        'message' => $restoreRes['message'],
                    ];
                } else {
                    $hasError = true;
                    $results[] = [
                        'title'   => 'Pemulihan File Backup',
                        'status'  => 'ERROR',
                        'message' => $restoreRes['message'],
                    ];
                }
            } else {
                // Jika tidak unggah file, cek akun admin default
                if (Schema::hasTable('users') && User::count() === 0) {
                    User::create([
                        'name'     => 'Administrator Toko',
                        'email'    => 'admin@sultanweb.id',
                        'password' => Hash::make('password'),
                        'role'     => 'admin',
                    ]);
                    User::create([
                        'name'     => 'Kasir Toko',
                        'email'    => 'kasir@sultanweb.id',
                        'password' => Hash::make('password'),
                        'role'     => 'cashier',
                    ]);
                    $results[] = [
                        'title'   => 'Inisialisasi Akun Petugas Baru',
                        'status'  => 'SUCCESS',
                        'message' => 'Dibuat 2 akun default: admin@sultanweb.id (Admin) & kasir@sultanweb.id (Kasir). Password: "password".',
                    ];
                }

                if (Schema::hasTable('settings') && Setting::count() === 0) {
                    Setting::create(['key' => 'shop_name', 'value' => 'SIBALOG STORE']);
                    Setting::create(['key' => 'app_name', 'value' => 'SIBALOG POS & ONLINE STORE']);
                    Setting::create(['key' => 'shop_phone', 'value' => '081234567890']);
                    Setting::create(['key' => 'shop_address', 'value' => 'Jl. Raya Utama No. 123']);
                }

                $results[] = [
                    'title'   => 'Status Database',
                    'status'  => 'INFO',
                    'message' => 'Database kosong berhasil disiapkan. Anda dapat login menggunakan akun default.',
                ];
            }

            // 4. Bersihkan Cache Laravel
            Artisan::call('optimize:clear');
            Artisan::call('optimize');
            $results[] = [
                'title'   => 'Optimasi Performa Sistem',
                'status'  => 'SUCCESS',
                'message' => 'Cache route, config, dan views berhasil disegarkan.',
            ];

        } catch (\Exception $e) {
            $hasError = true;
            $results[] = [
                'title'   => 'Terjadi Kendala',
                'status'  => 'ERROR',
                'message' => $e->getMessage(),
            ];
            Log::error("Gagal menjalankan migrasi baru: " . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => !$hasError,
                'results' => $results,
            ]);
        }

        return response()->view('migrasibaru', [
            'hasSubmitted' => true,
            'results'      => $results,
            'hasError'     => $hasError,
            'time'         => Carbon::now()->translatedFormat('d F Y, H:i:s'),
            'stats'        => [
                'products' => Schema::hasTable('products') ? Product::count() : 0,
                'users'    => Schema::hasTable('users') ? User::count() : 0,
                'orders'   => Schema::hasTable('orders') ? Order::count() : 0,
            ],
        ]);
    }

    /**
     * Helper: Memproses File Restore (.ZIP, .JSON, .SQL)
     */
    private function processRestoreFile($uploadedFile, string $mode): array
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $tempExtractDir = storage_path('app/temp_restore_' . time());

        try {
            if ($extension === 'zip') {
                if (!class_exists('ZipArchive')) {
                    throw new \Exception('Ekstensi PHP ZipArchive tidak aktif di server ini.');
                }

                $zip = new ZipArchive();
                if ($zip->open($uploadedFile->getRealPath()) === true) {
                    $zip->extractTo($tempExtractDir);
                    $zip->close();

                    $jsonPath = "{$tempExtractDir}/backup_data.json";
                    if (!File::exists($jsonPath)) {
                        throw new \Exception('File backup_data.json tidak ditemukan di dalam paket ZIP.');
                    }
                    $jsonData = json_decode(File::get($jsonPath), true);

                    // Salin seluruh file storage (foto produk, galeri, dll)
                    $extractedStorage = "{$tempExtractDir}/storage";
                    if (File::exists($extractedStorage)) {
                        $targetStorage = storage_path('app/public');
                        if (!File::exists($targetStorage)) {
                            File::makeDirectory($targetStorage, 0755, true);
                        }
                        File::copyDirectory($extractedStorage, $targetStorage);
                    }

                    $restoredCounts = $this->restoreDatabase($jsonData['tables'] ?? [], $mode);

                } else {
                    throw new \Exception('Gagal mengekstrak arsip ZIP.');
                }

            } elseif ($extension === 'json') {
                $jsonData = json_decode(File::get($uploadedFile->getRealPath()), true);
                if (!$jsonData || !isset($jsonData['tables'])) {
                    throw new \Exception('Struktur file JSON tidak valid.');
                }
                $restoredCounts = $this->restoreDatabase($jsonData['tables'], $mode);

            } elseif ($extension === 'sql') {
                $sqlContent = File::get($uploadedFile->getRealPath());
                DB::unprepared($sqlContent);
                $restoredCounts = ['sql_executed' => 1];
            } else {
                throw new \Exception('Format file tidak didukung. Harap unggah file .zip, .json, atau .sql.');
            }

            if (File::exists($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }

            $modeLabel = ($mode === 'replace') ? 'Mode Timpa Bersih' : 'Mode Gabungkan';
            $summary = "Pemulihan data berhasil ({$modeLabel}). Rincian data: " . implode(', ', array_map(
                fn($k, $v) => "{$v} {$k}",
                array_keys($restoredCounts),
                array_values($restoredCounts)
            ));

            return [
                'success' => true,
                'message' => $summary,
            ];

        } catch (\Exception $e) {
            if (File::exists($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }
            Log::error("Gagal memproses file restore: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Helper: Kumpulkan Semua Data Database ke dalam Array
     */
    private function collectAllData(): array
    {
        $tables = [
            'settings'         => DB::table('settings')->get()->map(fn($r) => (array) $r)->toArray(),
            'users'            => DB::table('users')->get()->map(fn($r) => (array) $r)->toArray(),
            'products'         => DB::table('products')->get()->map(fn($r) => (array) $r)->toArray(),
            'sales'            => DB::table('sales')->get()->map(fn($r) => (array) $r)->toArray(),
            'sale_details'     => DB::table('sale_details')->get()->map(fn($r) => (array) $r)->toArray(),
            'orders'           => Schema::hasTable('orders') ? DB::table('orders')->get()->map(fn($r) => (array) $r)->toArray() : [],
            'order_items'      => Schema::hasTable('order_items') ? DB::table('order_items')->get()->map(fn($r) => (array) $r)->toArray() : [],
            'order_complaints' => Schema::hasTable('order_complaints') ? DB::table('order_complaints')->get()->map(fn($r) => (array) $r)->toArray() : [],
        ];

        $counts = array_map('count', $tables);

        return [
            'metadata' => [
                'app_name'     => Setting::where('key', 'app_name')->value('value') ?: 'SIBALOG POS',
                'app_version'  => '2.0-Production',
                'exported_at'  => Carbon::now()->toIso8601String(),
                'exported_by'  => auth()->user()->name ?? 'Administrator',
                'php_version'  => PHP_VERSION,
                'counts'       => $counts,
            ],
            'tables' => $tables,
        ];
    }

    /**
     * Helper: Generate Dump SQL Raw
     */
    private function generateSqlDump(array $jsonData): string
    {
        $sql = "-- ========================================================\n";
        $sql .= "-- SIBALOG DATABASE BACKUP & MIGRATION DUMP\n";
        $sql .= "-- Aplikasi   : " . ($jsonData['metadata']['app_name'] ?? 'SIBALOG') . "\n";
        $sql .= "-- Waktu      : " . ($jsonData['metadata']['exported_at'] ?? date('c')) . "\n";
        $sql .= "-- Diekspor Oleh: " . ($jsonData['metadata']['exported_by'] ?? 'Admin') . "\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($jsonData['tables'] as $tableName => $rows) {
            if (empty($rows)) continue;

            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Data untuk tabel `{$tableName}` (" . count($rows) . " baris)\n";
            $sql .= "-- --------------------------------------------------------\n";

            foreach ($rows as $row) {
                $columns = array_keys($row);
                $escapedColumns = array_map(fn($c) => "`{$c}`", $columns);
                $escapedValues = array_map(function($v) {
                    if (is_null($v)) return 'NULL';
                    if (is_numeric($v)) return $v;
                    return "'" . addslashes((string)$v) . "'";
                }, array_values($row));

                $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ") ON DUPLICATE KEY UPDATE `updated_at` = VALUES(`updated_at`);\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- Selesai.\n";

        return $sql;
    }

    /**
     * Helper: Jalankan Restore Database
     */
    private function restoreDatabase(array $tables, string $mode): array
    {
        $restoredCounts = [];

        $tableOrder = [
            'settings',
            'users',
            'products',
            'sales',
            'sale_details',
            'orders',
            'order_items',
            'order_complaints',
        ];

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        DB::beginTransaction();

        try {
            if ($mode === 'replace') {
                $reverseOrder = array_reverse($tableOrder);
                foreach ($reverseOrder as $tbl) {
                    if (Schema::hasTable($tbl)) {
                        DB::table($tbl)->truncate();
                    }
                }
            }

            foreach ($tableOrder as $tbl) {
                if (!isset($tables[$tbl]) || !is_array($tables[$tbl])) continue;
                if (!Schema::hasTable($tbl)) continue;

                $rows = $tables[$tbl];
                $count = 0;

                foreach ($rows as $row) {
                    $validColumns = Schema::getColumnListing($tbl);
                    $filteredRow = array_intersect_key($row, array_flip($validColumns));

                    if (empty($filteredRow)) continue;

                    if ($mode === 'replace') {
                        DB::table($tbl)->insert($filteredRow);
                        $count++;
                    } else {
                        if (isset($filteredRow['id'])) {
                            $id = $filteredRow['id'];
                            $existing = DB::table($tbl)->where('id', $id)->first();
                            if ($existing) {
                                DB::table($tbl)->where('id', $id)->update($filteredRow);
                            } else {
                                DB::table($tbl)->insert($filteredRow);
                            }
                        } else {
                            DB::table($tbl)->insert($filteredRow);
                        }
                        $count++;
                    }
                }

                $restoredCounts[$tbl] = $count;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        }

        return $restoredCounts;
    }

    /**
     * Helper: Buat File README_MIGRASI.txt
     */
    private function generateReadme(string $appName, string $timestamp, array $counts): string
    {
        $cSettings   = $counts['settings'] ?? 0;
        $cUsers      = $counts['users'] ?? 0;
        $cProducts   = $counts['products'] ?? 0;
        $cSales      = $counts['sales'] ?? 0;
        $cSaleItems  = $counts['sale_details'] ?? 0;
        $cOrders     = $counts['orders'] ?? 0;
        $cOrderItems = $counts['order_items'] ?? 0;
        $cComplaints = $counts['order_complaints'] ?? 0;

        return <<<TXT
================================================================================
PAKET LENGKAP BACKUP & MIGRASI SISTEM
================================================================================
Nama Aplikasi : {$appName}
Waktu Ekspor  : {$timestamp}
Dibuat Oleh   : Sistem Backup Otomatis SIBALOG

ISI PAKET INI:
1. backup_data.json  : Berisi seluruh data database terstruktur dalam format JSON.
2. database.sql      : Dump database MySQL/MariaDB siap import via phpMyAdmin / CLI.
3. storage/          : Berisi seluruh file upload asli (foto barang, galeri, logo, dll).

RINGKASAN DATA:
- Pengaturan Sistem : {$cSettings} data
- Akun Pengguna     : {$cUsers} akun
- Produk & Stok     : {$cProducts} produk
- Transaksi Kasir   : {$cSales} transaksi
- Detail Penjualan  : {$cSaleItems} item
- Pesanan Online    : {$cOrders} pesanan
- Item Pesanan      : {$cOrderItems} item
- Komplain Masuk    : {$cComplaints} data
================================================================================
TXT;
    }

    /**
     * Helper: Hitung Ukuran Folder Storage
     */
    private function getStorageSizeReadable(): string
    {
        $path = storage_path('app/public');
        if (!File::exists($path)) return '0 MB';

        $bytes = 0;
        foreach (File::allFiles($path) as $file) {
            $bytes += $file->getSize();
        }

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
