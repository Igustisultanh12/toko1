<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dari request
        $search = $request->input('search');

        // Mulai query ke model Product
        $productsQuery = Product::query();

        // Jika ada input pencarian, tambahkan kondisi 'where' ke query
        if ($search) {
            $productsQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhere('barcode', 'like', '%' . $search . '%');
        }

        // Ambil data produk dengan paginasi
        $products = $productsQuery->latest()->paginate(10);

        // --- PERUBAHAN BARU: Logika untuk Notifikasi Stok ---
        $lowStockThreshold = 5; // Tentukan ambang batas stok rendah di sini
        $lowStockProducts = Product::where('stock', '>', 0)->where('stock', '<=', $lowStockThreshold)->get();
        $outOfStockProducts = Product::where('stock', '=', 0)->get();
        // --- AKHIR PERUBAHAN ---

        // Kirim semua data yang dibutuhkan ke view
        return view('admin.products.index', compact('products', 'search', 'lowStockProducts', 'outOfStockProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Biasanya tidak digunakan untuk panel admin, tapi bisa diarahkan ke edit
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

        /**
     * Menampilkan halaman form untuk import produk.
     */
    public function showImportForm()
    {
        return view('admin.products.import');
    }

    /**
     * Memproses file Excel yang diunggah.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
             }
             return back()->with('error', 'Gagal mengimpor data: ' . implode(' | ', $errorMessages));
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diimpor!');
    }
    /**
     * Menangani permintaan untuk menambah stok dengan cepat.
     */
    public function quickStockUpdate(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        // Jika produk ditemukan
        if ($product) {
            $product->increment('stock', $request->quantity);
            return response()->json([
                'success' => true,
                'message' => "Stok untuk produk '{$product->name}' berhasil ditambahkan.",
                'new_stock' => $product->stock,
            ]);
        }

        // Jika produk tidak ditemukan
        return response()->json([
            'success' => false,
            'message' => 'Produk dengan barcode tersebut tidak ditemukan.',
        ], 404);
    }
}


