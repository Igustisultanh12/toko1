@extends('online.layout')

@section('title', 'Lacak Pesanan Online')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10 space-y-8">

    {{-- HEADER PENCARIAN --}}
    <div class="text-center space-y-3">
        <div class="w-14 h-14 bg-emerald-50 text-[#00880F] rounded-2xl flex items-center justify-center mx-auto text-2xl border border-emerald-200/60 shadow-sm">
            📦
        </div>
        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Lacak Status Pesanan Anda</h2>
        <p class="text-xs text-gray-400 font-medium max-w-md mx-auto">
            Masukkan Nomor Pesanan (contoh: <code>ORD-20260826-XXXXX</code>), Nomor Resi Pengiriman, atau Nomor WhatsApp untuk melihat tahapan pesanan.
        </p>
    </div>

    {{-- KOTAK INPUT FORM CARI --}}
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-gray-100 shadow-xl">
        <form method="GET" action="{{ route('order.track.index') }}" class="space-y-4">
            <div class="relative">
                <input type="text" name="q" value="{{ $search ?? '' }}" required placeholder="Ketik No. Pesanan / Resi / No. WhatsApp..."
                       class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-bold text-sm text-gray-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
                <div class="absolute left-4 top-4 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                </div>
            </div>

            {{-- WIDGET CLOUDFLARE TURNSTILE (JIKA AKTIF DI SETTING ADMIN) --}}
            @if(\App\Services\TurnstileService::isEnabled())
                <div class="pt-2 flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ \App\Services\TurnstileService::getSiteKey() }}" data-theme="light"></div>
                </div>
            @endif

            <button type="submit" 
                    class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/25 transition flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                <span>Lacak Pesanan Sekarang</span>
            </button>
        </form>
    </div>

    {{-- HASIL PENCARIAN PESANAN --}}
    @if(!empty($search))
        <div class="space-y-4">
            <h3 class="font-black text-gray-800 uppercase text-xs tracking-wider">Hasil Pencarian ({{ $orders->count() }} Pesanan Ditemukan):</h3>
            
            @forelse($orders as $order)
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-mono font-black text-indigo-700 text-xs">{{ $order->order_number }}</span>
                            <h4 class="font-black text-gray-900 text-sm mt-0.5">{{ $order->customer_name }}</h4>
                            <p class="text-[10px] text-gray-400 font-medium">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <div>
                            {!! $order->status_badge !!}
                        </div>
                    </div>

                    <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl">
                        <span>Total Belanja: <b>{{ $order->formatted_total }}</b> &bull; {{ $order->items->count() }} Macam Produk</span>
                        @if($order->tracking_number)
                            <p class="mt-1 text-[#00661A] font-bold">🚚 Resi: <code class="bg-emerald-100 px-2 py-0.5 rounded text-[#00661A] font-mono">{{ $order->tracking_number }}</code> ({{ $order->courier }})</p>
                        @endif
                    </div>

                    <div class="pt-1 flex justify-end">
                        <a href="{{ route('order.track', $order->order_number) }}" 
                           class="px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl text-xs font-black uppercase tracking-wider transition">
                            Lihat Detail Lacak →
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 bg-white rounded-[2rem] border border-gray-100 text-center text-gray-400 font-bold text-xs italic">
                    Pesanan dengan kata kunci "{{ $search }}" tidak ditemukan. Pastikan nomor pesanan atau nomor HP sudah benar.
                </div>
            @endforelse
        </div>
    @endif

</div>
@endsection
