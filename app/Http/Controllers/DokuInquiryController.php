<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class DokuInquiryController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        $invoiceNumber = $data['order']['invoice_number'] ?? '';

        // Ambil ID Sale dari format INV-{id}-TIMESTAMP
        $parts = explode('-', $invoiceNumber);
        $saleId = $parts[1] ?? null;

        $sale = Sale::find($saleId);

        if ($sale && $sale->status === 'pending') {
            // Jawaban Sukses ke DOKU
            return response()->json([
                'responseCode' => '0000',
                'responseMessage' => 'Success',
                'order' => [
                    'invoice_number' => $invoiceNumber,
                    'amount' => $sale->total_amount
                ]
            ]);
        }

        // Jawaban jika data tidak ditemukan atau sudah dibayar
        return response()->json([
            'responseCode' => '4004',
            'responseMessage' => 'Invoice not found or already processed'
        ]);
    }
}