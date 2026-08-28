<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * JADWAL OTOMATIS: Hapus pesanan online & transaksi kasir yang tidak dibayar lebih dari 24 jam
 * Dijalankan setiap jam secara otomatis di background.
 */
Schedule::command('orders:cleanup-unpaid')->hourly();
