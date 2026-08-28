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
     * Tampilan Utama Pusat Backup & Migrasi Data (Vue 3 Inertia)
     */
    public function index()
    {
        $shop = Setting::pluck('value', 'key')->all();

        // Hitung total data statistik
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
     * Berisi: backup_data.json, database.sql, README_MIGRASI.txt, dan seluruh file upload di storage/app/public/
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
            // 1. Ekspor Data JSON
            $jsonData = $this->collectAllData();
            $jsonFilePath = "{$tempDir}/backup_data.json";
            File::put($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            // 2. Ekspor SQL Dump
            $sqlContent = $this->generateSqlDump($jsonData);
            $sqlFilePath = "{$tempDir}/database.sql";
            File::put($sqlFilePath, $sqlContent);

            // 3. Buat File Petunjuk Migrasi
            $readmeContent = $this->generateReadme($cleanAppName, $timestamp, $jsonData['metadata']['counts']);
            File::put("{$tempDir}/README_MIGRASI.txt", $readmeContent);

            // 4. Buat File ZIP
            $zipPath = storage_path("app/{$zipFileName}");
            
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $zip->addFile($jsonFilePath, 'backup_data.json');
                    $zip->addFile($sqlFilePath, 'database.sql');
                    $zip->addFile("{$tempDir}/README_MIGRASI.txt", 'README_MIGRASI.txt');

                    // Tambahkan seluruh file storage publik
                    $publicStoragePath = storage_path('app/public');
                    if (File::exists($publicStoragePath)) {
                        $files = File::allFiles($publicStoragePath);
                        foreach ($files as $file) {
                            $relativePath = 'storage/' . str_replace('\\', '/', $file->getRelativePathname());
                            $zip->addFile($file->getRealPath(), $relativePath);
                        }
                    }

                    // Folder upload langsung di storage/app jika ada
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
     * Proses Impor & Pemulihan (Restore) Data dari File Backup (.ZIP / .JSON)
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
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $mode = $request->input('mode', 'replace');

        $jsonData = null;
        $tempExtractDir = storage_path('app/temp_restore_' . time());

        try {
            if ($extension === 'zip') {
                if (!class_exists('ZipArchive')) {
                    throw new \Exception('Ekstensi PHP ZipArchive tidak aktif untuk membaca file ZIP.');
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

                    $extractedStorage = "{$tempExtractDir}/storage";
                    if (File::exists($extractedStorage)) {
                        $targetStorage = storage_path('app/public');
                        if (!File::exists($targetStorage)) {
                            File::makeDirectory($targetStorage, 0755, true);
                        }
                        File::copyDirectory($extractedStorage, $targetStorage);
                    }
                } else {
                    throw new \Exception('Gagal membuka file arsip ZIP.');
                }
            } elseif ($extension === 'json') {
                $jsonData = json_decode(File::get($uploadedFile->getRealPath()), true);
            } else {
                throw new \Exception('Format file tidak didukung. Harap unggah file .zip atau .json.');
            }

            if (!$jsonData || !isset($jsonData['tables'])) {
                throw new \Exception('Struktur file backup tidak valid atau rusak.');
            }

            $restoredCounts = $this->restoreDatabase($jsonData['tables'], $mode);

            if (File::exists($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }

            Artisan::call('optimize:clear');

            $modeLabel = ($mode === 'replace') ? 'Mode Timpa Bersih' : 'Mode Gabungkan';
            $summary = "Pemulihan data selesai ({$modeLabel}). Berhasil memulihkan: " . implode(', ', array_map(
                fn($k, $v) => "{$v} {$k}",
                array_keys($restoredCounts),
                array_values($restoredCounts)
            ));

            return back()->with('success', $summary);

        } catch (\Exception $e) {
            if (File::exists($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }
            Log::error("Gagal restore backup: " . $e->getMessage());
            return back()->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    /**
     * Jalur Migrasi Langsung Satu-Klik (/migrasibaru)
     * Menjalankan:
     * 1. php artisan migrate --force
     * 2. Buat akun Admin default jika belum ada
     * 3. Buat setelan default jika belum ada
     * 4. php artisan storage:link
     * 5. php artisan optimize:clear & optimize
     */
    public function migrasibaru(Request $request)
    {
        ini_set('max_execution_time', 300);

        $results = [];
        $hasError = false;

        try {
            // 1. Jalankan Database Migration
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();
            $results[] = [
                'title'   => 'Database Migration',
                'status'  => 'SUCCESS',
                'message' => trim($migrateOutput) ?: 'Semua tabel database berhasil dimigrasi.',
            ];

            // 2. Cek & Buat Akun Pengguna Default jika tabel kosong
            if (Schema::hasTable('users')) {
                $userCount = User::count();
                if ($userCount === 0) {
                    $admin = User::create([
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
                        'title'   => 'Inisialisasi Akun Petugas Default',
                        'status'  => 'SUCCESS',
                        'message' => 'Dibuat 2 akun default: admin@sultanweb.id (Admin) & kasir@sultanweb.id (Kasir) dengan password default: "password".',
                    ];
                } else {
                    $results[] = [
                        'title'   => 'Akun Petugas',
                        'status'  => 'INFO',
                        'message' => "Terdapat {$userCount} akun petugas yang sudah terdaftar di sistem.",
                    ];
                }
            }

            // 3. Cek & Buat Pengaturan Toko Default
            if (Schema::hasTable('settings')) {
                $defaultSettings = [
                    'shop_name'     => 'SIBALOG STORE',
                    'app_name'      => 'SIBALOG POS & ONLINE STORE',
                    'shop_phone'    => '081234567890',
                    'shop_address'  => 'Jl. Raya Utama No. 123, Indonesia',
                    'footer_note'   => 'Terima kasih atas kunjungan dan kepercayaan Anda.',
                ];

                foreach ($defaultSettings as $key => $val) {
                    Setting::firstOrCreate(['key' => $key], ['value' => $val]);
                }

                $results[] = [
                    'title'   => 'Pengaturan Toko',
                    'status'  => 'SUCCESS',
                    'message' => 'Pengaturan nama toko, alamat, dan profil awal berhasil dikonfigurasi.',
                ];
            }

            // 4. Buat Symlink Storage
            try {
                Artisan::call('storage:link');
                $storageOutput = Artisan::output();
                $results[] = [
                    'title'   => 'Storage Symlink',
                    'status'  => 'SUCCESS',
                    'message' => trim($storageOutput) ?: 'Symlink folder storage publik berhasil ditautkan.',
                ];
            } catch (\Exception $se) {
                $results[] = [
                    'title'   => 'Storage Symlink',
                    'status'  => 'INFO',
                    'message' => 'Storage symlink sudah ada atau tidak memerlukan perubahan.',
                ];
            }

            // 5. Bersihkan & Buat Ulang Cache Sistem
            Artisan::call('optimize:clear');
            Artisan::call('optimize');
            $results[] = [
                'title'   => 'Optimasi & Cache Laravel',
                'status'  => 'SUCCESS',
                'message' => 'Cache config, route, dan view telah disegarkan untuk performa maksimal.',
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

        // Jika request menginginkan JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => !$hasError,
                'results' => $results,
            ]);
        }

        // Tampilkan HTML modern mandiri (bisa dibuka langsung tanpa login untuk first setup)
        return response()->view('migrasibaru', [
            'results'  => $results,
            'hasError' => $hasError,
            'time'     => Carbon::now()->translatedFormat('d F Y, H:i:s'),
        ]);
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
3. storage/          : Berisi seluruh file upload asli:
   - products/       : Foto utama produk & galeri multi-foto (products/gallery/)
   - complaints/     : Bukti foto dan video unboxing komplain pelanggan
   - logos/          : File logo toko
   - favicons/       : File favicon aplikasi
   - audio/          : Suara lonceng kasir transaksi lunas

RINGKASAN JUMLAH DATA:
- Pengaturan Sistem : {$cSettings} data
- Akun Pengguna     : {$cUsers} akun
- Produk & Stok     : {$cProducts} produk
- Transaksi Kasir   : {$cSales} transaksi
- Detail Penjualan  : {$cSaleItems} item
- Pesanan Online    : {$cOrders} pesanan
- Item Pesanan      : {$cOrderItems} item
- Komplain Masuk    : {$cComplaints} data

================================================================================
CARA MEMINDAHKAN KE SERVER / APLIKASI BARU:
================================================================================
METODE 1 (PALING MUDAH - VIA APLIKASI WEB):
1. Login ke Panel Admin aplikasi baru.
2. Buka menu "Backup & Migrasi Data" (atau URL /admin/backup).
3. Pada bagian "Pulihkan / Impor Data", pilih file .zip ini.
4. Pilih mode "Timpa Bersih (Fresh Replace)" untuk server baru, lalu klik "Mulai Proses Impor".
5. Selesai! Semua data database, foto barang, transaksi, dan settingan toko langsung aktif.

METODE 2 (MANUAL VIA SSH / FTP / CPANEL / AAPANEL):
1. Salin seluruh isi folder "storage/" di zip ini ke folder:
   /www/wwwroot/domain-anda/storage/app/public/
2. Import file "database.sql" ke database MySQL/MariaDB baru lewat phpMyAdmin atau terminal:
   mysql -u user_db -p nama_db < database.sql
3. Jalankan perintah di server baru:
   php artisan storage:link
   php artisan optimize:clear
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
