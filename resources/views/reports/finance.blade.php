@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Arus Kas')
@section('header_title', 'Pusat Laporan Keuangan & Kas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-20" x-data="{ activeTab: 'all' }">

    {{-- HEADER ACTION & 2 TOMBOL CETAK PDF (TUNAI & QRIS) --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-6 sm:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Laporan Keuangan & Arus Kas Toko</h2>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Monitoring penerimaan kas tunai dan digital QRIS DOKU (otomatis dipotong 0.7% MDR per transaksi).</p>
        </div>
        
        {{-- TOMBOL CETAK SPESIFIK TUNAI & QRIS --}}
        <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
            {{-- 1. CETAK LAPORAN TUNAI --}}
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'cash'])) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 bg-[#00AA13] text-white hover:bg-[#00880F] rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2.5"/></svg>
                Cetak PDF Tunai
            </a>

            {{-- 2. CETAK LAPORAN QRIS --}}
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'qris'])) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 bg-[#00AED6] text-white hover:bg-cyan-600 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-cyan-500/25 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke-width="2.5"/></svg>
                Cetak PDF QRIS
            </a>

            {{-- 3. CETAK REKAP KESELURUHAN --}}
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'all'])) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 bg-[#EE2737] text-white hover:bg-rose-700 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                PDF Rekap
            </a>

            {{-- 4. EXPORT EXCEL --}}
            <a href="{{ route('admin.reports.finance.excel', request()->all()) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition-all border border-gray-200">
                Excel
            </a>
        </div>
    </div>

    {{-- FILTER FORM CARD --}}
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
        <form method="GET" action="{{ route('admin.reports.finance') }}" id="financeFilterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- PILIH PERIODE --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Periode</label>
                    <select name="period" id="periodSelectFinance" onchange="togglePeriodInputsFinance()" 
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                        <option value="all" {{ ($filters['period'] ?? 'all') == 'all' ? 'selected' : '' }}>🌟 Semua Periode (Seluruh Waktu)</option>
                        <option value="daily" {{ ($filters['period'] ?? '') == 'daily' ? 'selected' : '' }}>📅 Harian (Pilih Tanggal)</option>
                        <option value="monthly" {{ ($filters['period'] ?? '') == 'monthly' ? 'selected' : '' }}>📆 Bulanan (Pilih Bulan)</option>
                        <option value="quarterly" {{ in_array($filters['period'] ?? '', ['quarterly', '3_months']) ? 'selected' : '' }}>📊 3 Bulan (Triwulan)</option>
                        <option value="yearly" {{ ($filters['period'] ?? '') == 'yearly' ? 'selected' : '' }}>📈 Tahunan (Pilih Tahun)</option>
                    </select>
                </div>

                {{-- DYNAMIC INPUT PERIODE --}}
                <div id="inputDailyFinance" class="period-input-fin" style="{{ ($filters['period'] ?? 'all') == 'daily' ? '' : 'display: none;' }}">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $filters['date'] ?? date('Y-m-d') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                </div>

                <div id="inputMonthlyFinance" class="period-input-fin" style="{{ ($filters['period'] ?? '') == 'monthly' ? '' : 'display: none;' }}">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Bulan & Tahun</label>
                    <input type="month" name="month" value="{{ $filters['month'] ?? date('Y-m') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                </div>

                <div id="inputQuarterlyFinance" class="period-input-fin grid grid-cols-2 gap-2" style="{{ in_array($filters['period'] ?? '', ['quarterly', '3_months']) ? '' : 'display: none;' }}">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Kuartal</label>
                        <select name="quarter" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-3 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                            <option value="1" {{ ($filters['quarter'] ?? 1) == 1 ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                            <option value="2" {{ ($filters['quarter'] ?? 1) == 2 ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                            <option value="3" {{ ($filters['quarter'] ?? 1) == 3 ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                            <option value="4" {{ ($filters['quarter'] ?? 1) == 4 ? 'selected' : '' }}>Q4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
                        <input type="number" name="year" min="2020" max="2099" value="{{ $filters['year'] ?? date('Y') }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-3 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                    </div>
                </div>

                <div id="inputYearlyFinance" class="period-input-fin" style="{{ ($filters['period'] ?? '') == 'yearly' ? '' : 'display: none;' }}">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
                    <input type="number" name="year" min="2020" max="2099" value="{{ $filters['year'] ?? date('Y') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                </div>

                {{-- FILTER METODE BAYAR --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Filter Kanal Kas</label>
                    <select name="payment_method" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:border-[#00AA13] focus:bg-white transition-all outline-none">
                        <option value="all" {{ ($filters['payment_method'] ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kanal (Tunai & QRIS)</option>
                        <option value="cash" {{ ($filters['payment_method'] ?? '') == 'cash' ? 'selected' : '' }}>💵 Kas Tunai (Cash)</option>
                        <option value="qris" {{ ($filters['payment_method'] ?? '') == 'qris' ? 'selected' : '' }}>📱 Digital QRIS (DOKU)</option>
                    </select>
                </div>

                {{-- CARI INVOICE / PELANGGAN --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Cari Transaksi</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Invoice / Pelanggan..."
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
                        Tampilkan Data
                    </button>
                    
                    <a href="{{ route('admin.reports.finance') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold text-xs uppercase transition-all">
                        Reset
                    </a>
                </div>
                <p class="text-xs text-gray-400 font-bold hidden sm:block">Periode Aktif: <span class="text-[#00880F]">{{ $periodLabel }}</span></p>
            </div>
        </form>
    </div>

    {{-- STATISTIK KEUANGAN GOPAY STYLE DENGAN PERHITUNGAN BERSIH QRIS (POTONGAN 0.7%) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Pemasukan Bersih --}}
        <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] p-6 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Total Pemasukan Bersih (Lunas)</p>
                <h3 class="text-3xl font-black">Rp {{ number_format($stats['total_income'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-200 mt-1 font-bold">{{ $stats['total_transactions'] ?? 0 }} Transaksi (Kas + QRIS Net)</p>
            </div>
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'all'])) }}" class="mt-4 text-[10px] font-black uppercase text-emerald-200 hover:text-white bg-black/20 hover:bg-black/30 px-3 py-1.5 rounded-xl text-center transition">
                Cetak PDF Rekap &rarr;
            </a>
        </div>

        {{-- Kas Tunai --}}
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:border-emerald-300 transition">
            <div>
                <div class="flex justify-between items-center mb-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penerimaan Tunai (Cash)</p>
                    <span class="px-2 py-0.5 bg-emerald-50 text-[#00880F] rounded-full text-[9px] font-black uppercase">{{ $stats['cash_count'] ?? 0 }} Struk</span>
                </div>
                <h3 class="text-2xl font-black text-[#00880F]">Rp {{ number_format($stats['cash_income'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-600 mt-1 font-bold">● 100% Uang Fisik Kasir</p>
            </div>
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'cash'])) }}" class="mt-4 text-[10px] font-black uppercase text-[#00880F] hover:bg-[#00AA13] hover:text-white bg-emerald-50 px-3 py-1.5 rounded-xl text-center transition border border-emerald-200/60">
                📄 Cetak Laporan Tunai PDF &rarr;
            </a>
        </div>

        {{-- Digital QRIS (Dipotong 0.7%) --}}
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:border-cyan-300 transition">
            <div>
                <div class="flex justify-between items-center mb-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penerimaan Bersih QRIS</p>
                    <span class="px-2 py-0.5 bg-cyan-50 text-[#00AED6] rounded-full text-[9px] font-black uppercase">{{ $stats['qris_count'] ?? 0 }} Struk</span>
                </div>
                <h3 class="text-2xl font-black text-[#00AED6]">Rp {{ number_format($stats['qris_net'] ?? $stats['qris_income'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-bold">
                    Bruto: Rp {{ number_format($stats['qris_gross'] ?? 0, 0, ',', '.') }} 
                    <span class="text-rose-500">(DOKU -0.7%: Rp {{ number_format($stats['qris_fee'] ?? 0, 0, ',', '.') }})</span>
                </p>
            </div>
            <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'qris'])) }}" class="mt-4 text-[10px] font-black uppercase text-[#00AED6] hover:bg-[#00AED6] hover:text-white bg-cyan-50 px-3 py-1.5 rounded-xl text-center transition border border-cyan-200/60">
                📱 Cetak Laporan QRIS PDF &rarr;
            </a>
        </div>

        {{-- Pending --}}
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dana Belum Selesai (Pending)</p>
                <h3 class="text-2xl font-black text-[#FFB800]">Rp {{ number_format($stats['pending_income'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-amber-600 mt-1 font-bold">● {{ $stats['pending_count'] ?? 0 }} Transaksi Tertunda</p>
            </div>
            <div class="mt-4 text-[10px] font-bold text-gray-400 bg-gray-50 px-3 py-1.5 rounded-xl text-center">
                Menunggu Pembayaran Pembeli
            </div>
        </div>
    </div>

    {{-- TAB NAVIGASI & TABEL DATA TRANSAKSI ARUS KAS --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        {{-- TAB HEADER --}}
        <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex flex-wrap items-center gap-2">
                {{-- TAB 1: SEMUA --}}
                <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'bg-[#00AA13] text-white shadow-md shadow-emerald-500/20' : 'bg-white text-gray-600 hover:bg-gray-100'"
                        class="px-5 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider transition-all flex items-center">
                    <span>Semua Transaksi</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'">{{ $transactions->total() }}</span>
                </button>

                {{-- TAB 2: TUNAI --}}
                <button @click="activeTab = 'cash'" 
                        :class="activeTab === 'cash' ? 'bg-[#00AA13] text-white shadow-md shadow-emerald-500/20' : 'bg-white text-[#00880F] hover:bg-emerald-50'"
                        class="px-5 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider transition-all flex items-center border border-emerald-200/60">
                    <span>💵 Kas Tunai (Cash)</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'cash' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'">{{ $stats['cash_count'] ?? count($cashTransactions) }}</span>
                </button>

                {{-- TAB 3: QRIS --}}
                <button @click="activeTab = 'qris'" 
                        :class="activeTab === 'qris' ? 'bg-[#00AED6] text-white shadow-md shadow-cyan-500/20' : 'bg-white text-[#00AED6] hover:bg-cyan-50'"
                        class="px-5 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider transition-all flex items-center border border-cyan-200/60">
                    <span>📱 Digital QRIS (DOKU 0.7%)</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'qris' ? 'bg-white/20 text-white' : 'bg-cyan-100 text-cyan-800'">{{ $stats['qris_count'] ?? count($qrisTransactions) }}</span>
                </button>
            </div>

            <span class="px-3.5 py-1.5 bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[10px] font-black rounded-full uppercase">
                {{ $periodLabel }}
            </span>
        </div>

        {{-- KONTEN TAB 1: SEMUA TRANSAKSI --}}
        <div x-show="activeTab === 'all'" class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">No. Invoice</th>
                        <th class="p-5">Tanggal & Waktu</th>
                        <th class="p-5">Pelanggan</th>
                        <th class="p-5 text-center">Kanal Kas</th>
                        <th class="p-5 text-right">Nominal Masuk (Netto)</th>
                        <th class="p-5 text-center">Status Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $index => $trx)
                    @php
                        $isQris = strtolower($trx->payment_method) === 'qris';
                        $gross = $trx->total_amount;
                        $fee = $isQris ? round($gross * 0.007, 0) : 0;
                        $net = $gross - $fee;
                    @endphp
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">
                            {{ $transactions->firstItem() ? $transactions->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="p-5 font-mono font-bold text-gray-800">
                            {{ $trx->transaction_number }}
                        </td>
                        <td class="p-5 text-gray-500 font-medium whitespace-nowrap">
                            {{ $trx->created_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td class="p-5 font-bold text-gray-700">
                            {{ $trx->customer_name ?? 'Pelanggan Umum' }}
                        </td>
                        <td class="p-5 text-center">
                            @if($isQris)
                                <span class="bg-cyan-50 text-[#00AED6] border border-cyan-200/60 font-black px-3 py-1 rounded-full text-[9px] uppercase tracking-wider">QRIS DOKU</span>
                            @else
                                <span class="bg-emerald-50 text-[#00880F] border border-emerald-200/60 font-black px-3 py-1 rounded-full text-[9px] uppercase tracking-wider">KAS TUNAI</span>
                            @endif
                        </td>
                        <td class="p-5 text-right font-black text-gray-900 whitespace-nowrap">
                            @if($isQris)
                                <span>Rp {{ number_format($net, 0, ',', '.') }}</span>
                                <span class="block text-[10px] text-gray-400 font-medium">(Bruto: {{ number_format($gross, 0, ',', '.') }} - DOKU: {{ number_format($fee, 0, ',', '.') }})</span>
                            @else
                                Rp {{ number_format($net, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="p-5 text-center whitespace-nowrap">
                            @if($trx->payment_status == 'success')
                                <span class="bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Lunas</span>
                            @elseif($trx->payment_status == 'pending')
                                <span class="bg-amber-50 text-amber-600 border border-amber-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Pending</span>
                            @else
                                <span class="bg-rose-50 text-rose-600 border border-rose-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-16 text-center text-gray-300 font-bold uppercase text-xs italic">
                            Tidak ada transaksi keuangan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($transactions->hasPages())
            <div class="p-6 border-t border-gray-100 bg-white">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>

        {{-- KONTEN TAB 2: KAS TUNAI SAJA --}}
        <div x-show="activeTab === 'cash'" class="overflow-x-auto" x-cloak>
            <div class="p-4 bg-emerald-50/60 border-b border-emerald-100 flex justify-between items-center">
                <p class="text-xs font-black text-[#00880F] uppercase">Daftar Transaksi Pembayaran Kas Tunai (100% Uang Fisik)</p>
                <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'cash'])) }}" 
                   class="px-4 py-2 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl font-black text-[11px] uppercase tracking-wider shadow-sm transition">
                    📄 Cetak PDF Laporan Tunai
                </a>
            </div>
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">No. Invoice</th>
                        <th class="p-5">Tanggal & Waktu</th>
                        <th class="p-5">Pelanggan</th>
                        <th class="p-5 text-right">Nominal Tunai</th>
                        <th class="p-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($cashTransactions as $index => $trx)
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                        <td class="p-5 font-mono font-bold text-gray-800">{{ $trx->transaction_number }}</td>
                        <td class="p-5 text-gray-500 font-medium whitespace-nowrap">{{ $trx->created_at->format('d M Y, H:i') }} WIB</td>
                        <td class="p-5 font-bold text-gray-700">{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                        <td class="p-5 text-right font-black text-[#00880F] whitespace-nowrap">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td class="p-5 text-center whitespace-nowrap">
                            <span class="bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Lunas</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-16 text-center text-gray-300 font-bold uppercase text-xs italic">
                            Belum ada transaksi penerimaan kas tunai pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KONTEN TAB 3: DIGITAL QRIS SAJA (DIPOTONG DOKU 0.7%) --}}
        <div x-show="activeTab === 'qris'" class="overflow-x-auto" x-cloak>
            <div class="p-4 bg-cyan-50/60 border-b border-cyan-100 flex justify-between items-center">
                <p class="text-xs font-black text-[#00AED6] uppercase">Daftar Transaksi Digital QRIS (DOKU Potongan 0.7% MDR)</p>
                <a href="{{ route('admin.reports.finance.pdf', array_merge(request()->all(), ['payment_method' => 'qris'])) }}" 
                   class="px-4 py-2 bg-[#00AED6] hover:bg-cyan-600 text-white rounded-xl font-black text-[11px] uppercase tracking-wider shadow-sm transition">
                    📱 Cetak PDF Laporan QRIS
                </a>
            </div>
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">No. Invoice</th>
                        <th class="p-5">Tanggal & Waktu</th>
                        <th class="p-5">Pelanggan</th>
                        <th class="p-5 text-right">Nominal Bruto</th>
                        <th class="p-5 text-right">Biaya DOKU (0.7%)</th>
                        <th class="p-5 text-right">Bersih Rekening</th>
                        <th class="p-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($qrisTransactions as $index => $trx)
                    @php
                        $gross = $trx->total_amount;
                        $fee = round($gross * 0.007, 0);
                        $net = $gross - $fee;
                    @endphp
                    <tr class="hover:bg-cyan-50/30 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                        <td class="p-5 font-mono font-bold text-gray-800">{{ $trx->transaction_number }}</td>
                        <td class="p-5 text-gray-500 font-medium whitespace-nowrap">{{ $trx->created_at->format('d M Y, H:i') }} WIB</td>
                        <td class="p-5 font-bold text-gray-700">{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                        <td class="p-5 text-right font-bold text-gray-700 whitespace-nowrap">Rp {{ number_format($gross, 0, ',', '.') }}</td>
                        <td class="p-5 text-right font-bold text-rose-600 whitespace-nowrap">- Rp {{ number_format($fee, 0, ',', '.') }}</td>
                        <td class="p-5 text-right font-black text-[#00880F] whitespace-nowrap">Rp {{ number_format($net, 0, ',', '.') }}</td>
                        <td class="p-5 text-center whitespace-nowrap">
                            @if($trx->payment_status == 'success')
                                <span class="bg-emerald-50 text-[#00880F] border border-emerald-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Lunas</span>
                            @elseif($trx->payment_status == 'pending')
                                <span class="bg-amber-50 text-amber-600 border border-amber-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Pending</span>
                            @else
                                <span class="bg-rose-50 text-rose-600 border border-rose-200/60 text-[9px] font-black px-3 py-1 rounded-full uppercase">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-16 text-center text-gray-300 font-bold uppercase text-xs italic">
                            Belum ada transaksi pembayaran digital QRIS pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function togglePeriodInputsFinance() {
    const period = document.getElementById('periodSelectFinance').value;
    const allInputs = document.querySelectorAll('.period-input-fin');
    allInputs.forEach(el => el.style.display = 'none');

    if (period === 'daily') {
        document.getElementById('inputDailyFinance').style.display = 'block';
    } else if (period === 'monthly') {
        document.getElementById('inputMonthlyFinance').style.display = 'block';
    } else if (period === 'quarterly' || period === '3_months') {
        document.getElementById('inputQuarterlyFinance').style.display = 'grid';
    } else if (period === 'yearly') {
        document.getElementById('inputYearlyFinance').style.display = 'block';
    }
}
</script>
@endsection
