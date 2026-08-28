<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen stok.
     */
    public function index()
    {
        return view('admin.stock.index');
    }

    /**
     * Mencari produk berdasarkan barcode untuk permintaan AJAX.
     */
    public function findProduct(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        if ($product) {
            // Jika produk ditemukan, kirim datanya sebagai JSON
            return response()->json($product);
        }

        // Jika tidak ditemukan, kirim error sebagai JSON
        return response()->json(['error' => 'Produk dengan barcode ini tidak ditemukan.'], 404);
    }

    /**
     * Mengupdate stok produk.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $product->increment('stock', $request->quantity); // Menambah stok

        return redirect()->route('admin.stock.index')
                         ->with('success', 'Stok untuk produk "' . $product->name . '" berhasil ditambahkan sebanyak ' . $request->quantity . '.');
    }
}
