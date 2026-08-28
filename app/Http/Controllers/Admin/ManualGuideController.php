<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ManualGuideController extends Controller
{
    /**
     * Tampilan Web Interaktif Buku Panduan
     */
    public function index()
    {
        return Inertia::render('Admin/Manual');
    }

    /**
     * Download Buku Panduan Lengkap dalam Format PDF (50+ Halaman)
     */
    public function exportPdf()
    {
        // Alokasikan memori dan batas waktu untuk rendering buku 50+ halaman
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $shop = Setting::pluck('value', 'key')->all();

        $pdf = Pdf::loadView('admin.manual.pdf', compact('shop'))
                  ->setPaper('a4', 'portrait')
                  ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $appName = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($shop['app_name'] ?? 'POS')) ?: 'POS';
        $fileName = 'Buku_Panduan_Lengkap_' . $appName . '_POS_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
