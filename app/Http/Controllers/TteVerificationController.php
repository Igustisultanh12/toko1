<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TteVerificationController extends Controller
{
    /**
     * Halaman Verifikasi Keaslian Dokumen & Tanda Tangan Elektronik (TTE) untuk Faktur / Invoice
     */
    public function verify($transactionNumber)
    {
        $sale = Sale::with(['details.product', 'user'])
                    ->where('transaction_number', $transactionNumber)
                    ->firstOrFail();

        $shop = Setting::pluck('value', 'key')->all();

        $user = $sale->user;
        $signerTitle = ($user && !empty($user->alias))
            ? $user->alias
            : ($shop['cashier_officer_title'] ?? 'Petugas Kasir');
        $signerName = $sale->user->name ?? ($shop['cashier_officer_name'] ?? 'Petugas Kasir');

        // Pastikan timezone Asia/Jakarta (WIB)
        $signedAt = Carbon::parse($sale->created_at)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        // Generate Digital Signature Hash (TTE)
        $rawSignatureData = $sale->transaction_number . '|' . $sale->total_amount . '|' . $sale->created_at . '|' . $signerName;
        $tteHash = strtoupper(hash('sha256', $rawSignatureData));

        return view('reports.verify_tte', compact('sale', 'shop', 'signerTitle', 'signerName', 'signedAt', 'tteHash'));
    }

    /**
     * Halaman Verifikasi Keaslian Dokumen & Tanda Tangan Elektronik (TTE) untuk Seluruh Laporan PDF
     */
    public function verifyDocument(Request $request)
    {
        $docType = $request->query('type', 'document');
        $docNo = $request->query('doc_no', 'DOC-' . date('Ymd'));
        $signerName = base64_decode($request->query('signer', '')) ?: 'Administrator Toko';
        $signerTitle = base64_decode($request->query('title', '')) ?: 'Petugas Kasir';
        $timestamp = (int) $request->query('timestamp', time());

        // Format waktu eksplisit ke Asia/Jakarta (WIB)
        $signedAt = Carbon::createFromTimestamp($timestamp, 'Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        $docTitles = [
            'sales'   => 'Laporan Rekapitulasi Penjualan',
            'finance' => 'Laporan Keuangan & Arus Kas',
            'qris'    => 'Laporan Transaksi Digital QRIS (DOKU)',
            'stock'   => 'Laporan Stok & Inventaris Barang',
            'invoice' => 'Faktur / Nota Penjualan',
        ];

        $docTitle = $docTitles[$docType] ?? 'Dokumen Laporan Resmi';
        $shop = Setting::pluck('value', 'key')->all();

        $rawSignatureData = $docType . '|' . $docNo . '|' . $signerName . '|' . $signerTitle . '|' . $timestamp;
        $tteHash = strtoupper(hash('sha256', $rawSignatureData . config('app.key')));

        return view('reports.verify_report_tte', compact(
            'docType',
            'docNo',
            'docTitle',
            'signerName',
            'signerTitle',
            'signedAt',
            'tteHash',
            'shop'
        ));
    }
}