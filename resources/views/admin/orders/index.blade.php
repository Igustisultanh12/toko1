@extends('layouts.admin')

@section('title', 'Manajemen Pesanan Online')
@section('header_title', 'Pesanan Online & Pengiriman')

@section('content')
<div x-data="adminOrderManager()" class="max-w-7xl mx-auto space-y-6 pb-20">

    {{-- HEADER BAR --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-[#00AA13] animate-pulse"></span>
                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Manajemen Pesanan Online</h3>
            </div>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Klik nomor pesanan atau nama pembeli untuk melihat detail & mengubah alur proses pesanan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('order.index') }}" target="_blank"
               class="px-4 py-2.5 bg-emerald-50 text-[#00880F] hover:bg-emerald-100 rounded-2xl font-black text-xs uppercase tracking-wider transition border border-emerald-200/60 flex items-center space-x-1.5 shadow-sm">
                <span>🌐 Buka Toko Online</span>
            </a>
            <a href="{{ route('order.track.index') }}" target="_blank"
               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
                📦 Portal Lacak
            </a>
        </div>
    </div>

    {{-- STATS FILTER TABS (RAPI & BEBAS SEMRAWUT) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        {{-- TAB 1: SEMUA LUNAS --}}
        <a href="{{ route('admin.orders.index', ['status' => 'all']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'all' ? 'bg-[#004D13] text-white shadow-lg border-[#004D13]' : 'bg-white text-gray-700 border-gray-100 hover:border-emerald-300' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Semua Lunas</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['all'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Total Pesanan Sah</span>
        </a>

        {{-- TAB 2: PERLU KONFIRMASI --}}
        <a href="{{ route('admin.orders.index', ['status' => 'unconfirmed']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'unconfirmed' ? 'bg-blue-600 text-white shadow-lg border-blue-600' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50/50' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Perlu Konfirmasi</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['unconfirmed'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Menunggu Toko</span>
        </a>

        {{-- TAB 3: SEDANG DISIAPKAN --}}
        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'processing' ? 'bg-indigo-600 text-white shadow-lg border-indigo-600' : 'bg-white text-indigo-600 border-indigo-100 hover:bg-indigo-50/50' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Sedang Disiapkan</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['processing'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Siap Dipacking</span>
        </a>

        {{-- TAB 4: SEDANG DIKIRIM --}}
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'shipped' ? 'bg-emerald-600 text-white shadow-lg border-emerald-600' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50/50' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Sedang Dikirim</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['shipped'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Dalam Perjalanan</span>
        </a>

        {{-- TAB 5: SELESAI --}}
        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'completed' ? 'bg-gray-900 text-white shadow-lg border-gray-900' : 'bg-white text-gray-700 border-gray-100 hover:bg-gray-50' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Selesai / Diterima</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['completed'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Paket Tiba</span>
        </a>

        {{-- TAB 6: BELUM BAYAR (DRAFT) --}}
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
           class="p-4 rounded-3xl border transition-all text-center flex flex-col justify-between {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-lg border-amber-600' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50/50' }}">
            <span class="text-[10px] font-black uppercase tracking-wider opacity-80">Belum Bayar (Draft)</span>
            <h4 class="text-2xl font-black mt-2">{{ $counts['pending'] }}</h4>
            <span class="text-[9px] opacity-70 mt-1">Auto Hapus 24 Jam</span>
        </a>
    </div>

    {{-- TABEL PESANAN ONLINE UTAMA --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- SEARCH FORM & BAR --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="relative w-full sm:w-96">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nomor invoice, nama pembeli, WhatsApp, resi..." 
                       class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-2xl text-xs font-bold text-gray-800 outline-none focus:border-[#00AA13] shadow-sm">
                <div class="absolute left-4 top-3.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                </div>
            </form>
            
            <p class="text-[11px] text-gray-400 font-medium">
                💡 <span class="font-bold text-gray-600">Tips:</span> Klik tombol <b class="text-indigo-600">Invoice</b> atau <b class="text-[#00AA13]">Kelola</b> untuk membuka modal edit proses lengkap.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="p-4 pl-6">Invoice & Waktu</th>
                        <th class="p-4">Pembeli & Kontak</th>
                        <th class="p-4">Barang Belanjaan</th>
                        <th class="p-4 text-right">Total Bayar</th>
                        <th class="p-4 text-center">Status & Resi</th>
                        <th class="p-4 pr-6 text-center">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-emerald-50/30 transition group">
                            
                            {{-- INVOICE & WAKTU (KLIK UNTUK BUKA DETAIL) --}}
                            <td class="p-4 pl-6">
                                <button type="button" @click="openDetailModal({{ json_encode($order) }})"
                                        class="font-mono font-black text-xs text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-xl transition border border-indigo-200/60 inline-flex items-center space-x-1">
                                    <span>{{ $order->order_number }}</span>
                                    <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2.5"/></svg>
                                </button>
                                <p class="text-[10px] text-gray-400 font-bold mt-1">{{ $order->created_at->format('d/m/Y H:i') }} WIB</p>
                                <span class="text-[9px] font-black text-emerald-700 uppercase block">QRIS DOKU (LUNAS)</span>
                            </td>

                            {{-- PEMBELI (KLIK NAMA UNTUK BUKA DETAIL) --}}
                            <td class="p-4 max-w-xs">
                                <button type="button" @click="openDetailModal({{ json_encode($order) }})"
                                        class="font-black text-gray-900 text-xs uppercase hover:text-[#00AA13] transition text-left block">
                                    {{ $order->customer_name }}
                                </button>
                                <div class="flex items-center space-x-1.5 mt-0.5">
                                    <span class="text-[11px] text-gray-500 font-bold font-mono">{{ $order->customer_phone }}</span>
                                    <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-[10px] font-black" title="Chat WA">💬</a>
                                </div>
                                <p class="text-[10px] text-gray-400 font-medium truncate max-w-[200px] mt-0.5">{{ $order->customer_address }}</p>
                                @if($order->customer_notes)
                                    <span class="inline-block mt-1 text-[9px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        Catatan: {{ $order->customer_notes }}
                                    </span>
                                @endif
                            </td>

                            {{-- BARANG BELANJAAN --}}
                            <td class="p-4 min-w-[180px]">
                                <ul class="space-y-1">
                                    @foreach($order->items as $item)
                                        <li class="text-[11px] flex justify-between">
                                            <span class="font-bold text-gray-800 truncate max-w-[140px]">{{ $item->product_name }}</span>
                                            <span class="text-gray-400 font-black ml-2">{{ $item->quantity }}x</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- TOTAL BAYAR --}}
                            <td class="p-4 text-right font-black text-[#00880F] text-sm whitespace-nowrap">
                                {{ $order->formatted_total }}
                            </td>

                            {{-- STATUS & RESI --}}
                            <td class="p-4 text-center space-y-1.5 whitespace-nowrap">
                                <div>{!! $order->status_badge !!}</div>
                                
                                @if($order->latestComplaint)
                                    <div class="pt-0.5">
                                        <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-[9px] font-black uppercase border border-rose-300 animate-pulse inline-block">
                                            ⚠️ Ada Komplain: {{ $order->latestComplaint->status_label }}
                                        </span>
                                    </div>
                                @endif

                                @if($order->tracking_number)
                                    <div class="p-1.5 bg-emerald-50 border border-emerald-200 rounded-xl text-[10px] font-mono font-bold text-[#00661A]">
                                        Resi: <b>{{ $order->tracking_number }}</b><br>
                                        <span class="text-[9px] text-gray-500">({{ $order->courier }})</span>
                                    </div>
                                @endif
                            </td>

                            {{-- AKSI OPERASIONAL --}}
                            <td class="p-4 pr-6 text-center whitespace-nowrap">
                                <div class="flex flex-col gap-1.5 items-center justify-center">
                                    
                                    {{-- TOMBOL UTAMA: DETAIL & KELOLA PROSES --}}
                                    <button type="button" @click="openDetailModal({{ json_encode($order) }})"
                                            class="w-full px-4 py-2 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl text-[11px] font-black uppercase tracking-wider shadow-md shadow-emerald-500/20 transition flex items-center justify-center space-x-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                                        <span>Kelola & Proses</span>
                                    </button>

                                    {{-- TOMBOL CEPAT STRUK, LABEL, WA & LACAK --}}
                                    <div class="flex items-center gap-1 mt-1">
                                        <a href="{{ route('admin.orders.receipt-pdf', $order->id) }}" target="_blank"
                                           class="p-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-[10px] font-black uppercase transition" title="Cetak Struk Thermal (58/80mm)">
                                            🧾 Struk
                                        </a>

                                        <a href="{{ route('admin.orders.shipping-label', $order->id) }}" target="_blank"
                                           class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl text-[10px] font-black uppercase transition" title="Cetak Label Resi A6">
                                            🏷️ Label A6
                                        </a>

                                        <a href="{{ route('order.track', $order->order_number) }}" target="_blank"
                                           class="p-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-[10px] font-black uppercase transition" title="Buka Halaman Lacak">
                                            🔗 Lacak
                                        </a>
                                    </div>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-gray-400 font-bold uppercase text-xs italic">
                                Belum ada data pesanan online pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100 bg-white">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL DETAIL & KELOLA PROSES PESANAN LENGKAP --}}
    {{-- ======================================================== --}}
    <div x-show="isDetailModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-indigo-950/80 backdrop-blur-md p-3 sm:p-6" 
         x-cloak x-transition>
        <div class="min-h-full flex items-start justify-center py-4 sm:py-8">
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 max-w-3xl w-full shadow-2xl space-y-6 border border-gray-100 relative"
                 @click.outside="isDetailModalOpen = false">
            
                {{-- HEADER MODAL --}}
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-indigo-200 inline-block mb-1">
                        Detail & Kelola Proses Pesanan
                    </span>
                    <h3 class="text-xl font-black text-gray-900 font-mono tracking-tight" x-text="selectedOrder.order_number"></h3>
                    <p class="text-xs text-gray-400 font-medium">Dipesan pada <span x-text="selectedOrderFormattedDate"></span> • QRIS Lunas</p>
                </div>
                <button @click="isDetailModalOpen = false" class="w-10 h-10 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-500 font-black flex items-center justify-center transition">✕</button>
            </div>

            {{-- STEPPER STATUS VISUAL --}}
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alur Proses Pesanan:</p>
                <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-black uppercase">
                    <div class="p-2 rounded-xl border transition" :class="selectedOrder.status === 'paid' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white text-gray-500 border-gray-200'">
                        1. Dibayar
                    </div>
                    <div class="p-2 rounded-xl border transition" :class="selectedOrder.status === 'processing' ? 'bg-indigo-600 text-white border-indigo-600 shadow' : 'bg-white text-gray-500 border-gray-200'">
                        2. Disiapkan
                    </div>
                    <div class="p-2 rounded-xl border transition" :class="selectedOrder.status === 'shipped' ? 'bg-emerald-600 text-white border-emerald-600 shadow' : 'bg-white text-gray-500 border-gray-200'">
                        3. Dikirim
                    </div>
                    <div class="p-2 rounded-xl border transition" :class="selectedOrder.status === 'completed' ? 'bg-gray-900 text-white border-gray-900 shadow' : 'bg-white text-gray-500 border-gray-200'">
                        4. Selesai
                    </div>
                </div>
            </div>

            {{-- GRID DETAIL: INFORMASI PEMBELI & FORM UBAH STATUS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- KOLOM KIRI: DATA PEMBELI & BARANG --}}
                <div class="space-y-4">
                    
                    {{-- TUJUAN PENGIRIMAN --}}
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-xs space-y-1.5">
                        <p class="font-black text-gray-400 uppercase text-[10px] tracking-wider">[ TUJUAN PENGIRIMAN ]</p>
                        <p class="font-black text-gray-900 text-sm uppercase" x-text="selectedOrder.customer_name"></p>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold font-mono text-gray-700" x-text="selectedOrder.customer_phone"></span>
                            <a :href="'https://api.whatsapp.com/send?phone=' + (selectedOrder.customer_phone || '').replace(/[^0-9]/g, '')" target="_blank"
                               class="px-2 py-0.5 bg-emerald-100 text-[#00661A] rounded-lg font-black text-[10px] hover:bg-emerald-200">
                                💬 Hubungi WhatsApp
                            </a>
                        </div>
                        <p class="text-gray-600 leading-relaxed pt-1" x-text="selectedOrder.customer_address"></p>
                        <template x-if="selectedOrder.customer_notes">
                            <div class="p-2 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-[11px] font-medium mt-1">
                                📝 Catatan: <span x-text="selectedOrder.customer_notes"></span>
                            </div>
                        </template>
                    </div>

                    {{-- RINCIAN BARANG BELANJAAN --}}
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-xs space-y-2">
                        <p class="font-black text-gray-400 uppercase text-[10px] tracking-wider">[ BARANG YANG DIPESAN ]</p>
                        <div class="divide-y divide-gray-200/60 max-h-48 overflow-y-auto pr-1">
                            <template x-for="item in (selectedOrder.items || [])" :key="item.id">
                                <div class="py-2 flex justify-between items-center text-[11px]">
                                    <div>
                                        <p class="font-bold text-gray-900 uppercase" x-text="item.product_name"></p>
                                        <span class="text-[10px] text-gray-400" x-text="item.quantity + 'x @ Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                    </div>
                                    <span class="font-black text-gray-900" x-text="'Rp ' + Number(item.subtotal).toLocaleString('id-ID')"></span>
                                </div>
                            </template>
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex justify-between items-center font-black text-xs text-gray-900">
                            <span>TOTAL BAYAR (QRIS LUNAS):</span>
                            <span class="text-sm text-[#00880F]" x-text="'Rp ' + Number(selectedOrder.total_amount || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- KOTAK FITUR LINK KOMPLAIN PELANGGAN (HANYA UNTUK PESANAN YANG SUDAH LUNAS) --}}
                    <template x-if="selectedOrder.payment_status === 'paid'">
                        <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200/80 text-xs space-y-2">
                            <div class="flex items-center space-x-1.5 text-[#00880F] font-black uppercase text-[10px] tracking-wider">
                                <span>🛡️</span>
                                <span>Link Pengaduan / Komplain Pelanggan:</span>
                            </div>
                            <p class="text-[11px] text-gray-600">Berikan tautan ini jika pembeli mengajukan keluhan barang tidak sesuai/rusak:</p>
                            
                            <div class="flex flex-wrap gap-2 pt-1">
                                <button type="button" @click="copyComplaintLink()"
                                        class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-[#00880F] border border-emerald-300 rounded-xl font-black text-[10px] uppercase tracking-wider transition shadow-sm flex items-center space-x-1">
                                    <span>📋 Salin Link Komplain</span>
                                </button>

                                <a :href="'https://api.whatsapp.com/send?phone=' + (selectedOrder.customer_phone || '').replace(/[^0-9]/g, '') + '&text=' + encodeURIComponent('Halo ' + selectedOrder.customer_name + ', jika Anda memiliki kendala/keluhan barang pada pesanan ' + selectedOrder.order_number + ', silakan isi form pengaduan resmi kami di tautan berikut: ' + window.location.origin + '/komplain/' + selectedOrder.order_number)" 
                                   target="_blank"
                                   class="px-3.5 py-2 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl font-black text-[10px] uppercase tracking-wider transition shadow-sm flex items-center space-x-1">
                                    <span>💬 Kirim Link via WA</span>
                                </a>
                            </div>
                        </div>
                    </template>

                    {{-- JIKA PESANAN INI SUDAH ADA KOMPLAIN MASUK DARI PELANGGAN --}}
                    <template x-if="selectedOrder.latest_complaint">
                        <div class="p-4 bg-amber-50 rounded-2xl border-2 border-amber-300 text-xs space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="font-black text-amber-900 uppercase text-[11px]">⚠️ Komplain Masuk dari Pembeli:</span>
                                <span class="px-2.5 py-0.5 bg-amber-200 text-amber-900 rounded-full text-[9px] font-black uppercase" x-text="selectedOrder.latest_complaint.status"></span>
                            </div>
                            
                            <div class="text-[11px] space-y-1 text-gray-800 bg-white p-3 rounded-xl border border-amber-200">
                                <p><b>Alasan:</b> <span x-text="selectedOrder.latest_complaint.reason"></span></p>
                                <p><b>Solusi:</b> <span x-text="selectedOrder.latest_complaint.expected_solution"></span></p>
                                <p><b>Keterangan:</b> <span class="italic" x-text="'\"' + selectedOrder.latest_complaint.description + '\"'"></span></p>
                                
                                <div class="pt-2 flex flex-wrap gap-2">
                                    <template x-for="(pic, pidx) in (selectedOrder.latest_complaint.photos || [])" :key="pidx">
                                        <a :href="'/media-file?path=' + encodeURIComponent(pic)" target="_blank" class="w-12 h-12 rounded-lg overflow-hidden border border-gray-300 block hover:opacity-90">
                                            <img :src="'/media-file?path=' + encodeURIComponent(pic)" class="w-full h-full object-cover">
                                        </a>
                                    </template>
                                </div>
                                <template x-if="selectedOrder.latest_complaint.video">
                                    <div class="pt-1">
                                        <a :href="'/media-file?path=' + encodeURIComponent(selectedOrder.latest_complaint.video)" target="_blank" class="text-indigo-600 font-bold underline text-[10px]">
                                            🎥 Tonton Video Unboxing
                                        </a>
                                    </div>
                                </template>
                            </div>

                            {{-- FORM TANGGAPAN ADMIN --}}
                            <form :action="'/admin/complaints/' + selectedOrder.latest_complaint.id + '/status'" method="POST" class="space-y-2 pt-1">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase">Perbarui Status Komplain:</label>
                                    <select name="status" x-model="selectedOrder.latest_complaint.status" class="w-full p-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800">
                                        <option value="pending">Menunggu Ditinjau</option>
                                        <option value="reviewed">Sedang Ditinjau Admin</option>
                                        <option value="approved">Komplain Disetujui (Ganti Barang / Refund)</option>
                                        <option value="rejected">Komplain Ditolak</option>
                                        <option value="resolved">Selesai / Sudah Terselesaikan</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" name="admin_notes" x-model="selectedOrder.latest_complaint.admin_notes" placeholder="Catatan/balasan untuk pembeli..."
                                           class="w-full p-2 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-800">
                                </div>
                                <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-[10px] uppercase">
                                    Perbarui Status Komplain
                                </button>
                            </form>
                        </div>
                    </template>

                </div>

                {{-- KOLOM KANAN: FORM KELOLA & UBAH PROSES PESANAN --}}
                <div class="p-5 bg-white rounded-3xl border-2 border-emerald-500/30 shadow-sm space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <h4 class="font-black text-gray-900 uppercase text-xs tracking-wider">Ubah Status & Pengiriman</h4>
                    </div>

                    <form :action="'/admin/orders/' + selectedOrder.id + '/update-process'" method="POST" class="space-y-3.5">
                        @csrf
                        
                        {{-- STATUS SELEKSI --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Status Pesanan Saat Ini:</label>
                            <select name="status" x-model="selectedOrder.status" required
                                    class="w-full p-3 bg-gray-50 border-2 border-gray-200 rounded-2xl font-black text-xs text-gray-900 outline-none focus:border-[#00AA13]">
                                <option value="paid">⏳ 1. Menunggu Konfirmasi Toko (Paid)</option>
                                <option value="processing">📦 2. Sedang Disiapkan / Dipacking (Processing)</option>
                                <option value="shipped">🚚 3. Sedang Dikirim / Diperjalanan (Shipped)</option>
                                <option value="completed">✅ 4. Selesai / Pesanan Diterima (Completed)</option>
                                <option value="cancelled">❌ 5. Batalkan Pesanan & Kembalikan Stok (Cancelled)</option>
                            </select>
                        </div>

                        {{-- EKSPEDISI / KURIR --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Pilihan Ekspedisi / Kurir:</label>
                            <select name="courier" x-model="selectedOrder.courier"
                                    class="w-full p-3 bg-gray-50 border-2 border-gray-200 rounded-2xl font-bold text-xs text-gray-900 outline-none focus:border-[#00AA13]">
                                <option value="J&T Express">J&T Express</option>
                                <option value="JNE Reguler">JNE Reguler</option>
                                <option value="SiCepat Express">SiCepat Express</option>
                                <option value="Anteraja">Anteraja</option>
                                <option value="Pos Indonesia">Pos Indonesia</option>
                                <option value="Kurir Toko">Kurir Toko Pribadi</option>
                            </select>
                        </div>

                        {{-- NOMOR RESI PENGIRIMAN --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Nomor Resi Pengiriman (AWB):</label>
                            <input type="text" name="tracking_number" x-model="selectedOrder.tracking_number" placeholder="Contoh: JP8928374928"
                                   class="w-full p-3 bg-gray-50 border-2 border-gray-200 rounded-2xl font-mono font-black text-xs text-gray-900 outline-none focus:border-[#00AA13] uppercase">
                            <p class="text-[9px] text-gray-400 mt-1">Nomor resi langsung tersinkronisasi ke portal lacak pembeli.</p>
                        </div>

                        {{-- CATATAN TAMBAHAN --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Catatan Tambahan:</label>
                            <input type="text" name="customer_notes" x-model="selectedOrder.customer_notes" placeholder="Catatan internal / pengiriman..."
                                   class="w-full p-3 bg-gray-50 border-2 border-gray-200 rounded-2xl font-medium text-xs text-gray-800 outline-none focus:border-[#00AA13]">
                        </div>

                        {{-- TOMBOL SUBMIT SIMPAN PERUBAHAN --}}
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3.5 bg-[#00AA13] hover:bg-[#00880F] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition">
                                💾 Simpan & Perbarui Status Pesanan
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- FOOTER MODAL: DOKUMEN CETAK & SHORTCUTS --}}
            <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a :href="'/admin/orders/' + selectedOrder.id + '/receipt-pdf'" target="_blank"
                       class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-black text-xs uppercase tracking-wider transition border border-indigo-200 flex items-center space-x-1.5">
                        <span>🧾 Cetak Struk Thermal PDF</span>
                    </a>
                    
                    <a :href="'/admin/orders/' + selectedOrder.id + '/shipping-label'" target="_blank"
                       class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl font-black text-xs uppercase tracking-wider transition border border-amber-200 flex items-center space-x-1.5">
                        <span>🏷️ Cetak Label Resi A6</span>
                    </a>

                    <a :href="'/lacak/' + selectedOrder.order_number" target="_blank"
                       class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wider transition">
                        🔗 Halaman Lacak Pembeli
                    </a>
                </div>

                <button type="button" @click="isDetailModalOpen = false" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xs uppercase tracking-wider rounded-xl transition">
                    Tutup
                </button>
            </div>

            </div>
        </div>
    </div>

</div>

<script>
function adminOrderManager() {
    return {
        isDetailModalOpen: false,
        selectedOrder: {},
        selectedOrderFormattedDate: '',
        lastOrderCount: {{ $counts['unconfirmed'] ?? 0 }},

        init() {
            // Polling realtime pesanan baru setiap 4 detik
            setInterval(async () => {
                try {
                    const res = await fetch("{{ route('orders.realtime-check') }}");
                    if (res.ok) {
                        const data = await res.json();
                        if (data.count > this.lastOrderCount) {
                            this.lastOrderCount = data.count;
                            if (typeof window.playNotificationSound === 'function') {
                                window.playNotificationSound('order_new');
                            }
                            Swal.fire({
                                icon: 'info',
                                title: 'Pesanan Online Baru Masuk!',
                                text: 'Ada pesanan online baru yang baru saja dibayar via QRIS.',
                                confirmButtonText: 'Perbarui Halaman',
                                confirmButtonColor: '#00AA13',
                                showCancelButton: true,
                                cancelButtonText: 'Nanti'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    }
                } catch(e) {}
            }, 4000);
        },

        openDetailModal(order) {
            this.selectedOrder = Object.assign({}, order);
            const date = new Date(order.created_at);
            this.selectedOrderFormattedDate = date.toLocaleDateString('id-ID', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            }) + ' WIB';
            this.isDetailModalOpen = true;
        },

        copyComplaintLink() {
            if (this.selectedOrder && this.selectedOrder.order_number) {
                const link = window.location.origin + '/komplain/' + this.selectedOrder.order_number;
                navigator.clipboard.writeText(link);
                Swal.fire({
                    icon: 'success',
                    title: 'Link Komplain Tersalin!',
                    text: 'Tautan form komplain khusus pesanan ' + this.selectedOrder.order_number + ' berhasil disalin ke clipboard.',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
    }
}
</script>
@endsection
