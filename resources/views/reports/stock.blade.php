@extends('layouts.admin')

@section('title', 'Laporan Stok Barang')
@section('header_title', 'Pusat Laporan Stok & Inventaris')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-20">

    {{-- HEADER ACTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Rekapitulasi Stok & Valuasi Barang</h2>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Monitoring kuantitas fisik persediaan, peringatan stok menipis, dan total aset inventaris toko.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.reports.stock.pdf', request()->all()) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-[#EE2737] text-white hover:bg-rose-700 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                Cetak PDF Stok (Landscape)
            </a>
            <a href="{{ route('admin.reports.stock.excel', request()->all()) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-[#00AA13] text-white hover:bg-[#00880F] rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- FILTER FORM CARD --}}
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
        <form method="GET" action="{{ route('admin.reports.stock') }}" id="stockFilterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- 1. FILTER STATUS STOK --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Status Kuantitas Stok</label>
                    <select name="stock_status" 
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                        <option value="all" {{ ($filters['stock_status'] ?? 'all') == 'all' ? 'selected' : '' }}>📦 Semua Kondisi Stok</option>
                        <option value="available" {{ ($filters['stock_status'] ?? '') == 'available' ? 'selected' : '' }}>🟢 Stok Tersedia (> 10 pcs)</option>
                        <option value="low" {{ ($filters['stock_status'] ?? '') == 'low' ? 'selected' : '' }}>🟡 Stok Menipis (1 - 10 pcs)</option>
                        <option value="empty" {{ ($filters['stock_status'] ?? '') == 'empty' ? 'selected' : '' }}>🔴 Stok Habis (0 pcs)</option>
                    </select>
                </div>

                {{-- 2. URUTKAN DATA --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Urutan Data</label>
                    <select name="sort_by" 
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                        <option value="name_asc" {{ ($filters['sort_by'] ?? 'name_asc') == 'name_asc' ? 'selected' : '' }}>Nama Produk (A - Z)</option>
                        <option value="stock_desc" {{ ($filters['sort_by'] ?? '') == 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak ↓</option>
                        <option value="stock_asc" {{ ($filters['sort_by'] ?? '') == 'stock_asc' ? 'selected' : '' }}>Stok Paling Sedikit ↑</option>
                        <option value="price_desc" {{ ($filters['sort_by'] ?? '') == 'price_desc' ? 'selected' : '' }}>Harga Jual Tertinggi ↓</option>
                        <option value="price_asc" {{ ($filters['sort_by'] ?? '') == 'price_asc' ? 'selected' : '' }}>Harga Jual Terendah ↑</option>
                        <option value="latest" {{ ($filters['sort_by'] ?? '') == 'latest' ? 'selected' : '' }}>Produk Terbaru Masuk</option>
                    </select>
                </div>

                {{-- 3. PENCARIAN --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Cari Nama / Barcode</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama atau kode barcode..."
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                        <div class="absolute left-3.5 top-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL FILTER --}}
            <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-width="2"/></svg>
                        Filter Stok
                    </button>
                    <a href="{{ route('admin.reports.stock') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold text-xs uppercase transition-all">
                        Reset
                    </a>
                </div>
                <p class="text-xs text-gray-400 font-bold hidden sm:block">Filter Aktif: <span class="text-[#00880F]">{{ $statusLabel }}</span></p>
            </div>
        </form>
    </div>

    {{-- SUMMARY STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Valuasi --}}
        <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] p-6 rounded-[2rem] shadow-xl text-white">
            <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Estimasi Valuasi Aset</p>
            <h3 class="text-2xl font-black">Rp {{ number_format($stats['total_valuation'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-emerald-200 mt-1 font-bold">● Total Aset Fisik Toko</p>
        </div>

        {{-- Total Macam Produk --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Macam Produk</p>
            <h3 class="text-2xl font-black text-gray-900">{{ $stats['total_products'] ?? 0 }} <span class="text-xs text-gray-400">SKU</span></h3>
            <p class="text-[10px] text-sky-600 mt-1 font-bold">● Katalog Terdaftar</p>
        </div>

        {{-- Total Fisik Stok --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Kuantitas Fisik</p>
            <h3 class="text-2xl font-black text-[#00880F]">{{ number_format($stats['total_stock'] ?? 0, 0, ',', '.') }} <span class="text-xs text-gray-400">Pcs</span></h3>
            <p class="text-[10px] text-emerald-600 mt-1 font-bold">● Unit Siap Jual</p>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stok Menipis (<=10)</p>
            <h3 class="text-2xl font-black text-[#FFB800]">{{ $stats['low_stock_count'] ?? 0 }} <span class="text-xs text-gray-400">Item</span></h3>
            <p class="text-[10px] text-amber-600 mt-1 font-bold">● Perlu Restok</p>
        </div>

        {{-- Stok Habis --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stok Habis (0 pcs)</p>
            <h3 class="text-2xl font-black text-rose-600">{{ $stats['empty_stock_count'] ?? 0 }} <span class="text-xs text-gray-400">Item</span></h3>
            <p class="text-[10px] text-rose-600 mt-1 font-bold">● Segera Order</p>
        </div>
    </div>

    {{-- TABEL DATA STOK BARANG --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="font-black text-gray-900 uppercase text-sm">Daftar Stok & Inventaris Produk</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Rincian sisa stok fisik dan perkiraan total valuasi penjualan.</p>
            </div>
            <span class="px-3.5 py-1.5 bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[10px] font-black rounded-full uppercase">
                {{ $statusLabel }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">Kode Barcode / SKU</th>
                        <th class="p-5">Nama Produk</th>
                        <th class="p-5">Keterangan</th>
                        <th class="p-5 text-right">Harga Jual</th>
                        <th class="p-5 text-center">Sisa Stok</th>
                        <th class="p-5 text-right">Total Valuasi</th>
                        <th class="p-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $index => $product)
                    @php
                        $itemVal = $product->stock * $product->price;
                    @endphp
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">
                            {{ $products->firstItem() ? $products->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="p-5 font-mono font-bold text-gray-700">
                            <span class="bg-gray-100 px-2.5 py-1 rounded-lg text-[11px]">{{ $product->barcode ?: 'SKU-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="p-5">
                            <p class="font-black text-gray-900 uppercase">{{ $product->name }}</p>
                        </td>
                        <td class="p-5 text-gray-500 font-medium">
                            {{ $product->description ?: '-' }}
                        </td>
                        <td class="p-5 text-right font-black text-[#00880F]">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="p-5 text-center">
                            <span class="font-black text-sm {{ $product->stock <= 0 ? 'text-rose-600' : ($product->stock <= 10 ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ $product->stock }} <small class="text-[10px] font-bold text-gray-400">pcs</small>
                            </span>
                        </td>
                        <td class="p-5 text-right font-black text-gray-900">
                            Rp {{ number_format($itemVal, 0, ',', '.') }}
                        </td>
                        <td class="p-5 text-center whitespace-nowrap">
                            @if($product->stock <= 0)
                                <span class="bg-rose-50 text-rose-600 border border-rose-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Habis</span>
                            @elseif($product->stock <= 10)
                                <span class="bg-amber-50 text-amber-600 border border-amber-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Menipis</span>
                            @else
                                <span class="bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-16 text-center text-gray-300 font-bold uppercase text-xs italic">
                            Tidak ada data stok produk yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-6 border-t border-gray-100 bg-white">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
