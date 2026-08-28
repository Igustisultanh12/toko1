@extends('online.layout')

@section('title', 'Struk Digital Pesanan ' . $order->order_number)

@section('content')
<style>
    @media print {
        header, nav, footer, .no-print, .btn-action-area {
            display: none !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
        }
        .receipt-card {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
    .thermal-paper {
        background: #ffffff;
        position: relative;
        font-family: 'Courier New', Courier, monospace;
    }
    .thermal-paper::before, .thermal-paper::after {
        content: "";
        display: block;
        position: absolute;
        left: 0;
        right: 0;
        height: 10px;
        background-size: 16px 16px;
    }
    .thermal-paper::before {
        top: -10px;
        background-image: radial-gradient(circle, transparent, transparent 50%, #ffffff 50%, #ffffff 100%);
    }
    .thermal-paper::after {
        bottom: -10px;
        background-image: radial-gradient(circle, #ffffff, #ffffff 50%, transparent 50%, transparent 100%);
    }
</style>

<div class="max-w-xl mx-auto px-4 py-8 space-y-6">

    {{-- KARTU STATUS SUKSES --}}
    <div class="no-print bg-emerald-500 text-white rounded-3xl p-6 shadow-xl flex items-center space-x-4">
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
            ✔
        </div>
        <div>
            <h3 class="font-black uppercase text-sm tracking-wide">Pesanan Online Berhasil Dibuat!</h3>
            <p class="text-xs text-emerald-100 font-medium">Pembayaran QRIS lunas. Struk thermal lengkap Anda telah diterbitkan.</p>
        </div>
    </div>

    {{-- KARTU STRUK THERMAL LENGKAP --}}
    <div class="receipt-card thermal-paper bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-2xl text-gray-900 space-y-4">
        
        {{-- HEADER IDENTITAS TOKO --}}
        <div class="text-center space-y-1 pb-2">
            @if(!empty($shop['shop_logo']))
                <img src="{{ route('media.file', ['path' => $shop['shop_logo']]) }}" class="h-12 mx-auto mb-2 object-contain grayscale">
            @endif
            <h2 class="text-lg font-black tracking-tight uppercase">{{ $shop['shop_name'] ?? 'TOKO BERKAH' }}</h2>
            @if(!empty($shop['app_tagline']))
                <p class="text-[11px] text-gray-600 font-bold uppercase">{{ $shop['app_tagline'] }}</p>
            @endif
            <p class="text-[10px] text-gray-600 leading-tight">{{ $shop['shop_address'] ?? 'Jember, Jawa Timur' }}</p>
            <p class="text-[10px] text-gray-600 font-bold">Telp/WA: {{ $shop['shop_phone'] ?? ($shop['phone'] ?? '-') }}</p>
            
            <div class="pt-2">
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-[10px] font-black uppercase tracking-wider border border-gray-300 rounded-md inline-block">
                    🧾 STRUK PESANAN ONLINE
                </span>
            </div>
        </div>

        <div class="border-t-2 border-dashed border-gray-300"></div>

        {{-- METADATA PESANAN --}}
        <div class="text-[11px] space-y-1 font-bold">
            <div class="flex justify-between">
                <span class="text-gray-600">NO. PESANAN:</span>
                <span class="font-mono text-gray-900">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">WAKTU ORDER:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">METODE BAYAR:</span>
                <span class="text-emerald-700 font-black">QRIS DOKU (LUNAS)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">STATUS SAAT INI:</span>
                <span class="font-black text-indigo-700 uppercase">{{ $order->status_label }}</span>
            </div>
        </div>

        <div class="border-t-2 border-dashed border-gray-300"></div>

        {{-- DATA TUJUAN PENGIRIMAN LENGKAP --}}
        <div class="text-[11px] space-y-1.5 font-medium">
            <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">[ DATA TUJUAN PENGIRIMAN ]</p>
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">Penerima</span>
                <span class="font-bold text-gray-900">: {{ $order->customer_name }}</span>
            </div>
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">Telepon / WA</span>
                <span class="font-bold">: {{ $order->customer_phone }}</span>
            </div>
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">Alamat</span>
                <span class="leading-snug">: {{ $order->customer_address }}</span>
            </div>
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">Ekspedisi</span>
                <span class="font-bold text-emerald-800">: {{ strtoupper($order->courier) }}</span>
            </div>
            @if(!empty($order->tracking_number))
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">No. Resi</span>
                <span class="font-black font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">: {{ $order->tracking_number }}</span>
            </div>
            @endif
            @if(!empty($order->customer_notes))
            <div class="flex">
                <span class="w-24 text-gray-600 shrink-0">Catatan</span>
                <span class="italic text-gray-700">: {{ $order->customer_notes }}</span>
            </div>
            @endif
        </div>

        <div class="border-t-2 border-dashed border-gray-300"></div>

        {{-- DAFTAR BARANG YANG DIBELI --}}
        <div class="space-y-2.5">
            <p class="font-black text-gray-800 uppercase text-[10px] tracking-wider">[ RINCIAN ITEM BELANJA ]</p>
            <div class="space-y-2">
                @foreach($order->items as $item)
                    <div class="text-[11px]">
                        <p class="font-black text-gray-900 uppercase leading-snug">{{ $item->product_name }}</p>
                        <div class="flex justify-between text-gray-600">
                            <span>{{ $item->quantity }} pcs x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t-2 border-dashed border-gray-300"></div>

        {{-- TOTAL TAGIHAN --}}
        <div class="text-[11px] space-y-1 font-bold">
            <div class="flex justify-between">
                <span class="text-gray-600">SUBTOTAL BARANG:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">ONGKOS KIRIM:</span>
                <span class="text-emerald-700">GRATIS / TERMASUK</span>
            </div>
            <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-200">
                <span>TOTAL BAYAR:</span>
                <span class="text-[#00AA13]">{{ $order->formatted_total }}</span>
            </div>
            <div class="flex justify-between text-[10px] text-gray-500 font-bold">
                <span>STATUS PEMBAYARAN:</span>
                <span class="text-emerald-700 uppercase">LUNAS (QRIS)</span>
            </div>
        </div>

        <div class="border-t-4 border-double border-gray-400"></div>

        {{-- FOOTER & SCAN QR CODE LACAK --}}
        <div class="text-center space-y-2 pt-1">
            <p class="font-black uppercase text-xs">TERIMA KASIH TELAH BERBELANJA!</p>
            <p class="text-[10px] text-gray-600">{{ $shop['receipt_footer'] ?? 'Simpan struk ini sebagai bukti pembelian yang sah.' }}</p>
            
            <div class="pt-2 inline-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode(route('order.track', $order->order_number)) }}" 
                     class="w-24 h-24 mx-auto border p-1 rounded-lg shadow-sm" alt="QR Lacak">
                <p class="text-[9px] text-gray-500 font-bold mt-1">Scan QR Code untuk Lacak Pengiriman</p>
                <p class="text-[9px] font-mono text-gray-400">{{ route('order.track', $order->order_number) }}</p>
            </div>
            
            <p class="text-[8px] text-gray-400 font-medium pt-2">Dicetak otomatis oleh Sistem Logistik Toko Online</p>
        </div>

    </div>

    {{-- TOMBOL AKSI CETAK & LACAK --}}
    <div class="no-print space-y-3 pt-2">
        <a href="{{ route('order.receipt.pdf', $order->order_number) }}" target="_blank"
           class="w-full py-3.5 bg-gray-900 hover:bg-black text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Unduh PDF Struk Thermal</span>
        </a>

        <a href="{{ route('order.track', $order->order_number) }}" 
           class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>📦 Lacak Realtime Status Pengiriman Paket</span>
        </a>

        @if(!empty($shop['shop_phone']))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop['shop_phone']) }}?text={{ urlencode('Halo Admin ' . ($shop['shop_name'] ?? 'Toko') . ', saya telah memesan dengan No. Pesanan: ' . $order->order_number . '. Mohon segera diproses.') }}" target="_blank"
           class="w-full py-3.5 bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs uppercase tracking-wider rounded-2xl border border-gray-200 transition flex items-center justify-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
            <span>Hubungi Admin via WhatsApp</span>
        </a>
        @endif
    </div>

</div>
@endsection
