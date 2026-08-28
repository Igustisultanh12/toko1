@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('header_title', 'Perbarui Data Produk')

@section('content')
<div class="max-w-4xl mx-auto pb-10 space-y-6" x-data="productForm('{{ $product->image_url }}', {{ json_encode($product->gallery_urls) }})">

    {{-- HEADER BAR --}}
    <div class="flex items-center justify-between bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Edit Data Produk: {{ $product->name }}</h3>
            <p class="text-xs text-gray-400 font-medium">Perbarui informasi harga, barcode, jumlah stok fisik, deskripsi, dan foto galeri barang.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-wider transition">
            &larr; Kembali
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700 font-bold space-y-1">
            <p class="font-black uppercase">Mohon perbaiki data input:</p>
            <ul class="list-disc list-inside font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="bg-white p-8 sm:p-10 rounded-[3rem] shadow-sm border border-gray-100">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NAMA BARANG --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Produk / Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required 
                           class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-gray-800 text-sm transition-all" placeholder="Contoh: Kemeja PDL Tactical Ripstop">
                </div>

                {{-- FOTO UTAMA PRODUK (COVER) --}}
                <div class="md:col-span-2 bg-gray-50 p-6 rounded-3xl border-2 border-dashed border-gray-200">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Foto Utama / Sampul Produk (Maks. 4MB)</label>
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-24 h-24 object-cover rounded-2xl border-2 border-[#00AA13] shadow-md">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="w-24 h-24 bg-gray-200/70 rounded-2xl flex items-center justify-center text-gray-400 text-3xl border border-gray-300">
                                🖼️
                            </div>
                        </template>
                        <div class="flex-1 text-center sm:text-left space-y-2">
                            <input type="file" name="image" id="image" accept="image/*" @change="previewImage($event)"
                                   class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#00AA13] file:text-white hover:file:bg-[#00880F] file:cursor-pointer file:transition">
                            <p class="text-[10px] text-gray-400 font-bold">Pilih file baru untuk mengganti foto utama. Biarkan kosong jika tidak ingin mengubah foto utama.</p>
                        </div>
                    </div>
                </div>

                {{-- FOTO GALERI TAMBAHAN --}}
                <div class="md:col-span-2 bg-emerald-50/40 p-6 rounded-3xl border-2 border-dashed border-emerald-200 space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-[10px] font-black text-[#00661A] uppercase tracking-widest">
                            📸 Galeri Foto Tambahan (Bisa Tambah Foto Baru)
                        </label>
                        <span class="text-[10px] font-bold text-gray-400">Multi-upload</span>
                    </div>

                    {{-- FOTO GALERI LAMA YANG SUDAH ADA DI DATABASE --}}
                    @if(!empty($product->gallery) && is_array($product->gallery) && count($product->gallery) > 0)
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider block mb-2">Foto Galeri Saat Ini:</span>
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                                @foreach($product->gallery as $gIdx => $gPath)
                                    <div class="relative group aspect-square rounded-2xl overflow-hidden border-2 border-gray-200 shadow-sm bg-white" id="gal-{{ $gIdx }}">
                                        <img src="{{ route('media.file', ['path' => $gPath]) }}" class="w-full h-full object-cover">
                                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white cursor-pointer transition p-1 text-center">
                                            <input type="checkbox" name="deleted_gallery_images[]" value="{{ $gPath }}" class="w-4 h-4 text-rose-600 rounded">
                                            <span class="text-[9px] font-black mt-1 text-rose-300">Centang Hapus</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[9px] text-gray-400 font-medium mt-1">💡 Arahkan kursor & centang foto di atas jika ingin menghapusnya saat disimpan.</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-500 tracking-wider block mb-1.5">Tambah Foto Galeri Baru:</span>
                        <input type="file" name="gallery[]" multiple accept="image/*" @change="previewNewGallery($event)"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#00AA13] file:text-white hover:file:bg-[#00880F] file:cursor-pointer file:transition">
                        
                        <template x-if="newGalleryPreviews.length > 0">
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 pt-3">
                                <template x-for="(img, idx) in newGalleryPreviews" :key="idx">
                                    <div class="relative aspect-square rounded-2xl overflow-hidden border-2 border-emerald-400 shadow-md">
                                        <img :src="img" class="w-full h-full object-cover">
                                        <span class="absolute top-1 right-1 px-1.5 py-0.5 bg-emerald-600 text-white rounded text-[8px] font-bold">Baru</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- KODE BARCODE --}}
                <div>
                    <label for="barcode" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Kode Barcode / SKU</label>
                    <div class="relative">
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-mono font-bold text-gray-800 text-xs transition-all uppercase" placeholder="899...">
                        <button type="button" @click="generateRandomBarcode()" class="absolute right-3 top-3 px-3 py-1.5 bg-emerald-100 text-[#00880F] rounded-xl text-[10px] font-black uppercase hover:bg-[#00AA13] hover:text-white transition">
                            Auto
                        </button>
                    </div>
                </div>

                {{-- HARGA JUAL --}}
                <div>
                    <label for="price" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0" 
                           class="w-full p-4 bg-emerald-50/50 border-2 border-emerald-200 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-[#00880F] text-base transition-all" placeholder="10000">
                </div>

                {{-- STOK FISIK --}}
                <div>
                    <label for="stock" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Stok Fisik Barang <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required min="0" 
                           class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-gray-800 text-xs transition-all" placeholder="10">
                </div>

                {{-- DISKON --}}
                <div>
                    <label for="discount_percent" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Diskon (%)</label>
                    <input type="number" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', $product->discount_percent) }}" min="0" max="100" step="0.1" 
                           class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-gray-800 text-xs transition-all" placeholder="0">
                </div>

                {{-- DESKRIPSI LENGKAP --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Deskripsi Lengkap & Spesifikasi Barang</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-medium text-gray-800 text-xs transition-all" 
                              placeholder="Tuliskan deskripsi lengkap produk, rincian bahan, ukuran/size chart, petunjuk pemakaian, dll...">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                    Perbarui Data Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function productForm(initialImage, initialGallery) {
    return {
        imagePreview: initialImage || null,
        newGalleryPreviews: [],

        generateRandomBarcode() {
            const rand = '899' + Math.floor(100000000 + Math.random() * 900000000);
            document.getElementById('barcode').value = rand;
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 4 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Melebihi Batas',
                        text: 'Ukuran foto maksimal adalah 4MB. Silakan pilih foto dengan ukuran lebih kecil.',
                        confirmButtonColor: '#00AA13'
                    });
                    event.target.value = '';
                    this.imagePreview = initialImage || null;
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = initialImage || null;
            }
        },

        previewNewGallery(event) {
            this.newGalleryPreviews = [];
            const files = event.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.newGalleryPreviews.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }
}
</script>
@endsection
