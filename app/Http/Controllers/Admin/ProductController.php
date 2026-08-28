<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $productsQuery = Product::query();

        if ($search) {
            $productsQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhere('barcode', 'like', '%' . $search . '%');
        }

        $products = $productsQuery->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'search'   => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|max:255|unique:products,barcode',
            'price'            => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'description'      => 'nullable|string|max:5000',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096', // Max 4MB
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $gFile) {
                if ($gFile->isValid()) {
                    $galleryPaths[] = $gFile->store('products/gallery', 'public');
                }
            }
            $validated['gallery'] = $galleryPaths;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk dan foto galeri berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'price'            => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'description'      => 'nullable|string|max:5000',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096', // Max 4MB
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
        ]);

        // 1. Update Foto Utama
        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // 2. Kelola Foto Galeri Tambahan
        $currentGallery = is_array($product->gallery) ? $product->gallery : [];

        // Hapus foto galeri yang ditandai untuk dihapus
        if ($request->has('deleted_gallery_images')) {
            $toDelete = (array) $request->input('deleted_gallery_images');
            $updatedGallery = [];
            foreach ($currentGallery as $path) {
                if (in_array($path, $toDelete)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    }
                } else {
                    $updatedGallery[] = $path;
                }
            }
            $currentGallery = $updatedGallery;
        }

        // Tambahkan foto galeri baru yang diunggah
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gFile) {
                if ($gFile->isValid()) {
                    $currentGallery[] = $gFile->store('products/gallery', 'public');
                }
            }
        }

        $validated['gallery'] = array_values($currentGallery);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk dan galeri foto berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        if (!empty($product->gallery) && is_array($product->gallery)) {
            foreach ($product->gallery as $gPath) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($gPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($gPath);
                }
            }
        }

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


