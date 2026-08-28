<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    /**
     * Cek apakah Turnstile aktif dan kredensial lengkap
     */
    public static function isEnabled(): bool
    {
        $enabled = Setting::where('key', 'turnstile_enabled')->value('value');
        $siteKey = Setting::where('key', 'turnstile_site_key')->value('value');
        $secretKey = Setting::where('key', 'turnstile_secret_key')->value('value');

        return ($enabled === '1' && !empty($siteKey) && !empty($secretKey));
    }

    /**
     * Dapatkan Site Key publik untuk widget frontend
     */
    public static function getSiteKey(): ?string
    {
        return Setting::where('key', 'turnstile_site_key')->value('value');
    }

    /**
     * Verifikasi token Turnstile ke API Cloudflare
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        if (!self::isEnabled()) {
            return true; // Jika tidak diaktifkan di admin, otomatis lolos
        }

        if (empty($token)) {
            return false;
        }

        $secretKey = Setting::where('key', 'turnstile_secret_key')->value('value');

        try {
            $response = Http::asForm()->timeout(6)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            $data = $response->json();
            return !empty($data['success']) && $data['success'] === true;
        } catch (\Exception $e) {
            Log::warning('Cloudflare Turnstile Verification Warning: ' . $e->getMessage());
            return true; // fail-open jika API Cloudflare down agar transaksi tidak macet
        }
    }
}
