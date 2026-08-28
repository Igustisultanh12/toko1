@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('header_title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto pb-10 space-y-6" x-data="productForm()">

    {{-- HEADER BAR --}}
    <div class="flex items-center justify-between bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Formulir Data Produk</h3>
            <p class="text-xs text-gray-400 font-medium">Lengkapi detail informasi barang, harga jual, foto utama, dan galeri foto produk.</p>
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
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-12">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NAMA BARANG --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Produk / Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
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
                            <p class="text-[10px] text-gray-400 font-bold">Foto sampul utama yang akan muncul di etalase dan thumbnail kasir.</p>
                        </div>
                    </div>
                </div>

                {{-- FOTO GALERI TAMBAHAN (MULTI-IMAGE) --}}
                <div class="md:col-span-2 bg-emerald-50/40 p-6 rounded-3xl border-2 border-dashed border-emerald-200">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-[10px] font-black text-[#00661A] uppercase tracking-widest">
                            📸 Galeri Foto Tambahan (Bisa Pilih Lebih Dari 1 Foto)
                        </label>
                        <span class="text-[10px] font-bold text-gray-400">Opsional &bull; Multi-upload</span>
                    </div>

                    <div class="space-y-4">
                        <input type="file" name="gallery[]" multiple accept="image/*" @change="previewGallery($event)"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#00AA13] file:text-white hover:file:bg-[#00880F] file:cursor-pointer file:transition">
                        
                        {{-- PREVIEW GRID GALERI --}}
                        <template x-if="galleryPreviews.length > 0">
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 pt-2">
                                <template x-for="(img, idx) in galleryPreviews" :key="idx">
                                    <div class="relative group aspect-square rounded-2xl overflow-hidden border-2 border-emerald-300 shadow-sm">
                                        <img :src="img" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 right-1 px-1.5 py-0.5 bg-black/60 text-white rounded text-[8px] font-mono font-black" x-text="'#' + (idx + 1)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <p class="text-[10px] text-gray-400 font-medium">Pengunjung toko online dapat menggeser/melihat semua foto produk ini dalam carousel detail barang.</p>
                    </div>
                </div>

                {{-- KODE BARCODE --}}
                <div>
                    <label for="barcode" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Kode Barcode / SKU</label>
                    <div class="relative">
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', request('barcode')) }}" 
                                class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-mono font-bold text-gray-800 text-xs transition-all uppercase" placeholder="899...">
                        <button type="button" @click="generateRandomBarcode()" class="absolute right-3 top-3 px-3 py-1.5 bg-emerald-100 text-[#00880F] rounded-xl text-[10px] font-black uppercase hover:bg-[#00AA13] hover:text-white transition">
                            Auto
                        </button>
                    </div>
                </div>

                {{-- HARGA JUAL --}}
                <div>
                    <label for="price" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" 
                           class="w-full p-4 bg-emerald-50/50 border-2 border-emerald-200 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-[#00880F] text-base transition-all" placeholder="10000">
                </div>

                {{-- STOK AWAL --}}
                <div>
                    <label for="stock" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Stok Awal Barang <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 10) }}" required min="0" 
                           class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-gray-800 text-xs transition-all" placeholder="10">
                </div>

                {{-- DISKON --}}
                <div>
                    <label for="discount_percent" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Diskon (%)</label>
                    <input type="number" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', 0) }}" min="0" max="100" step="0.1" 
                           class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-gray-800 text-xs transition-all" placeholder="0">
                </div>

                {{-- DESKRIPSI LENGKAP BARANG --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Deskripsi Lengkap & Spesifikasi Barang</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-medium text-gray-800 text-xs transition-all" 
                              placeholder="Tuliskan deskripsi lengkap produk, rincian bahan, ukuran/size chart, petunjuk pemakaian, garansi, dll...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                    Simpan Produk & Foto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function productForm() {
    return {
        imagePreview: null,
        galleryPreviews: [],

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
                    this.imagePreview = null;
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        },

        previewGallery(event) {
            this.galleryPreviews = [];
            const files = event.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.galleryPreviews.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }
}
</script>
@endsection
