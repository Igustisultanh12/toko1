@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard Ringkasan')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto pb-10">

    {{-- 1. GOPAY POCKET STYLE BANNER & QUICK ACTIONS --}}
    <div class="bg-gradient-to-r from-[#00360D] via-[#005718] to-[#00880F] rounded-[2.5rem] p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20 relative overflow-hidden">
        {{-- Background Glow Accent --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#00AA13]/30 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center relative z-10">
            {{-- SALDO / OMZET UTAMA --}}
            <div class="lg:col-span-6 space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-emerald-200">
                        ⚡ Real-time Hari Ini
                    </span>
                    <span class="text-xs text-emerald-100 font-bold">{{ date('d F Y') }}</span>
                </div>
                
                <div>
                    <p class="text-xs text-emerald-200 uppercase font-black tracking-wider">Total Pendapatan Toko</p>
                    <h1 id="live-pendapatan-banner" class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mt-1 transition-all duration-300">
                        Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}
                    </h1>
                </div>

                <div class="flex items-center space-x-4 pt-1 text-xs text-emerald-100">
                    <span class="flex items-center font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 mr-1.5 animate-ping"></span>
                        <span id="live-transaksi-banner">{{ $transaksiHariIni ?? 0 }}</span> Transaksi Sukses
                    </span>
                    <span>•</span>
                    <span class="font-bold"><span id="live-terjual-banner">{{ $produkTerjualHariIni ?? 0 }}</span> Item Terjual</span>
                </div>
            </div>

            {{-- QUICK ACTION SQUIRCLES --}}
            <div class="lg:col-span-6">
                <div class="grid grid-cols-5 gap-2 bg-white/10 backdrop-blur-md p-3.5 rounded-3xl border border-white/15">
                    {{-- AKSI 1: KASIR POS --}}
                    <a href="{{ route('cashier.pos.index') }}" 
                       class="flex flex-col items-center justify-center p-1.5 rounded-2xl hover:bg-white/20 active:scale-90 transition group text-center">
                        <div class="w-11 h-11 bg-white text-[#00880F] rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-1.5 text-white">Kasir POS</span>
                    </a>

                    {{-- AKSI 2: TAMBAH PRODUK --}}
                    <a href="{{ route('admin.products.create') }}" 
                       class="flex flex-col items-center justify-center p-1.5 rounded-2xl hover:bg-white/20 active:scale-90 transition group text-center">
                        <div class="w-11 h-11 bg-[#00AED6] text-white rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-1.5 text-white">+ Produk</span>
                    </a>

                    {{-- AKSI 3: LAPORAN PENJUALAN --}}
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex flex-col items-center justify-center p-1.5 rounded-2xl hover:bg-white/20 active:scale-90 transition group text-center">
                        <div class="w-11 h-11 bg-[#EE2737] text-white rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 17v-2m3 2v-4m3 2v-6m-8 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-1.5 text-white">Penjualan</span>
                    </a>

                    {{-- AKSI 4: LAPORAN STOK --}}
                    <a href="{{ route('admin.reports.stock') }}" 
                       class="flex flex-col items-center justify-center p-1.5 rounded-2xl hover:bg-white/20 active:scale-90 transition group text-center">
                        <div class="w-11 h-11 bg-[#00AA13] text-white rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300 border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-1.5 text-white">Stok PDF</span>
                    </a>

                    {{-- AKSI 5: KEUANGAN --}}
                    <a href="{{ route('admin.reports.finance') }}" 
                       class="flex flex-col items-center justify-center p-1.5 rounded-2xl hover:bg-white/20 active:scale-90 transition group text-center">
                        <div class="w-11 h-11 bg-[#FFB800] text-gray-900 rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-1.5 text-white">Keuangan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- DOKUMENTASI & BUKU PANDUAN BANNER --}}
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-emerald-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h4 class="font-black text-gray-900 text-sm uppercase tracking-tight">Buku Panduan Lengkap Sistem (Manual Pengguna)</h4>
                <p class="text-xs text-gray-500 mt-0.5">Panduan menyeluruh 10 Bab mulai dari login, kasir POS, DOKU QRIS, laporan keuangan, hingga TTE digital.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
            <a href="{{ route('admin.manual.index') }}" 
               class="flex-1 sm:flex-none text-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-black text-xs uppercase tracking-wider transition">
                Baca Online
            </a>
            <a href="{{ route('admin.manual.pdf') }}" 
               class="flex-1 sm:flex-none text-center px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20 transition flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh PDF</span>
            </a>
        </div>
    </div>

    {{-- 2. KARTU STATISTIK GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- KARTU 1 --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100/80 hover:shadow-md hover:border-emerald-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Penjualan Hari Ini</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center group-hover:bg-[#00AA13] group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 id="live-pendapatan-card" class="text-2xl font-black text-gray-900 tracking-tight transition-all duration-300">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] font-bold text-emerald-600 mt-1 uppercase">● Kas Masuk Tercatat</p>
        </div>

        {{-- KARTU 2 --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100/80 hover:shadow-md hover:border-sky-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-50 text-[#00AED6] flex items-center justify-center group-hover:bg-[#00AED6] group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight"><span id="live-transaksi-card">{{ $transaksiHariIni ?? 0 }}</span> <span class="text-sm font-bold text-gray-400">Order</span></h3>
            <p class="text-[10px] font-bold text-sky-600 mt-1 uppercase">● Nota & Invoice Selesai</p>
        </div>

        {{-- KARTU 3 --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100/80 hover:shadow-md hover:border-amber-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Item Terjual</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-[#FA8B00] flex items-center justify-center group-hover:bg-[#FA8B00] group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight"><span id="live-terjual-card">{{ $produkTerjualHariIni ?? 0 }}</span> <span class="text-sm font-bold text-gray-400">Pcs</span></h3>
            <p class="text-[10px] font-bold text-amber-600 mt-1 uppercase">● Volume Barang Keluar</p>
        </div>

        {{-- KARTU 4 --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100/80 hover:shadow-md hover:border-emerald-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Katalog Produk</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center group-hover:bg-[#00AA13] group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight"><span id="live-produk-card">{{ $totalProduk ?? 0 }}</span> <span class="text-sm font-bold text-gray-400">SKU</span></h3>
            <p class="text-[10px] font-bold text-emerald-600 mt-1 uppercase">● Siap Dijual di Kasir</p>
        </div>
    </div>

    {{-- 3. GRAFIK & REVENUE BREAKDOWN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- GRAFIK ANALISIS 7 HARI TERAKHIR --}}
        <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Tren Pendapatan 7 Hari Terakhir</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Performa grafik transaksi toko</p>
                </div>
                <span class="px-3 py-1 bg-emerald-50 text-[#00880F] font-black text-[10px] rounded-full uppercase">
                    Aktif & Akurat
                </span>
            </div>
            <div class="h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- PRODUK TERLARIS BULAN INI --}}
        <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Top Produk Terlaris</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bulan {{ date('F Y') }}</p>
                    </div>
                    <span class="text-lg">🔥</span>
                </div>

                <div class="space-y-4">
                    @forelse($produkTerlaris ?? [] as $index => $top)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/80 hover:bg-emerald-50/50 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-800 font-black text-xs flex items-center justify-center shadow-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 leading-tight">{{ $top->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Terjual Cepat</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-[#00AA13] text-white rounded-full font-black text-xs">
                                {{ $top->total_terjual }} pcs
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400 text-xs italic">
                            Belum ada produk terjual bulan ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('admin.products.index') }}" 
               class="w-full text-center mt-6 py-3 bg-gray-100 hover:bg-[#00AA13] hover:text-white text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition-all">
                Kelola Stok Produk &rarr;
            </a>
        </div>
    </div>

    {{-- 4. RIWAYAT TRANSAKSI TERAKHIR --}}
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Transaksi Terakhir Masuk</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Aktivitas kasir & online store terbaru (Live Realtime)</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-black text-[#00880F] hover:underline uppercase tracking-wider">
                Lihat Semua &rarr;
            </a>
        </div>

        <div id="live-recent-transactions" class="divide-y divide-gray-100">
            @forelse($penjualanTerakhir ?? [] as $sale)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/60 rounded-2xl px-3 transition">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm border border-emerald-100 shrink-0">
                            🧾
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-black text-xs text-gray-900">{{ $sale->transaction_number }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $sale->payment_method === 'qris' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ strtoupper($sale->payment_method) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5">
                                Pelanggan: <b class="text-gray-800">{{ $sale->customer_name ?? 'Pelanggan Umum' }}</b> • Kasir: {{ $sale->user->name ?? 'Kasir' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end sm:space-x-6">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">
                            {{ $sale->created_at->diffForHumans() }}
                        </span>
                        <span class="text-base font-black text-emerald-600 tracking-tight">
                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400 text-xs italic">
                    Belum ada riwayat transaksi.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- SCRIPT CHART.JS GREEN THEME --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const rawData = {!! $chartData !!};
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Gradient Green
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(0, 170, 19, 0.35)');
    gradient.addColorStop(1, 'rgba(0, 170, 19, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: rawData.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: rawData.data,
                borderColor: '#00AA13',
                borderWidth: 3.5,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4,
                pointBackgroundColor: '#00AA13',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { family: 'Plus Jakarta Sans', weight: 'bold' },
                    bodyFont: { family: 'Plus Jakarta Sans', weight: 'bold' },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F3F4F6', drawBorder: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                        color: '#9CA3AF',
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + 'rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                        color: '#9CA3AF'
                    }
                }
            }
        }
    });

    // REALTIME LIVE DASHBOARD UPDATER (SETIAP 4 DETIK)
    let lastTransactionCount = {{ $transaksiHariIni ?? 0 }};

    async function pollLiveDashboard() {
        try {
            const res = await fetch("{{ route('dashboard.live-stats') }}");
            if (!res.ok) return;
            const data = await res.json();

            // Update Angka Omzet Banner & Card
            const bannerOmzet = document.getElementById('live-pendapatan-banner');
            const cardOmzet = document.getElementById('live-pendapatan-card');
            if (bannerOmzet) bannerOmzet.innerText = data.formatted_pendapatan_hari_ini;
            if (cardOmzet) cardOmzet.innerText = data.formatted_pendapatan_hari_ini;

            // Update Transaksi Count
            const bannerTrx = document.getElementById('live-transaksi-banner');
            const cardTrx = document.getElementById('live-transaksi-card');
            if (bannerTrx) bannerTrx.innerText = data.transaksi_hari_ini;
            if (cardTrx) cardTrx.innerText = data.transaksi_hari_ini;

            // Update Item Terjual
            const bannerTerjual = document.getElementById('live-terjual-banner');
            const cardTerjual = document.getElementById('live-terjual-card');
            if (bannerTerjual) bannerTerjual.innerText = data.item_terjual_hari_ini;
            if (cardTerjual) cardTerjual.innerText = data.item_terjual_hari_ini;

            // Update Total Produk
            const cardProduk = document.getElementById('live-produk-card');
            if (cardProduk) cardProduk.innerText = data.total_produk;

            // Jika ada transaksi baru yang masuk
            if (data.transaksi_hari_ini > lastTransactionCount) {
                lastTransactionCount = data.transaksi_hari_ini;
                if (typeof window.playNotificationSound === 'function') {
                    window.playNotificationSound('payment_success');
                }

                // Render ulang daftar transaksi terbaru secara live
                const container = document.getElementById('live-recent-transactions');
                if (container && data.transaksi_terakhir && data.transaksi_terakhir.length > 0) {
                    let html = '';
                    data.transaksi_terakhir.forEach(item => {
                        const isOnline = item.type === 'online';
                        const badgeBg = isOnline ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                        const icon = isOnline ? '🛒' : '🧾';

                        html += `
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/60 rounded-2xl px-3 transition animate-pulse">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm border border-emerald-100 shrink-0">
                                        ${icon}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono font-black text-xs text-gray-900">${item.number}</span>
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase ${badgeBg}">
                                                ${item.method}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 font-medium mt-0.5">
                                            Pelanggan: <b class="text-gray-800">${item.customer}</b> • Sumber: ${item.staff}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end sm:space-x-6">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">
                                        ${item.time_human}
                                    </span>
                                    <span class="text-base font-black text-emerald-600 tracking-tight">
                                        ${item.formatted_amount}
                                    </span>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                }
            }
        } catch (e) {
            console.log('Error polling dashboard stats:', e);
        }
    }

    setInterval(pollLiveDashboard, 4000);
});
</script>
@endsection
