<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Menampilkan Pusat Komando & Pengaturan Sistem
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update Identitas Aplikasi, Toko, Favicon, DOKU, & Fitur Suara
     */
    public function update(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'app_name'              => 'nullable|string|max:100',
            'app_tagline'           => 'nullable|string|max:150',
            'shop_name'             => 'required|string|max:100',
            'shop_phone'            => 'nullable|string|max:20',
            'shop_address'          => 'nullable|string|max:500',
            'app_favicon'           => 'nullable|mimes:ico,png,jpg,jpeg,svg|max:1024',
            'shop_logo'             => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'payment_success_sound' => 'nullable|mimes:mp3,wav,ogg|max:3072',
            'is_voice_enabled'      => 'nullable|in:0,1',
            'doku_client_id'        => 'nullable|string',
            'doku_secret_key'       => 'nullable|string',
            'doku_base_url'         => 'nullable|url',
            'cashier_officer_title' => 'nullable|string|max:100',
            'cashier_officer_name'  => 'nullable|string|max:100',
            'receipt_footer'        => 'nullable|string|max:200',
            'turnstile_enabled'     => 'nullable|in:0,1',
            'turnstile_site_key'    => 'nullable|string|max:150',
            'turnstile_secret_key'  => 'nullable|string|max:150',
        ]);

        // Ambil semua data kecuali file dan token
        $data = $request->except(['_token', '_method', 'app_favicon', 'shop_logo', 'payment_success_sound']);

        // 2. LOGIKA TOGGLE SUARA & TURNSTILE
        $data['is_voice_enabled']  = $request->has('is_voice_enabled') ? '1' : '0';
        $data['turnstile_enabled'] = $request->has('turnstile_enabled') ? '1' : '0';

        // 3. LOGIKA UPLOAD FAVICON
        if ($request->hasFile('app_favicon')) {
            $this->deleteOldFile('app_favicon');
            $path = $request->file('app_favicon')->store('favicons', 'public');
            $data['app_favicon'] = $path;
            Log::info("Favicon baru berhasil disimpan di storage: " . $path, [
                'full_path' => storage_path('app/public/' . $path),
                'exists'    => file_exists(storage_path('app/public/' . $path))
            ]);
        }

        // 4. LOGIKA UPLOAD LOGO TOKO
        if ($request->hasFile('shop_logo')) {
            $this->deleteOldFile('shop_logo');
            $path = $request->file('shop_logo')->store('logos', 'public');
            $data['shop_logo'] = $path;
            Log::info("Logo toko baru berhasil disimpan di storage: " . $path, [
                'full_path' => storage_path('app/public/' . $path),
                'exists'    => file_exists(storage_path('app/public/' . $path))
            ]);
        }

        // 5. LOGIKA UPLOAD MP3 NOTIFIKASI
        if ($request->hasFile('payment_success_sound')) {
            $this->deleteOldFile('payment_success_sound');
            $path = $request->file('payment_success_sound')->store('audio', 'public');
            $data['payment_success_sound'] = $path;
            Log::info("Audio suara kasir baru berhasil disimpan di storage: " . $path, [
                'full_path' => storage_path('app/public/' . $path),
                'exists'    => file_exists(storage_path('app/public/' . $path))
            ]);
        }

        // 6. EKSEKUSI PENYIMPANAN
        try {
            DB::beginTransaction();
            
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? '']
                );
            }

            DB::commit();
            Log::info('Pengaturan toko berhasil diperbarui oleh user: ' . (auth()->user()->name ?? 'System'));

            // Refresh cache sistem agar perubahan langsung aktif di seluruh halaman
            Artisan::call('optimize:clear');

            return back()->with('success', 'Pengaturan Aplikasi & Identitas Toko Berhasil Diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui pengaturan toko: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal Memperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Menghapus file lama dari storage
     */
    private function deleteOldFile($key)
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting && $setting->value) {
            if (Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
                Log::info("File lama untuk setting '{$key}' berhasil dihapus: " . $setting->value);
            }
        }
    }
}
