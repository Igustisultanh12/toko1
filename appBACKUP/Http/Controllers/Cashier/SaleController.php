<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman utama Point of Sale (POS) mode biasa.
     */
    public function index()
    {
        return view('cashier.pos');
    }

    /**
     * Menampilkan halaman utama Point of Sale (POS) mode PC.
     */
    public function pcIndex()
    {
        return view('cashier.pos-pc');
    }

    /**
     * Mencari produk berdasarkan barcode via AJAX.
     */
    public function checkProduct(Request $request)
    {
        $product = Product::where('barcode', $request->query('barcode'))->first();

        if ($product) {
            if ($product->stock <= 0) {
                return response()->json(['error' => 'Stok produk habis.'], 404);
            }
            return response()->json($product);
        }

        return response()->json(['error' => 'Produk tidak ditemukan.'], 404);
    }

    /**
     * Mencari produk berdasarkan nama via AJAX.
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
                            ->where('stock', '>', 0)
                            ->take(10)
                            ->get();
        return response()->json($products);
    }

    /**
     * Menyimpan transaksi baru dari POS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,qris',
            'amount_paid' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'total_amount' => $validated['total'],
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
                'amount_paid' => $validated['amount_paid'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception('Stok tidak mencukupi untuk produk: ' . $product->name);
                }

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price,
                    // PERBAIKAN: Mengubah 'discount_per_unit' menjadi 'discount_at_transaction'
                    'discount_at_transaction' => $product->discount_percent > 0 ? ($product->price * ($product->discount_percent / 100)) : 0,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil disimpan.', 'sale' => $sale], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan struk dalam format PDF untuk di-download.
     */
    public function showReceipt(Sale $sale)
    {
        $sale->load('saleDetails.product');
        $pdf = Pdf::loadView('cashier.receipt', compact('sale'));
        return $pdf->stream('struk-' . $sale->id . '.pdf');
    }

    /**
     * Menampilkan halaman struk yang dioptimalkan untuk printer thermal.
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load('saleDetails.product');
        return view('cashier.print-receipt', compact('sale'));
    }
}
