<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\DokuService; // Import Service Doku
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    protected $dokuService;

    // Inject DokuService untuk menangani pembayaran QRIS
    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    public function index()
    {
        return view('cashier.pos');
    }

    /**
     * AJAX: Cek produk berdasarkan barcode.
     */
    public function checkProduct(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();

        if ($product) {
            if ($product->stock > 0) {
                return response()->json($product);
            }
            return response()->json(['error' => 'Stok produk habis!'], 422);
        }
        return response()->json(['error' => 'Produk tidak ditemukan!'], 404);
    }

    /**
     * AJAX: Cari produk berdasarkan nama.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        if (!$query) return response()->json([]);

        $products = Product::where('name', 'LIKE', "%{$query}%")
                            ->where('stock', '>', 0)
                            ->limit(10)
                            ->get();

        return response()->json($products);
    }

    /**
     * Menyimpan transaksi & Integrasi Pembayaran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'total' => 'required|numeric',
            'amount_paid' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            // Hitung kembalian
            $changeAmount = $request->amount_paid - $request->total;

            // 1. Simpan data utama penjualan
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'total_amount' => $request->total,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'change_amount' => $changeAmount > 0 ? $changeAmount : 0,
            ]);

            // 2. Simpan detail item & Kurangi Stok (Pessimistic Locking)
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                // Menggunakan relasi 'details' sesuai di Model Sale
                $sale->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price,
                    'discount_at_transaction' => $product->discount_percent ?? 0,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // 3. Jika QRIS, buat URL pembayaran DOKU
            $paymentUrl = null;
            if ($request->payment_method === 'qris') {
                $paymentUrl = $this->dokuService->createPaymentUrl($sale);
                if (!$paymentUrl) {
                    throw new \Exception('Gagal menghubungi gateway pembayaran DOKU.');
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'sale' => $sale,
                'payment_url' => $paymentUrl
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate Receipt PDF (Metode tunggal untuk Print & Download).
     */
    public function generateReceipt(Sale $sale)
    {
        $sale->load(['details.product', 'user']);
        
        $pdf = Pdf::loadView('cashier.print-receipt', compact('sale'))
                  ->setPaper([0, 0, 164.41, 600], 'portrait');

        return $pdf->stream("Struk-SIKANDA-{$sale->id}.pdf");
    }
}