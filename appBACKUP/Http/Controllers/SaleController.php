<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index() { return view('cashier.pos'); }

    public function checkProductByBarcode(Request $request) {
        $product = Product::where('barcode', $request->barcode)->first();
        if ($product) {
            if ($product->stock > 0) return response()->json($product);
            return response()->json(['error' => 'Stok produk habis!'], 404);
        }
        return response()->json(['error' => 'Produk tidak ditemukan!'], 404);
    }

    public function store(Request $request) {
        $request->validate(['items' => 'required|array|min:1', 'items.*.id' => 'required|exists:products,id', 'items.*.quantity' => 'required|integer|min:1']);
        DB::beginTransaction();
        try {
            $sale = Sale::create(['user_id' => Auth::id(), 'total_amount' => $request->total, 'payment_method' => 'cash']);
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if ($product->stock < $item['quantity']) throw new \Exception('Stok produk ' . $product->name . ' tidak mencukupi.');
                $sale->details()->create(['product_id' => $product->id, 'quantity' => $item['quantity'], 'price_at_transaction' => $product->price, 'discount_at_transaction' => $product->discount_percent]);
                $product->decrement('stock', $item['quantity']);
            }
            DB::commit();
            return response()->json(['success' => 'Transaksi berhasil disimpan!', 'sale_id' => $sale->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaksi gagal: ' . $e->getMessage()], 500);
        }
    }
}
