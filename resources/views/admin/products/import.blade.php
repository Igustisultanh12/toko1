@extends('layouts.admin')

@section('title', 'Impor Produk Excel')
@section('header_title', 'Impor Data Produk Excel')

@section('content')
<div class="max-w-4xl mx-auto pb-10 space-y-6">

    {{-- HEADER BAR --}}
    <div class="flex items-center justify-between bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Impor Katalog Produk Massal</h3>
            <p class="text-xs text-gray-400 font-medium">Unggah file spreadsheet Excel (.xlsx / .xls) untuk mendaftarkan ratusan produk sekaligus.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-wider transition">
            &larr; Kembali
        </a>
    </div>

    {{-- ERROR NOTIFIKASI --}}
    @if (session('error'))
        <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm flex items-center">
            <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600 mr-4 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
            </div>
            <div>
                <p class="text-rose-700 font-black text-xs uppercase tracking-wider mb-0.5">Gagal Mengimpor File</p>
                <p class="text-rose-600 text-xs font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- UPLOAD CARD --}}
    <div class="bg-white p-8 sm:p-10 rounded-[3rem] shadow-sm border border-gray-100">
        <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-emerald-50 text-[#00880F] rounded-3xl flex items-center justify-center mx-auto mb-3 border border-emerald-200/60 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Pilih Berkas File Excel</h3>
                    <p class="text-xs text-gray-400 font-medium">Mendukung format file: .xlsx, .xls, .csv (Maksimal 5MB)</p>
                </div>

                <div class="relative">
                    <input type="file" name="file" id="file" required 
                           class="w-full px-6 py-10 bg-gray-50 border-2 border-dashed border-gray-200 hover:border-[#00AA13] rounded-3xl outline-none transition-all font-bold text-gray-500 cursor-pointer text-xs">
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-[10px] text-gray-400 font-medium italic">
                        *Pastikan kolom data sesuai petunjuk di bawah
                    </p>
                    <button type="submit" 
                            class="px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                        Proses Impor Produk
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- PANDUAN FORMAT EXCEL --}}
    <div class="bg-white p-8 sm:p-10 rounded-[3rem] shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-emerald-50 text-[#00880F] rounded-2xl flex items-center justify-center font-black border border-emerald-200/60">?</div>
            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Struktur Format Kolom Excel</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start space-x-3">
                <span class="w-6 h-6 bg-white rounded-lg flex items-center justify-center font-black text-[#00880F] text-xs shadow-sm shrink-0">1</span>
                <div>
                    <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">Kolom: <code class="text-[#00880F]">nama</code></p>
                    <p class="text-gray-500 font-medium text-[11px] mt-0.5">Wajib diisi (Nama Produk Anda).</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start space-x-3">
                <span class="w-6 h-6 bg-white rounded-lg flex items-center justify-center font-black text-[#00880F] text-xs shadow-sm shrink-0">2</span>
                <div>
                    <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">Kolom: <code class="text-[#00880F]">barcode</code></p>
                    <p class="text-gray-500 font-medium text-[11px] mt-0.5">Opsional (Harus unik jika diisi).</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start space-x-3">
                <span class="w-6 h-6 bg-white rounded-lg flex items-center justify-center font-black text-[#00880F] text-xs shadow-sm shrink-0">3</span>
                <div>
                    <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">Kolom: <code class="text-[#00880F]">harga</code></p>
                    <p class="text-gray-500 font-medium text-[11px] mt-0.5">Wajib (Hanya angka tanpa Rp atau titik).</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start space-x-3">
                <span class="w-6 h-6 bg-white rounded-lg flex items-center justify-center font-black text-[#00880F] text-xs shadow-sm shrink-0">4</span>
                <div>
                    <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">Kolom: <code class="text-[#00880F]">stok</code></p>
                    <p class="text-gray-500 font-medium text-[11px] mt-0.5">Wajib (Hanya angka jumlah stok fisik).</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200/60 flex items-center space-x-2 text-xs text-[#00880F] font-bold">
            <span>💡</span>
            <span>Gunakan header baris pertama dengan huruf kecil semua persis seperti di atas.</span>
        </div>
    </div>

</div>
@endsection
