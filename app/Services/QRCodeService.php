<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QRCodeService
{
    /**
     * Generate Base64 Data URI PNG untuk QR Code agar DomPDF bisa me-render langsung tanpa koneksi saat compile
     */
    public static function generateBase64($data, $size = 150)
    {
        try {
            $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&margin=1&data=" . urlencode($data);
            
            $response = Http::timeout(3)->get($url);
            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Exception $e) {
            Log::warning("Gagal fetch QR dari API: " . $e->getMessage());
        }

        // Fallback: Gunakan quickchart.io jika qrserver gagal
        try {
            $url = "https://quickchart.io/qr?size={$size}&text=" . urlencode($data);
            $response = Http::timeout(3)->get($url);
            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Exception $e) {
            Log::warning("Gagal fetch QR fallback: " . $e->getMessage());
        }

        // Fallback offline SVG QR placeholder
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="#ffffff"/>
            <rect x="10" y="10" width="30" height="30" fill="#000000"/>
            <rect x="15" y="15" width="20" height="20" fill="#ffffff"/>
            <rect x="20" y="20" width="10" height="10" fill="#000000"/>
            <rect x="60" y="10" width="30" height="30" fill="#000000"/>
            <rect x="65" y="15" width="20" height="20" fill="#ffffff"/>
            <rect x="70" y="20" width="10" height="10" fill="#000000"/>
            <rect x="10" y="60" width="30" height="30" fill="#000000"/>
            <rect x="15" y="65" width="20" height="20" fill="#ffffff"/>
            <rect x="20" y="70" width="10" height="10" fill="#000000"/>
            <rect x="50" y="50" width="10" height="10" fill="#000000"/>
            <rect x="70" y="70" width="15" height="15" fill="#000000"/>
            <rect x="50" y="75" width="10" height="10" fill="#000000"/>
            <rect x="75" y="50" width="10" height="10" fill="#000000"/>
            <text x="50" y="96" font-size="6" text-anchor="middle" font-weight="bold" fill="#333">TTE SIKANDA</text>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
