<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    /**
     * Cek apakah Turnstile aktif (Dinonaktifkan)
     */
    public static function isEnabled(): bool
    {
        return false;
    }

    /**
     * Dapatkan Site Key publik untuk widget frontend
     */
    public static function getSiteKey(): ?string
    {
        return null;
    }

    /**
     * Verifikasi token Turnstile (Otomatis lolos)
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        return true;
    }
}
