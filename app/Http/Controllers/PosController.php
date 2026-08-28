<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting; // Pastikan model Setting sudah ada
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * Tampilan Utama POS Kasir
     */
    public function index() 
    { 
        // Mengambil semua pengaturan terkait dari database
        $settings = Setting::whereIn('key', [
            'shop_name', 
            'shop_address', 
            'shop_phone', 
            'receipt_footer', 
            'is_voice_enabled', 
            'payment_success_sound'
        ])->pluck('value', 'key');

        $shop = [
            'shop_name'      => $settings['shop_name'] ?? 'TOKO ANANDA',
            'shop_address'   => $settings['shop_address'] ?? 'Jember, Jawa Timur',
            'shop_phone'     => $settings['shop_phone'] ?? '-',
            'receipt_footer' => $settings['receipt_footer'] ?? 'Terima Kasih Telah Berbelanja!',
            'is_voice_enabled' => (bool) ($settings['is_voice_enabled'] ?? false),
            // MENGIRIM URL AUDIO MP3 KE FRONTEND
            'payment_sound'  => !empty($settings['payment_success_sound']) 
                                ? asset('storage/' . $settings['payment_success_sound']) 
                                : null,
        ];

        return view('cashier.pos', compact('shop')); 
    }
    
    /**
     * Scan Barcode: Cari produk berdasarkan kode unik
     */
    public function checkProduct(Request $request) 
    {
        $product = Product::where('barcode', $request->barcode)->first();
        if ($product) {
            if ($product->stock <= 0) {
                return response()->json(['error' => 'Stok habis!'], 422);
            }
            return response()->json($product);
        }
        return response()->json(['error' => 'Produk tidak ditemukan'], 404);
    }

    /**
     * Live Search: Cari produk berdasarkan nama
     */
    public function search(Request $request) 
    {
        $query = $request->query('query');
        $products = Product::where('name', 'like', '%' . $query . '%')
            ->where('stock', '>', 0)
            ->take(10)
            ->get();
        return response()->json($products);
    }

    /**
     * Simpan Transaksi & Integrasi Pembayaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric',
            'payment_method' => 'required|in:cash,qris',
            'amount_paid' => 'required|numeric', 
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan data utama penjualan
            $sale = Sale::create([
                'transaction_number' => 'TRX-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'total_amount' => $request->total,
                'amount_paid' => $request->amount_paid,
                'change_amount' => max(0, $request->amount_paid - $request->total),
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'success' : 'pending',
            ]);

            // 2. Simpan detail item & Kurangi Stok secara aman (Locking)
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup.");
                }

                $sale->details()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // 3. Logika QRIS DOKU
            $qrString = null;
            if ($request->payment_method === 'qris') {
                $responseDoku = $this->dokuService->createPaymentUrl($sale);
                
                if (!$responseDoku) {
                    throw new \Exception('Gagal tersambung ke server DOKU.');
                }
                
                $qrString = $responseDoku; 
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'qr_string' => $qrString 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("POS Error: " . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Polling: Cek status pembayaran QRIS secara realtime
     */
    public function checkStatus($id)
    {
        $sale = Sale::findOrFail($id);
        return response()->json([
            'status' => $sale->status 
        ]);
    }

    /**
     * Cetak Struk (PDF / Web View)
     */
    public function generateReceipt($sale_id)
    {
        $sale = Sale::with(['details.product', 'user'])->findOrFail($sale_id);
        
        $settings = Setting::whereIn('key', ['shop_name', 'shop_address', 'shop_phone', 'receipt_footer'])->pluck('value', 'key');

        $shop = [
            'shop_name' => $settings['shop_name'] ?? 'TOKO ANANDA',
            'shop_address' => $settings['shop_address'] ?? '-',
            'shop_phone' => $settings['shop_phone'] ?? '-',
            'receipt_footer' => $settings['receipt_footer'] ?? 'Terima Kasih!',
        ];

        $pdf = Pdf::loadView('cashier.print-receipt', compact('sale', 'shop'));
        $pdf->setPaper([0, 0, 164.41, 600], 'portrait'); 

        return $pdf->stream("SIKANDA-{$sale->transaction_number}.pdf");
    }
}