@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('header_title', 'Pusat Laporan Penjualan')

@section('content')
<div x-data="salesReport" class="max-w-7xl mx-auto space-y-6 pb-20">

    {{-- HEADER ACTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Rekapitulasi Penjualan</h2>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Klik pada Nomor Invoice atau Nama Pelanggan untuk melihat detail & mencetak faktur PDF.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.print.daily') }}" target="_blank" 
               class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-2xl font-bold text-xs transition-all">
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg>
                Struk Hari Ini
            </a>
            <a href="{{ route('admin.reports.stock') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-emerald-50 text-[#00880F] hover:bg-emerald-100 rounded-2xl font-bold text-xs transition-all border border-emerald-200/60">
                📦 Laporan Stok
            </a>
            <a href="{{ route('admin.reports.finance') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-2xl font-bold text-xs transition-all">
                Keuangan →
            </a>
        </div>
    </div>

    {{-- FILTER FORM CARD --}}
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFilterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- 1. PILIH PERIODE --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Pilih Periode</label>
                    <select name="period" id="periodSelect" onchange="togglePeriodInputs()" 
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <option value="daily" {{ ($filters['period'] ?? 'daily') == 'daily' ? 'selected' : '' }}>📅 Harian (Pilih Tanggal)</option>
                        <option value="monthly" {{ ($filters['period'] ?? '') == 'monthly' ? 'selected' : '' }}>📆 Bulanan (Pilih Bulan)</option>
                        <option value="quarterly" {{ in_array($filters['period'] ?? '', ['quarterly', '3_months']) ? 'selected' : '' }}>📊 3 Bulan (Triwulan)</option>
                        <option value="yearly" {{ ($filters['period'] ?? '') == 'yearly' ? 'selected' : '' }}>📈 Tahunan (Pilih Tahun)</option>
                    </select>
                </div>

                {{-- 2. DYNAMIC INPUT PERIODE --}}
                <div id="inputDaily" class="period-input">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="{{ $filters['date'] ?? date('Y-m-d') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <div id="inputMonthly" class="period-input" style="display: none;">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Bulan & Tahun</label>
                    <input type="month" name="month" value="{{ $filters['month'] ?? date('Y-m') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <div id="inputQuarterly" class="period-input grid grid-cols-2 gap-2" style="display: none;">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kuartal</label>
                        <select name="quarter" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-3 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            <option value="1" {{ ($filters['quarter'] ?? 1) == 1 ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                            <option value="2" {{ ($filters['quarter'] ?? 1) == 2 ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                            <option value="3" {{ ($filters['quarter'] ?? 1) == 3 ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                            <option value="4" {{ ($filters['quarter'] ?? 1) == 4 ? 'selected' : '' }}>Q4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tahun</label>
                        <input type="number" name="year" min="2020" max="2099" value="{{ $filters['year'] ?? date('Y') }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-3 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                </div>

                <div id="inputYearly" class="period-input" style="display: none;">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tahun</label>
                    <input type="number" name="year" min="2020" max="2099" value="{{ $filters['year'] ?? date('Y') }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                {{-- 3. FILTER METODE PEMBAYARAN --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Metode Bayar</label>
                    <select name="payment_method" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <option value="all" {{ ($filters['payment_method'] ?? 'all') == 'all' ? 'selected' : '' }}>Semua Metode</option>
                        <option value="cash" {{ ($filters['payment_method'] ?? '') == 'cash' ? 'selected' : '' }}>💵 Tunai (Cash)</option>
                        <option value="qris" {{ ($filters['payment_method'] ?? '') == 'qris' ? 'selected' : '' }}>📱 QRIS (DOKU)</option>
                    </select>
                </div>

                {{-- 4. CARI INVOICE / PELANGGAN --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Cari Invoice / Pelanggan</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik Invoice / Pelanggan..."
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <div class="absolute left-3.5 top-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL FILTER & EXPORT --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-width="2"/></svg>
                        Tampilkan Laporan
                    </button>
                    
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold text-xs uppercase transition-all">
                        Reset Filter
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    {{-- TOMBOL CETAK PDF LANDSCAPE --}}
                    <a href="{{ route('admin.reports.pdf', request()->all()) }}" 
                       class="flex items-center px-5 py-3 bg-[#EE2737] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 hover:bg-rose-700 transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                        Cetak PDF Rekap
                    </a>

                    {{-- TOMBOL EXCEL --}}
                    <a href="{{ route('admin.reports.excel', request()->all()) }}" 
                       class="flex items-center px-5 py-3 bg-[#00AA13] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 hover:bg-[#00880F] transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                        Export Excel
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- SUMMARY STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] p-6 rounded-[2rem] shadow-xl text-white">
            <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Total Pemasukan (Lunas)</p>
            <h3 class="text-2xl font-black">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-emerald-200 mt-1 font-bold">{{ $periodLabel }}</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Transaksi</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $stats['total_transactions'] ?? 0 }} <span class="text-xs text-gray-400">Struk</span></h3>
            <p class="text-[10px] text-sky-600 mt-1 font-bold">● Invoice Terbit</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Barang Terjual</p>
            <h3 class="text-2xl font-black text-[#00880F]">{{ $stats['total_items_sold'] ?? 0 }} <span class="text-xs text-gray-400">Pcs</span></h3>
            <p class="text-[10px] text-emerald-600 mt-1 font-bold">● Kuantitas Fisik</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pemasukan Tunai</p>
            <h3 class="text-xl font-black text-gray-800">Rp {{ number_format($stats['cash_revenue'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-amber-600 mt-1 font-bold">● Cash Kasir</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pemasukan QRIS</p>
            <h3 class="text-xl font-black text-[#00AED6]">Rp {{ number_format($stats['qris_revenue'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-cyan-600 mt-1 font-bold">● DOKU Gateway</p>
        </div>
    </div>

    {{-- TABEL DATA PENJUALAN --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-black text-gray-800 uppercase text-sm">Daftar Transaksi Penjualan</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Klik pada Nomor Invoice atau Nama Pelanggan untuk melihat detail & mencetak faktur.</p>
            </div>
            <span class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full uppercase">
                {{ $periodLabel }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">No. Invoice</th>
                        <th class="p-5">Waktu</th>
                        <th class="p-5">Nama Pelanggan</th>
                        <th class="p-5">Rincian Barang Terjual</th>
                        <th class="p-5 text-center">Total Qty</th>
                        <th class="p-5 text-center">Metode</th>
                        <th class="p-5 text-right">Total Nominal</th>
                        <th class="p-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    @forelse($transactions as $index => $trx)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">
                            {{ $transactions->firstItem() ? $transactions->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="p-5">
                            {{-- KLIK NOMOR INVOICE --}}
                            <button type="button" @click='openDetailModal(@json($trx))' 
                                    class="font-mono font-black text-indigo-600 hover:text-indigo-900 hover:underline flex items-center gap-1 group text-left">
                                <span>{{ $trx->transaction_number }}</span>
                                <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2"/></svg>
                            </button>
                        </td>
                        <td class="p-5 text-gray-500 font-medium whitespace-nowrap">
                            {{ $trx->created_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td class="p-5">
                            {{-- KLIK NAMA PELANGGAN --}}
                            <button type="button" @click='openDetailModal(@json($trx))' 
                                    class="font-bold text-gray-800 bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 px-3 py-1.5 rounded-xl transition-all text-left">
                                {{ $trx->customer_name ?? 'Pelanggan Umum' }}
                            </button>
                        </td>
                        <td class="p-5 min-w-[250px]">
                            @if($trx->details && $trx->details->count() > 0)
                                <ul class="space-y-1">
                                    @foreach($trx->details as $item)
                                        <li class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-gray-700">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                            <span class="text-gray-500 font-medium ml-2">
                                                {{ $item->quantity }}x @ Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400 italic text-[11px]">-</span>
                            @endif
                        </td>
                        <td class="p-5 text-center font-black text-gray-700">
                            {{ $trx->details ? $trx->details->sum('quantity') : 0 }}
                        </td>
                        <td class="p-5 text-center">
                            @if(strtolower($trx->payment_method) === 'qris')
                                <span class="bg-indigo-100 text-indigo-700 font-black px-2.5 py-1 rounded-full text-[9px] uppercase tracking-wider">QRIS</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 font-black px-2.5 py-1 rounded-full text-[9px] uppercase tracking-wider">CASH</span>
                            @endif
                        </td>
                        <td class="p-5 text-right font-black text-gray-900 whitespace-nowrap">
                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="p-5 text-center whitespace-nowrap">
                            @if($trx->payment_status == 'success')
                                <span class="bg-green-100 text-green-700 text-[9px] font-black px-3 py-1 rounded-full uppercase shadow-sm">Lunas</span>
                            @elseif($trx->payment_status == 'pending')
                                <span class="bg-orange-100 text-orange-700 text-[9px] font-black px-3 py-1 rounded-full uppercase shadow-sm">Pending</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-[9px] font-black px-3 py-1 rounded-full uppercase shadow-sm">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-12 text-center text-gray-400 font-medium">
                            Tidak ada transaksi yang cocok dengan filter periode yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($transactions->hasPages())
        <div class="p-6 border-t border-gray-50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL DETAIL TRANSAKSI --}}
    <div x-show="isDetailModalOpen" class="fixed inset-0 bg-indigo-950/80 backdrop-blur-sm flex items-center justify-center z-[150] p-4" x-cloak x-transition>
        <div class="bg-white rounded-[3rem] p-6 sm:p-8 w-full max-w-2xl text-left shadow-2xl border-t-[8px] border-indigo-600 max-h-[90vh] overflow-y-auto space-y-6">
            
            {{-- MODAL HEADER --}}
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div>
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2.5 py-1 rounded-lg">Detail Transaksi Penjualan</span>
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mt-1" x-text="selectedTrx.transaction_number"></h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="formatDate(selectedTrx.created_at)"></p>
                </div>
                <button @click="isDetailModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            {{-- INFORMASI TRANSAKSI --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Pelanggan:</span>
                    <p class="font-black text-gray-800 text-xs" x-text="selectedTrx.customer_name || 'Pelanggan Umum'"></p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Kasir / Petugas:</span>
                    <p class="font-bold text-gray-800 text-xs" x-text="selectedTrx.user ? selectedTrx.user.name : 'Kasir'"></p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Metode Bayar:</span>
                    <p class="font-black uppercase text-xs text-indigo-600" x-text="selectedTrx.payment_method"></p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Status Bayar:</span>
                    <p class="font-black uppercase text-xs" :class="selectedTrx.payment_status === 'success' ? 'text-green-600' : 'text-orange-500'" x-text="selectedTrx.payment_status === 'success' ? 'Lunas' : selectedTrx.payment_status"></p>
                </div>
            </div>

            {{-- TABEL RINCIAN BARANG --}}
            <div>
                <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Rincian Barang yang Dibeli</h4>
                <div class="border border-gray-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase">
                            <tr>
                                <th class="p-3 text-center">No</th>
                                <th class="p-3">Nama Produk</th>
                                <th class="p-3 text-right">Harga</th>
                                <th class="p-3 text-center">Qty</th>
                                <th class="p-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(item, idx) in (selectedTrx.details || [])" :key="idx">
                                <tr>
                                    <td class="p-3 text-center text-gray-400 font-bold" x-text="idx + 1"></td>
                                    <td class="p-3 font-bold text-gray-800" x-text="item.product ? item.product.name : 'Produk Dihapus'"></td>
                                    <td class="p-3 text-right text-gray-600" x-text="formatCurrency(item.price_at_transaction)"></td>
                                    <td class="p-3 text-center font-bold" x-text="item.quantity"></td>
                                    <td class="p-3 text-right font-black text-gray-900" x-text="formatCurrency((item.price_at_transaction - (item.discount_at_transaction || 0)) * item.quantity)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50/80 font-black">
                            <tr>
                                <td colspan="4" class="p-3 text-right text-gray-600 uppercase text-[11px]">Total Tagihan:</td>
                                <td class="p-3 text-right text-indigo-700 text-sm font-black" x-text="formatCurrency(selectedTrx.total_amount)"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="p-3 text-right text-gray-500 uppercase text-[10px]">Jumlah Dibayar:</td>
                                <td class="p-3 text-right text-gray-800 text-xs font-bold" x-text="formatCurrency(selectedTrx.amount_paid)"></td>
                            </tr>
                            <template x-if="selectedTrx.payment_method === 'cash'">
                                <tr>
                                    <td colspan="4" class="p-3 text-right text-gray-500 uppercase text-[10px]">Kembalian:</td>
                                    <td class="p-3 text-right text-emerald-600 text-xs font-bold" x-text="formatCurrency(Math.max(0, (selectedTrx.amount_paid || 0) - (selectedTrx.total_amount || 0)))"></td>
                                </tr>
                            </template>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- TOMBOL AKSI CETAK & WHATSAPP --}}
            <div class="space-y-3 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- CETAK FAKTUR PDF LANDSCAPE --}}
                        <a :href="'/admin/reports/invoice/' + selectedTrx.id + '/pdf'" target="_blank"
                           class="px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-100 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                            Faktur PDF
                        </a>

                        {{-- CETAK LABEL PENGIRIMAN PAKET --}}
                        <a :href="'/shipping-label/' + selectedTrx.id + '/pdf?recipient_name=' + encodeURIComponent(selectedTrx.customer_name || 'Pelanggan Umum') + '&recipient_phone=' + encodeURIComponent(waPhone || '')" target="_blank"
                           class="px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-100 transition flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2.5"/></svg>
                            Label Paket (Resi)
                        </a>

                        {{-- CETAK STRUK THERMAL KASIR --}}
                        <a :href="'/receipt/' + selectedTrx.id + '/print'" target="_blank"
                           class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold text-xs uppercase transition flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg>
                            Struk Thermal
                        </a>
                    </div>

                    <button @click="isDetailModalOpen = false" class="px-6 py-3 bg-gray-900 text-white font-bold text-xs rounded-2xl uppercase hover:bg-black transition">
                        Tutup
                    </button>
                </div>

                {{-- KIRIM VIA WHATSAPP --}}
                <div class="bg-green-50/80 p-3.5 rounded-2xl border border-green-200 text-left space-y-2">
                    <label class="block text-[10px] font-black text-green-800 uppercase tracking-wider">Kirim Struk & Link Faktur PDF ke WhatsApp</label>
                    <div class="flex gap-2">
                        <input type="tel" x-model="waPhone" placeholder="08xxxxxxxxxx"
                               class="flex-1 bg-white border border-green-300 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 outline-none focus:ring-2 focus:ring-green-500">
                        <button @click="sendWhatsAppReceipt()" 
                                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-black text-xs rounded-xl shadow transition flex items-center shrink-0">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                            Kirim WA
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
function togglePeriodInputs() {
    const period = document.getElementById('periodSelect').value;
    const allInputs = document.querySelectorAll('.period-input');
    allInputs.forEach(el => el.style.display = 'none');

    if (period === 'daily') {
        document.getElementById('inputDaily').style.display = 'block';
    } else if (period === 'monthly') {
        document.getElementById('inputMonthly').style.display = 'block';
    } else if (period === 'quarterly' || period === '3_months') {
        document.getElementById('inputQuarterly').style.display = 'grid';
    } else if (period === 'yearly') {
        document.getElementById('inputYearly').style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    togglePeriodInputs();
});

document.addEventListener('alpine:init', () => {
    Alpine.data('salesReport', () => ({
        isDetailModalOpen: false,
        selectedTrx: {},
        waPhone: '',
        shopName: @json($shop['shop_name'] ?? 'TOKO ANANDA'),
        shopAddress: @json($shop['shop_address'] ?? ''),
        shopPhone: @json($shop['shop_phone'] ?? ''),
        shopFooter: @json($shop['receipt_footer'] ?? 'Terima Kasih!'),

        openDetailModal(trx) {
            this.selectedTrx = trx;
            this.waPhone = '';
            this.isDetailModalOpen = true;
        },

        formatCurrency(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta' }) + ' - ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB';
        },

        async sendWhatsAppReceipt() {
            if (!this.waPhone || this.waPhone.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor WhatsApp Kosong',
                    text: 'Silakan masukkan nomor WhatsApp tujuan pengiriman struk.',
                    confirmButtonColor: '#00AA13'
                });
                return;
            }

            let phone = this.waPhone.replace(/[^0-9]/g, '');
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            } else if (!phone.startsWith('62')) {
                phone = '62' + phone;
            }

            const trx = this.selectedTrx;

            // Generate/Ambil tautan bertanda tangan digital yang aktif 24 jam
            let pdfUrl = `${window.location.origin}/invoice/${trx.transaction_number}/download`;
            try {
                const res = await fetch(`/invoice/${trx.transaction_number}/get-link`);
                const json = await res.json();
                if (json.signed_url) {
                    pdfUrl = json.signed_url;
                }
            } catch (e) {
                pdfUrl = `${window.location.origin}/invoice/${trx.transaction_number}/download`;
            }

            let itemsList = '';
            (trx.details || []).forEach((item, idx) => {
                const sub = (item.price_at_transaction - (item.discount_at_transaction || 0)) * item.quantity;
                itemsList += `${idx + 1}. *${item.product ? item.product.name : 'Produk'}*\n   ${item.quantity} pcs x Rp ${new Intl.NumberFormat('id-ID').format(item.price_at_transaction)} = Rp ${new Intl.NumberFormat('id-ID').format(sub)}\n`;
            });

            let message = `*${this.shopName.toUpperCase()}*\n`;
            if (this.shopAddress) message += `📍 _${this.shopAddress}_\n`;
            if (this.shopPhone && this.shopPhone !== '-') message += `📞 _Telp: ${this.shopPhone}_\n`;
            message += `================================\n`;
            message += `🧾 *STRUK PEMBELIAN*\n`;
            message += `No. Nota   : \`${trx.transaction_number}\`\n`;
            message += `Tanggal    : ${this.formatDate(trx.created_at)}\n`;
            message += `Pelanggan  : *${trx.customer_name || 'Pelanggan Umum'}*\n`;
            message += `Kasir      : ${trx.user ? trx.user.name : 'Kasir'}\n`;
            message += `--------------------------------\n`;
            message += `*DAFTAR BARANG:*\n${itemsList}`;
            message += `--------------------------------\n`;
            message += `💰 *TOTAL BELANJA : Rp ${new Intl.NumberFormat('id-ID').format(trx.total_amount)}*\n`;
            message += `Metode Bayar  : ${trx.payment_method.toUpperCase()}\n`;
            message += `Status Bayar  : ✅ *${trx.payment_status === 'success' ? 'LUNAS' : trx.payment_status.toUpperCase()}*\n`;
            message += `================================\n`;
            message += `📄 *UNDUH FAKTUR (PDF):*\n${pdfUrl}\n`;
            message += `_(Tautan berlaku selama 24 jam)_\n`;
            message += `================================\n`;
            message += `_${this.shopFooter}_`;

            const waUrl = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank');
        }
    }));
});
</script>
@endsection
