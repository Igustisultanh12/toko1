<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <--- WAJIB TAMBAHKAN BARIS INI

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void 
    { 
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        // Set PHP timezone & Carbon locale ke Waktu Indonesia Barat (WIB)
        date_default_timezone_set('Asia/Jakarta');
        \Carbon\Carbon::setLocale('id');

        // Pastikan tabel settings ada sebelum mengambil data agar tidak error saat migrasi awal
        if (Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::pluck('value', 'key')->all();
            view()->share('shop', $settings);
        }
    }
}