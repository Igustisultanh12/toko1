<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>{{ $shop['app_name'] ?? 'SIKANDA' }} - Kasir Modern</title>
    @if(!empty($shop['app_favicon']))
        <link rel="icon" href="{{ route('media.file', ['path' => $shop['app_favicon']]) }}">
    @endif

    

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    

    <script src="https://unpkg.com/@zxing/library@latest"></script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>



    <style>

        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

        [x-cloak] { display: none !important; }

        .custom-scroll::-webkit-scrollbar { width: 4px; }

        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .glass-header { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }

        .loader { border-top-color: #4f46e5; animation: spinner 1.5s linear infinite; }

        @keyframes spinner { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        

        @media (max-width: 768px) {

            .cart-height { height: calc(100vh - 450px); }

        }

        #qris-placeholder iframe { width: 100%; height: 100%; border: none; border-radius: 1.5rem; }

        

        .pulse-green { animation: pulse-bg 2s infinite; }

        @keyframes pulse-bg {

            0% { background-color: rgba(79, 70, 229, 0.1); }

            50% { background-color: rgba(34, 197, 94, 0.2); }

            100% { background-color: rgba(79, 70, 229, 0.1); }

        }

    </style>

</head>

<body class="h-screen overflow-hidden pb-safe">



    <div x-data="posSystem" @keydown.window="handleShortcuts($event)" class="flex flex-col h-full">

        

        {{-- HEADER POS --}}
        <header class="bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-6 py-3.5 flex justify-between items-center shadow-sm z-50">
            <div class="flex items-center space-x-5">
                <div class="flex items-center space-x-3">
                    @if(!empty($shop['app_favicon']))
                        <img src="{{ route('media.file', ['path' => $shop['app_favicon']]) }}" class="w-8 h-8 rounded-xl object-contain shadow-sm">
                    @else
                        <div class="w-8 h-8 rounded-xl bg-[#00AA13] flex items-center justify-center font-black text-white text-sm shadow-sm">
                            {{ strtoupper(substr($shop['app_name'] ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-black text-[#00880F] tracking-tight uppercase leading-none">{{ $shop['app_name'] ?? 'SIKANDA' }}</h1>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">{{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-xs font-black text-emerald-800 uppercase flex items-center bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-200/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        <span id="cashier-name">{{ Auth::user()->name }}</span>
                    </span>

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 bg-gray-900 hover:bg-black text-white text-[11px] font-black uppercase tracking-wider rounded-xl shadow-md transition active:scale-95 border border-gray-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Dashboard Admin</span>
                        </a>
                    @endif

                    @php
                        $unconfirmedCount = \App\Models\Order::where('status', 'paid')->count();
                    @endphp

                    <a href="{{ route('admin.orders.index') }}" 
                       class="inline-flex items-center space-x-2 px-3.5 py-1.5 bg-emerald-50 text-[#00880F] hover:bg-emerald-100 text-[11px] font-black uppercase tracking-wider rounded-xl transition border border-emerald-200/60 shadow-sm relative group">
                        <span>🛒 Pesanan Online</span>
                        <span id="posOrderBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white shadow-sm animate-pulse {{ $unconfirmedCount > 0 ? '' : 'hidden' }}">
                            {{ $unconfirmedCount }}
                        </span>
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                {{-- TOMBOL KONTROL SUARA MP3 --}}
                <div class="flex items-center bg-gray-100 rounded-2xl p-1 space-x-1">
                    <button @click="testVoice()" class="flex items-center space-x-1 px-3 py-1.5 bg-white rounded-xl shadow-sm hover:bg-emerald-50 transition-all text-[#00880F] border border-transparent active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[10px] font-black uppercase">Tes Suara</span>
                    </button>

                    <button @click="toggleMute()" class="p-1.5 rounded-xl transition-all active:scale-90" :class="isMuted ? 'text-red-500 bg-red-50' : 'text-emerald-600 bg-emerald-50'">
                        <template x-if="!isMuted">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H3a1 1 0 01-1-1V8a1 1 0 011-1h1.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.984 3.984 0 00-1.172-2.828 1 1 0 010-1.415z"/></svg>
                        </template>
                        <template x-if="isMuted">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.12 10l3.53 3.53a1 1 0 01-1.41 1.41L10.71 11.41l-3.53 3.53a1 1 0 01-1.41-1.41L9.29 10 5.76 6.47a1 1 0 011.41-1.41L10.71 8.59l3.53-3.53a1 1 0 011.41 1.41L12.12 10zM10 4.58l-3.71 3.71L5.59 7.59 10 3.17l4.41 4.42-1.41 1.41L10 5.29v-.71z" /></svg>
                        </template>
                    </button>
                </div>

                <div class="text-right pr-3 border-r hidden sm:block">
                    <p class="font-black text-gray-800 text-xs" x-text="currentTime"></p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-2xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-500 transition-all active:scale-90 border border-rose-100" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden bg-[#F6F8F9]">
            {{-- KOLOM KIRI: CARI, SCAN & DAFTAR ITEM --}}
            <div class="w-full lg:w-3/5 p-4 sm:p-6 flex flex-col overflow-y-auto custom-scroll space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="relative" @click.away="searchResults = []">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Cari Produk</label>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts()" 
                               class="w-full px-4 py-3.5 bg-white border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all text-xs" placeholder="Ketik nama produk...">

                        <div x-show="searchResults.length > 0" class="absolute z-[60] w-full mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden" x-cloak>
                            <template x-for="p in searchResults" :key="p.id">
                                <div @click="addToCart(p)" class="p-3.5 hover:bg-[#00AA13] hover:text-white cursor-pointer flex justify-between items-center border-b border-gray-50 transition-colors">
                                    <span class="font-bold text-xs uppercase" x-text="p.name"></span>
                                    <span class="font-black text-xs" x-text="formatCurrency(p.price)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Scanner Barcode</label>
                        <div class="flex shadow-sm rounded-2xl overflow-hidden border-2 border-gray-100 bg-white focus-within:border-[#00AA13]">
                            <input type="text" x-model="manualBarcode" x-ref="manualBarcode" @keydown.enter.prevent="scanBarcode()" 
                                   class="flex-grow px-4 py-3 bg-white border-none outline-none font-mono font-bold text-[#00880F] uppercase text-xs" placeholder="Arahkan barcode...">
                            <button @click="toggleScanner()" class="bg-[#00AA13] hover:bg-[#00880F] text-white px-5 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2.5"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TABEL KERANJANG BELANJA --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex-grow flex flex-col cart-height">
                    <div class="overflow-y-auto flex-grow custom-scroll">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50/80 border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="p-4 text-left font-black text-gray-400 uppercase text-[10px]">Item</th>
                                    <th class="p-4 text-center font-black text-gray-400 uppercase text-[10px]">Qty</th>
                                    <th class="p-4 text-right font-black text-gray-400 uppercase text-[10px]">Subtotal</th>
                                    <th class="p-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-if="cart.length === 0">
                                    <tr><td colspan="4" class="p-16 text-center text-gray-300 font-bold uppercase italic text-xs">Belum ada item terpilih</td></tr>
                                </template>
                                <template x-for="(item, index) in cart" :key="index">
                                    <tr class="hover:bg-emerald-50/40 transition-colors">
                                        <td class="p-4 font-black text-gray-800 uppercase" x-text="item.name"></td>
                                        <td class="p-4 text-center">
                                            <div class="inline-flex items-center space-x-1.5 bg-gray-50 p-1 rounded-xl border border-gray-100">
                                                <button @click="updateQty(index, -1)" class="w-7 h-7 bg-white rounded-lg font-black text-gray-700 shadow-sm active:scale-90">-</button>
                                                <span class="font-black text-sm w-6 text-center text-[#00880F]" x-text="item.quantity"></span>
                                                <button @click="updateQty(index, 1)" class="w-7 h-7 bg-white rounded-lg font-black text-gray-700 shadow-sm active:scale-90">+</button>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right font-black text-[#00880F] text-sm" x-text="formatCurrency(item.price * item.quantity)"></td>
                                        <td class="p-4 text-center"><button @click="removeItem(index)" class="text-gray-300 hover:text-rose-500 font-black text-base">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: RINGKASAN TAGIHAN & PEMBAYARAN --}}
            <div class="w-full lg:w-2/5 bg-white p-6 sm:p-8 border-l border-gray-100 shadow-2xl flex flex-col justify-between">
                <div class="space-y-5">
                    <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Ringkasan Pembayaran</h2>

                    {{-- INPUT NAMA PELANGGAN --}}
                    <div class="bg-gray-50 p-4 rounded-3xl border border-gray-100">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 flex justify-between items-center">
                            <span>Nama Pelanggan</span>
                            <span class="text-[9px] text-[#00880F] font-black cursor-pointer hover:underline" @click="customerName = 'Pelanggan Umum'">Set Umum</span>
                        </label>
                        <input type="text" x-model="customerName" placeholder="Contoh: Pak Budi / Bu Siti" 
                               class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs font-bold text-gray-800 outline-none focus:border-[#00AA13] focus:ring-2 focus:ring-emerald-500/10 transition-all">
                    </div>

                    {{-- TOTAL BAYAR GOPAY CARD STYLE --}}
                    <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] p-7 rounded-[2.5rem] shadow-xl shadow-emerald-950/15 text-white text-center">
                        <p class="text-[10px] font-black text-emerald-200 uppercase mb-1 tracking-widest leading-none">Total Tagihan Belanja</p>
                        <div class="text-4xl sm:text-5xl font-black tracking-tight leading-none my-2">
                            <span class="text-lg mr-1 opacity-80">Rp</span><span x-text="formatNumber(total)"></span>
                        </div>
                        <p class="text-[10px] text-emerald-100 font-bold uppercase tracking-wider" x-text="cart.length + ' Macam Produk'"></p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button @click="openPaymentModal()" :disabled="cart.length === 0" 
                            class="w-full bg-[#00AA13] hover:bg-[#00880F] text-white py-5 rounded-[2rem] text-sm font-black shadow-xl shadow-emerald-500/25 active:scale-95 disabled:bg-gray-200 disabled:shadow-none transition-all uppercase tracking-widest">
                        PROSES BAYAR (B)
                    </button>
                    <p class="text-[9px] font-bold text-gray-400 text-center tracking-widest">
                        &copy; {{ date('Y') }} I Gusti Sultan. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        {{-- MODAL PEMBAYARAN --}}
        <div x-show="isPaymentModalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[100] p-4" x-cloak x-transition>
            <div @click.away="!isLoading && (isPaymentModalOpen = false)" class="bg-white rounded-[3rem] p-8 sm:p-10 w-full max-w-lg shadow-2xl border border-gray-100">
                <div class="text-center mb-6">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Pilih Metode Pembayaran</p>
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mt-2" x-text="formatCurrency(total)"></h2>
                </div>

                {{-- NAMA PELANGGAN DI DALAM MODAL --}}
                <div class="mb-4 text-left">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Pelanggan</label>
                    <input type="text" x-model="customerName" placeholder="Pelanggan Umum"
                           class="w-full text-xs font-bold px-4 py-3 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3 mb-5">
                    <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-[#00AA13] text-white shadow-lg shadow-emerald-500/25' : 'bg-gray-100 text-gray-500'" class="p-4 rounded-2xl font-black transition-all uppercase tracking-wider text-xs">
                        💵 TUNAI (CASH)
                    </button>
                    <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-[#00AED6] text-white shadow-lg shadow-cyan-500/25' : 'bg-gray-100 text-gray-500'" class="p-4 rounded-2xl font-black transition-all uppercase tracking-wider text-xs">
                        📱 QRIS / GOPAY
                    </button>
                </div>

                <div x-show="paymentMethod === 'cash'" x-transition>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest text-left">Uang Diterima dari Pelanggan</label>
                    <input type="number" x-model.number="amountPaid" x-ref="amountPaidInput"
                           class="w-full text-3xl font-black p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl text-center outline-none focus:border-[#00AA13] focus:bg-white transition-all" placeholder="0">

                    <div x-show="amountPaid > 0" class="mt-3 p-3.5 rounded-2xl border-2 border-dashed border-emerald-200 text-center bg-emerald-50/50" x-transition>
                         <p class="text-[10px] font-black text-[#00880F] uppercase tracking-widest leading-none">Uang Kembalian</p>
                         <p class="text-2xl font-black text-[#00880F] mt-1" x-text="formatCurrency(change)"></p>
                    </div>
                </div>

                <div class="mt-8 flex space-x-3">
                    <button @click="isPaymentModalOpen = false" :disabled="isLoading" class="flex-1 py-4 font-black text-gray-400 hover:text-gray-600 uppercase text-xs tracking-widest rounded-2xl bg-gray-50 transition">BATAL</button>
                    <button @click="completeTransaction()" :disabled="isLoading || (paymentMethod === 'cash' && amountPaid < total)" 
                            class="flex-[2] bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 disabled:bg-gray-200 transition-all flex justify-center items-center">
                        <template x-if="isLoading"><div class="loader h-5 w-5 border-3"></div></template>
                        <template x-if="!isLoading"><span>SELESAIKAN TRANSAKSI</span></template>
                    </button>
                </div>
            </div>
        </div>



        {{-- MODAL QRIS DINAMIS --}}
        <div x-show="isQrisModalOpen" class="fixed inset-0 bg-indigo-950/95 backdrop-blur-md flex items-center justify-center z-[150] p-4" x-cloak x-transition>
            <div class="bg-white rounded-[3.5rem] p-4 w-full max-w-lg text-center shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600"></div>

                <div id="qris-placeholder" class="bg-gray-50 rounded-[2.5rem] overflow-hidden border-4 border-gray-100 h-[500px] flex items-center justify-center relative">
                    <p class="animate-pulse font-bold text-gray-300">Menyiapkan QRIS...</p>
                </div>

                <div class="p-4 flex gap-3">
                    <button @click="forceCheckStatus(lastSaleId)" class="flex-1 bg-emerald-50 text-[#00880F] py-3.5 rounded-2xl font-black hover:bg-emerald-100 transition-all uppercase tracking-widest text-[11px] flex items-center justify-center border border-emerald-200 shadow-sm active:scale-95">
                        <span>🔄 SUDAH BAYAR / CEK</span>
                    </button>
                    <button @click="confirmCancelTransaction()" class="flex-1 bg-red-50 text-red-500 py-3.5 rounded-2xl font-black hover:bg-red-100 transition-all uppercase tracking-widest text-[11px] border border-red-200 active:scale-95">
                        BATALKAN
                    </button>
                </div>
            </div>
        </div>



        {{-- MODAL SUKSES --}}

        <div x-show="isSuccessModalOpen" class="fixed inset-0 bg-indigo-900/95 flex items-center justify-center z-[130] p-4" x-cloak x-transition>

            <div class="bg-white rounded-[3rem] p-10 w-full max-w-sm text-center shadow-2xl border-t-[12px]" :class="paymentStatus === 'success' ? 'border-green-500' : 'border-indigo-500'">

                <div x-show="paymentStatus === 'pending'">

                    <div class="loader h-24 w-24 mx-auto mb-4 border-8"></div>

                    <p class="font-black text-indigo-600 uppercase text-sm">Memverifikasi Pembayaran...</p>

                </div>

                <div x-show="paymentStatus === 'success'">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3.5"/></svg>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-wider leading-none">PEMBAYARAN BERHASIL!</h3>
                    <p class="text-3xl font-black text-green-600 my-2" x-text="formatCurrency(lastTotal)"></p>
                    
                    {{-- DETAIL TRANSAKSI --}}
                    <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 text-left my-4 text-xs space-y-1.5 font-medium text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-400">No. Invoice:</span>
                            <span class="font-mono font-bold text-gray-800" x-text="lastTransactionNumber"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Pelanggan:</span>
                            <span class="font-bold text-gray-800" x-text="lastCustomerName || 'Pelanggan Umum'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Metode Bayar:</span>
                            <span class="font-bold uppercase" :class="lastPaymentMethod === 'qris' ? 'text-indigo-600' : 'text-emerald-600'" x-text="lastPaymentMethod"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        {{-- PILIHAN 1: CETAK STRUK --}}
                        <button @click="isMobileApp() ? printReceiptBluetooth() : printReceiptWeb()" :disabled="isPrinting"
                                class="w-full bg-indigo-600 text-white py-3.5 rounded-2xl font-black uppercase text-xs tracking-wider shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2.5"/></svg>
                            <span x-show="!isPrinting">CETAK NOTA / STRUK</span>
                            <div x-show="isPrinting" class="loader h-4 w-4 border-2"></div>
                        </button>

                        {{-- PILIHAN 2: KIRIM STRUK KE WHATSAPP --}}
                        <div class="bg-green-50/80 p-3.5 rounded-2xl border border-green-200 text-left space-y-2">
                            <label class="block text-[10px] font-black text-green-800 uppercase tracking-wider">Kirim Struk ke WhatsApp Pelanggan</label>
                            <div class="flex gap-2">
                                <input type="tel" x-model="customerPhone" placeholder="08xxxxxxxxxx"
                                       class="flex-1 bg-white border border-green-300 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 outline-none focus:ring-2 focus:ring-green-500">
                                <button @click="sendReceiptToWhatsApp()" 
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-black text-xs rounded-xl shadow transition-all flex items-center shrink-0">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                                    Kirim
                                </button>
                            </div>
                        </div>

                        {{-- PILIHAN 3: KIRIM PAKET / CETAK LABEL PENGIRIMAN --}}
                        <button @click="openShippingModal()" 
                                class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3.5 rounded-2xl font-black uppercase text-xs tracking-wider shadow-lg shadow-amber-100 active:scale-95 transition-all flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2.5"/></svg>
                            📦 KIRIM PAKET (CETAK RESI)
                        </button>

                        {{-- PILIHAN 4: TRANSAKSI BARU --}}
                        <button @click="location.reload()" 
                                class="w-full bg-gray-900 text-white py-3.5 rounded-2xl font-black uppercase text-xs tracking-wider hover:bg-black transition-all">
                            Transaksi Baru (ESC)
                        </button>
                    </div>
                </div>

            </div>

        </div>

        {{-- MODAL FORM LABEL PENGIRIMAN PAKET --}}
        <div x-show="isShippingModalOpen" class="fixed inset-0 bg-indigo-950/90 backdrop-blur-sm flex items-center justify-center z-[140] p-4" x-cloak x-transition>
            <div class="bg-white rounded-[3rem] p-6 sm:p-8 w-full max-w-2xl text-left shadow-2xl border-t-[8px] border-amber-500 max-h-[92vh] overflow-y-auto space-y-6">
                
                {{-- HEADER MODAL --}}
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div>
                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-lg">📦 Label Pengiriman Paket</span>
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mt-1">Cetak Resi / Tempelan Paket</h3>
                        <p class="text-xs text-gray-400 font-medium">Invoice: <span class="font-mono font-bold text-gray-700" x-text="lastTransactionNumber"></span></p>
                    </div>
                    <button @click="isShippingModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- SEKSI 1: DATA PENERIMA (TUJUAN) --}}
                    <div class="bg-amber-50/50 p-5 rounded-3xl border border-amber-200/80 space-y-4">
                        <div class="flex items-center space-x-2 border-b border-amber-200/60 pb-2">
                            <span class="bg-amber-500 text-white font-black text-[10px] px-2 py-0.5 rounded-md uppercase">TO</span>
                            <h4 class="font-black text-gray-800 text-xs uppercase tracking-wider">Data Penerima (Tujuan)</h4>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nama Penerima</label>
                            <input type="text" x-model="shippingRecipientName" 
                                   class="w-full bg-white border border-amber-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nomor HP / WhatsApp Penerima</label>
                            <input type="tel" x-model="shippingRecipientPhone" placeholder="08xxxxxxxxxx"
                                   class="w-full bg-white border border-amber-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap Tujuan Pengiriman</label>
                            <textarea x-model="shippingRecipientAddress" rows="3" placeholder="Jl. Nama Jalan No. XX, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Kode Pos"
                                      class="w-full bg-white border border-amber-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Ekspedisi / Kurir</label>
                                <select x-model="shippingCourier" class="w-full bg-white border border-amber-200 rounded-xl px-3 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-amber-500 outline-none">
                                    <option value="J&T Express">J&T Express</option>
                                    <option value="JNE Express">JNE Express</option>
                                    <option value="SiCepat">SiCepat</option>
                                    <option value="Shopee Xpress">SPX (Shopee)</option>
                                    <option value="GoSend / Grab">GoSend / Grab</option>
                                    <option value="POS Indonesia">POS Indonesia</option>
                                    <option value="Anteraja">Anteraja</option>
                                    <option value="Kurir Toko">Kurir Toko</option>
                                    <option value="Ambil Sendiri">Ambil Sendiri</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Catatan Paket</label>
                                <input type="text" x-model="shippingNotes" 
                                       class="w-full bg-white border border-amber-200 rounded-xl px-3 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-amber-500 outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI 2: DATA PENGIRIM (DARI / TOKO) --}}
                    <div class="bg-gray-50 p-5 rounded-3xl border border-gray-200 space-y-4">
                        <div class="flex items-center space-x-2 border-b border-gray-200 pb-2">
                            <span class="bg-gray-700 text-white font-black text-[10px] px-2 py-0.5 rounded-md uppercase">FROM</span>
                            <h4 class="font-black text-gray-800 text-xs uppercase tracking-wider">Data Pengirim (Toko)</h4>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Nama Toko / Pengirim</label>
                            <input type="text" x-model="shippingSenderName" 
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Nomor Telepon Toko</label>
                            <input type="tel" x-model="shippingSenderPhone" 
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Alamat Asal Toko</label>
                            <textarea x-model="shippingSenderAddress" rows="3" 
                                      class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                        </div>
                        
                        <div class="p-3 bg-indigo-50 rounded-2xl text-[10px] text-indigo-700 font-medium leading-relaxed">
                            💡 Data pengirim otomatis diambil dari database Pengaturan Toko dan dapat disesuaikan jika perlu.
                        </div>
                    </div>

                </div>

                {{-- FOOTER MODAL ACTIONS --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button @click="isShippingModalOpen = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-2xl uppercase transition">
                        Kembali
                    </button>

                    <button @click="printShippingLabel()" 
                            class="px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-amber-100 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2.5"/></svg>
                        GENERATE & CETAK LABEL PAKET (PDF)
                    </button>
                </div>

            </div>
        </div>

    </div>



    <iframe id="print-iframe" style="display:none;"></iframe>



    <script>
        {{-- FUNGSI TEXT-TO-SPEECH (SUARA NOMINAL BAHASA INDONESIA) --}}
        function speakPaymentSuccess(amount, isMuted) {
            if (isMuted) return;

            try {
                const nominalInt = Math.round(amount || 0);
                const textToSpeak = `Pembayaran senilai ${nominalInt} rupiah berhasil`;

                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(textToSpeak);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    utterance.pitch = 1.0;

                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => (v.lang === 'id-ID' || v.lang === 'id_ID' || (v.lang && v.lang.toLowerCase().includes('id'))));
                    if (idVoice) {
                        utterance.voice = idVoice;
                    }

                    window.speechSynthesis.speak(utterance);
                }
            } catch (e) {
                console.error("Speech Synthesis Error:", e);
            }
        }

        {{-- LOGIKA PEMUTARAN AUDIO MP3 + SUARA NOMINAL --}}
        function announcePaymentSuccess(amount, soundUrl, isMuted) {
            if (isMuted) return;

            if (soundUrl && soundUrl !== "") {
                const audio = new Audio(soundUrl);
                audio.play().catch(e => console.log("Audio Playback Error:", e));
                setTimeout(() => {
                    speakPaymentSuccess(amount, isMuted);
                }, 1000);
            } else {
                speakPaymentSuccess(amount, isMuted);
            }
        }

        window.playNotificationSound = function(type = 'chime') {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();

                if (type === 'order_new' || type === 'chime') {
                    const now = ctx.currentTime;
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, now);
                    gain1.gain.setValueAtTime(0.3, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.4);

                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1320, now + 0.18);
                    gain2.gain.setValueAtTime(0.35, now + 0.18);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.18);
                    osc2.stop(now + 0.8);
                } else if (type === 'payment_success' || type === 'success') {
                    const notes = [523.25, 659.25, 783.99, 1046.50];
                    notes.forEach((freq, idx) => {
                        const startTime = ctx.currentTime + (idx * 0.1);
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, startTime);
                        gain.gain.setValueAtTime(0.25, startTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(startTime);
                        osc.stop(startTime + 0.35);
                    });
                }
            } catch(e) {
                console.log("Audio Error:", e);
            }
        };

        function playPaymentSound(soundUrl, isMuted) {
            if (isMuted || !soundUrl || soundUrl === "") return;
            const audio = new Audio(soundUrl);
            audio.play().catch(e => console.error("Audio Playback Error:", e));
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                shop: {
                    name: @json($shop['shop_name'] ?? 'TOKO ANANDA'),
                    address: @json($shop['shop_address'] ?? 'Jember'),
                    phone: @json($shop['shop_phone'] ?? '-'),
                    footer: @json($shop['receipt_footer'] ?? 'Terima Kasih!'),
                    payment_sound: @json($shop['payment_sound'] ?? null) 
                },

                customerName: 'Pelanggan Umum',
                customerPhone: '',
                cart: [], searchQuery: '', searchResults: [], manualBarcode: '',
                isPaymentModalOpen: false, isQrisModalOpen: false, 
                paymentMethod: 'cash', amountPaid: '', isSuccessModalOpen: false,
                lastSaleId: null, lastTransactionNumber: null,
                lastTotal: 0, lastItems: [], lastCustomerName: '', lastPaymentMethod: 'cash',
                lastSignedInvoiceUrl: '',
                isLoading: false, isPrinting: false, currentTime: '',
                paymentStatus: 'pending', statusInterval: null,
                isMuted: !(@json($shop['is_voice_enabled'] ?? true)),

                // --- STATE PENGIRIMAN PAKET ---
                isShippingModalOpen: false,
                shippingRecipientName: '',
                shippingRecipientPhone: '',
                shippingRecipientAddress: '',
                shippingCourier: 'J&T Express',
                shippingNotes: 'FRAGILE - JANGAN DIBANTING / DITINDIH',
                shippingSenderName: '',
                shippingSenderPhone: '',
                shippingSenderAddress: '',

                get total() { return this.cart.reduce((a, b) => a + (b.price * b.quantity), 0); },
                get change() { return Math.max(0, (this.amountPaid || 0) - this.total); },

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    this.codeReader = new ZXing.BrowserMultiFormatReader();
                    this.$nextTick(() => this.$refs.manualBarcode?.focus());
                    
                    // Preload voice jika browser siap
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.onvoiceschanged = () => {
                            window.speechSynthesis.getVoices();
                        };
                    }

                    // Polling notifikasi pesanan online masuk di layar kasir
                    this.startOnlineOrderPolling();

                    // Tangkap event postMessage dari iframe DOKU
                    window.addEventListener('message', (event) => {
                        if (event.data && (event.data.status === 'SUCCESS' || event.data.type === 'DOKU_PAYMENT_SUCCESS' || event.data.event === 'payment_success')) {
                            if (this.lastSaleId) {
                                this.forceCheckStatus(this.lastSaleId);
                            }
                        }
                    });
                },

                startOnlineOrderPolling() {
                    let lastNotified = {{ $unconfirmedCount ?? 0 }};
                    const checkOrders = async () => {
                        try {
                            const res = await fetch("{{ route('orders.realtime-check') }}");
                            if (res.ok) {
                                const data = await res.json();
                                const badge = document.getElementById('posOrderBadge');
                                if (badge) {
                                    if (data.count > 0) {
                                        badge.innerText = data.count;
                                        badge.classList.remove('hidden');
                                    } else {
                                        badge.innerText = '0';
                                        badge.classList.add('hidden');
                                    }
                                }
                                if (data.count > lastNotified) {
                                    lastNotified = data.count;
                                    if (typeof window.playNotificationSound === 'function') {
                                        window.playNotificationSound('order_new');
                                    }
                                    if (data.latest_order) {
                                        Swal.fire({
                                            title: '🚨 PESANAN ONLINE BARU!',
                                            html: `<div style="text-align:left; font-size:12px; line-height:1.6; margin-top:6px;">
                                                        <p><b>No Pesanan:</b> <code style="color:#00AA13; font-weight:bold;">${data.latest_order.order_number}</code></p>
                                                        <p><b>Nama:</b> ${data.latest_order.customer_name}</p>
                                                        <p><b>Total:</b> <b style="color:#00AA13;">${data.latest_order.formatted_total}</b> (QRIS)</p>
                                                        <p><b>Ekspedisi:</b> ${data.latest_order.courier}</p>
                                                   </div>`,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonColor: '#00AA13',
                                            cancelButtonColor: '#6B7280',
                                            confirmButtonText: 'Buka Pesanan',
                                            cancelButtonText: 'Tutup'
                                        }).then((res) => {
                                            if (res.isConfirmed) {
                                                window.location.href = "{{ route('admin.orders.index') }}?status=unconfirmed";
                                            }
                                        });
                                    }
                                } else if (data.count < lastNotified) {
                                    lastNotified = data.count;
                                }
                            }
                        } catch(e) {}
                    };
                    checkOrders();
                    setInterval(checkOrders, 3500);
                },

                updateTime() { 
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB'; 
                },
                formatCurrency(n) { return new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n); },
                formatNumber(n) { return new Intl.NumberFormat('id-ID').format(n); },
                isMobileApp() { return !!window.bluetoothSerial; },

                toggleMute() {
                    this.isMuted = !this.isMuted;
                    if (!this.isMuted) {
                        Swal.fire({ title: 'Suara Aktif', icon: 'success', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                        this.unlockAudio();
                    }
                },

                unlockAudio() {
                    if (this.shop.payment_sound) {
                        let pancingan = new Audio(this.shop.payment_sound);
                        pancingan.volume = 0;
                        pancingan.play().catch(e => {});
                    }
                },

                testVoice() {
                    if (this.isMuted) {
                        Swal.fire({ title: 'Suara Dibisukan', text: 'Aktifkan suara di pojok kanan atas dulu.', icon: 'warning', toast: true, position: 'top-end', timer: 2500 });
                        return;
                    }
                    Swal.fire({ title: 'Mengetes Suara...', text: 'Pembayaran senilai 2000 rupiah berhasil', icon: 'info', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                    announcePaymentSuccess(2000, this.shop.payment_sound, false);
                },

                async sendReceiptToWhatsApp() {
                    if (!this.customerPhone || this.customerPhone.trim() === '') {
                        Swal.fire({
                            title: 'Nomor WhatsApp Kosong',
                            text: 'Silakan masukkan nomor WhatsApp pelanggan.',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    let phone = this.customerPhone.replace(/[^0-9]/g, '');
                    if (phone.startsWith('0')) {
                        phone = '62' + phone.substring(1);
                    } else if (!phone.startsWith('62')) {
                        phone = '62' + phone;
                    }

                    const shopName = this.shop.name || 'TOKO ANANDA';
                    const shopAddress = this.shop.address || '';
                    const now = new Date();
                    const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'Asia/Jakarta' }) + ' ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' });

                    let itemsList = '';
                    (this.lastItems || []).forEach((item, idx) => {
                        const subtotal = this.formatNumber(item.price * item.quantity);
                        itemsList += `${idx + 1}. *${item.name}*\n   ${item.quantity} pcs x Rp ${this.formatNumber(item.price)} = Rp ${subtotal}\n`;
                    });

                    // Ambil link download bertanda tangan digital (berlaku 24 jam)
                    let pdfUrl = this.lastSignedInvoiceUrl;
                    if (!pdfUrl) {
                        try {
                            const res = await fetch(`/invoice/${this.lastTransactionNumber}/get-link`);
                            const json = await res.json();
                            if (json.signed_url) {
                                pdfUrl = json.signed_url;
                            }
                        } catch (e) {
                            pdfUrl = `${window.location.origin}/invoice/${this.lastTransactionNumber}/download`;
                        }
                    }

                    let message = `*${shopName.toUpperCase()}*\n`;
                    if (shopAddress) message += `📍 _${shopAddress}_\n`;
                    if (shopPhone && shopPhone !== '-') message += `📞 _Telp: ${shopPhone}_\n`;
                    message += `================================\n`;
                    message += `🧾 *STRUK PEMBELIAN*\n`;
                    message += `No. Nota   : \`${this.lastTransactionNumber || '-'}\`\n`;
                    message += `Tanggal    : ${dateStr} WIB\n`;
                    message += `Pelanggan  : *${this.lastCustomerName || 'Pelanggan Umum'}*\n`;
                    message += `Kasir      : ${document.querySelector('.user-name')?.innerText?.trim() || 'Admin'}\n`;
                    message += `--------------------------------\n`;
                    message += `*DAFTAR BARANG:*\n${itemsList}`;
                    message += `--------------------------------\n`;
                    message += `💰 *TOTAL BELANJA : Rp ${this.formatNumber(this.lastTotal)}*\n`;
                    message += `Metode Bayar  : ${(this.lastPaymentMethod || 'CASH').toUpperCase()}\n`;
                    message += `Status Bayar  : ✅ *LUNAS / SUKSES*\n`;
                    message += `================================\n`;
                    message += `📄 *UNDUH FAKTUR (PDF):*\n${pdfUrl}\n`;
                    message += `_(Tautan berlaku selama 24 jam)_\n`;
                    message += `================================\n`;
                    message += `_${this.shop.footer || 'Terima kasih telah berbelanja!'}_`;

                    const waUrl = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(message)}`;
                    window.open(waUrl, '_blank');
                },

                openShippingModal() {
                    this.shippingRecipientName = this.lastCustomerName || 'Pelanggan Umum';
                    this.shippingRecipientPhone = this.customerPhone || '';
                    this.shippingRecipientAddress = '';
                    this.shippingCourier = 'J&T Express';
                    this.shippingNotes = 'FRAGILE - JANGAN DIBANTING / DITINDIH';
                    this.shippingSenderName = this.shop.name || 'TOKO ANANDA';
                    this.shippingSenderPhone = this.shop.phone || '';
                    this.shippingSenderAddress = this.shop.address || '';
                    this.isShippingModalOpen = true;
                },

                printShippingLabel() {
                    if (!this.lastSaleId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Transaksi Tidak Valid',
                            text: 'ID Transaksi tidak ditemukan untuk mencetak label resi.',
                            confirmButtonColor: '#00AA13'
                        });
                        return;
                    }
                    const params = new URLSearchParams({
                        recipient_name: this.shippingRecipientName,
                        recipient_phone: this.shippingRecipientPhone,
                        recipient_address: this.shippingRecipientAddress,
                        sender_name: this.shippingSenderName,
                        sender_phone: this.shippingSenderPhone,
                        sender_address: this.shippingSenderAddress,
                        courier: this.shippingCourier,
                        notes: this.shippingNotes
                    });
                    window.open(`/shipping-label/${this.lastSaleId}/pdf?${params.toString()}`, '_blank');
                },

                async searchProducts() {

                    if (this.searchQuery.length < 2) return;

                    const res = await fetch(`{{ route('cashier.pos.search') }}?query=${this.searchQuery}`);

                    this.searchResults = await res.json();

                },



                addToCart(p) {

                    const exists = this.cart.find(i => i.id === p.id);

                    if (exists) exists.quantity++; else this.cart.unshift({ ...p, quantity: 1 });

                    this.searchResults = []; this.searchQuery = '';

                },



                scanBarcode() { if (this.manualBarcode.trim()) { this.fetchProductByBarcode(this.manualBarcode.trim()); this.manualBarcode = ''; } },



                async fetchProductByBarcode(code) {

                    const res = await fetch(`{{ route('cashier.pos.checkProduct') }}?barcode=${code}`);

                    if (res.ok) { this.addToCart(await res.json()); } else { Swal.fire('Gagal', 'Produk tidak ditemukan', 'error'); }

                },



                updateQty(idx, n) { if (this.cart[idx].quantity + n > 0) this.cart[idx].quantity += n; else this.removeItem(idx); },

                removeItem(idx) { this.cart.splice(idx, 1); },

                openPaymentModal() { this.amountPaid = ''; this.isPaymentModalOpen = true; this.$nextTick(() => this.$refs.amountPaidInput?.focus()); },



                async completeTransaction() {

                    {{-- UNLOCK VOICE UNTUK APK (Tepat saat tombol diklik fisik) --}}

                    this.unlockAudio();



                    this.isLoading = true;

                    const payload = {
                        customer_name: this.customerName || 'Pelanggan Umum',
                        items: this.cart.map(i => ({ id: i.id, quantity: i.quantity })),
                        total: this.total, 
                        payment_method: this.paymentMethod,
                        amount_paid: this.paymentMethod === 'cash' ? this.amountPaid : this.total,
                        _token: '{{ csrf_token() }}'
                    };



                    try {

                        const res = await fetch(`{{ route('cashier.pos.store') }}`, {

                            method: 'POST', headers: { 'Content-Type': 'application/json' },

                            body: JSON.stringify(payload)

                        });

                        const data = await res.json();

                        if (!res.ok) throw new Error(data.message || data.error || 'Gagal menyimpan transaksi');



                        this.lastSaleId = data.sale.id;
                        this.lastTransactionNumber = data.sale.transaction_number;
                        this.lastTotal = data.sale.total_amount || this.total;
                        this.lastItems = JSON.parse(JSON.stringify(this.cart));
                        this.lastCustomerName = this.customerName || 'Pelanggan Umum';
                        this.lastPaymentMethod = this.paymentMethod;
                        this.lastSignedInvoiceUrl = data.signed_invoice_url || '';
                        this.customerPhone = '';
                        this.isPaymentModalOpen = false;

                        if (this.paymentMethod === 'qris' && data.qr_string) {
                            this.isQrisModalOpen = true;
                            this.$nextTick(() => {
                                const placeholder = document.getElementById('qris-placeholder');
                                if (data.qr_string.startsWith('http')) {
                                    placeholder.innerHTML = `<iframe src="${data.qr_string}"></iframe>`;
                                } else {
                                    placeholder.innerHTML = "";
                                    new QRCode(placeholder, { text: data.qr_string, width: 250, height: 250 });
                                }
                            });
                            this.paymentStatus = 'pending';
                            this.startAutoCheckStatus(data.sale.id); 
                        } else {
                            // SUKSES TUNAI: Putar Suara MP3 + Suara Nominal
                            setTimeout(() => announcePaymentSuccess(this.lastTotal, this.shop.payment_sound, this.isMuted), 300);
                            this.paymentStatus = 'success';
                            this.isSuccessModalOpen = true;
                            if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 1000);
                        }
                    } catch (e) { Swal.fire('Gagal', e.message, 'error'); } finally { this.isLoading = false; }
                },

                forceCheckStatus(saleId) {
                    if (!saleId) return;
                    fetch(`/cashier/pos/check-status/${saleId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (this.statusInterval) clearInterval(this.statusInterval);
                                this.isQrisModalOpen = false;
                                this.paymentStatus = 'success';
                                this.isSuccessModalOpen = true;
                                announcePaymentSuccess(this.lastTotal, this.shop.payment_sound, this.isMuted);
                                if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 500);
                            } else {
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Konfirmasi Pembayaran QRIS',
                                    text: 'Webhook DOKU belum selesai sinkronisasi. Jika pelanggan sudah berhasil scan/transfer, apakah Anda ingin menandai transaksi ini sebagai LUNAS?',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00AA13',
                                    cancelButtonColor: '#6B7280',
                                    confirmButtonText: '✅ Ya, Tandai Lunas Sekarang',
                                    cancelButtonText: 'Tunggu Webhook'
                                }).then(async (result) => {
                                    if (result.isConfirmed) {
                                        try {
                                            const confirmRes = await fetch(`/cashier/pos/force-confirm/${saleId}`, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
                                            });
                                            if (confirmRes.ok) {
                                                if (this.statusInterval) clearInterval(this.statusInterval);
                                                this.isQrisModalOpen = false;
                                                this.paymentStatus = 'success';
                                                this.isSuccessModalOpen = true;
                                                announcePaymentSuccess(this.lastTotal, this.shop.payment_sound, this.isMuted);
                                                if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 500);
                                            }
                                        } catch (err) {}
                                    }
                                });
                            }
                        }).catch(e => {});
                },

                startAutoCheckStatus(saleId) {
                    if (this.statusInterval) clearInterval(this.statusInterval);
                    this.statusInterval = setInterval(async () => {
                        try {
                            const res = await fetch(`/cashier/pos/check-status/${saleId}`);
                            const data = await res.json();
                            if (data.status === 'success') {
                                clearInterval(this.statusInterval);
                                this.isQrisModalOpen = false;
                                this.paymentStatus = 'success';
                                this.isSuccessModalOpen = true;

                                // SUKSES QRIS: Putar Suara MP3 + Suara Nominal
                                announcePaymentSuccess(this.lastTotal, this.shop.payment_sound, this.isMuted);

                                if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 500);
                            }
                        } catch (e) { }
                    }, 2000); 
                },



                // --- GAYA INDOMARET DENGAN TITIK DUA SEJAJAR ---

                printReceiptBluetooth() {

                    if (!this.isMobileApp()) return;

                    this.isPrinting = true;

                    

                    window.bluetoothSerial.isEnabled(() => {

                        window.bluetoothSerial.list((devices) => {

                            const printer = devices.find(d => 

                                d.name.toUpperCase().includes('RPP02N') || 

                                d.name.toUpperCase().includes('BT') ||

                                d.name.toUpperCase().includes('PRINTER')

                            );

                            

                            if (!printer) { 

                                Swal.fire('Error', 'Printer Tidak Ditemukan.', 'error'); 

                                this.isPrinting = false; return; 

                            }

                            

                            window.bluetoothSerial.connect(printer.id, () => {

                                let receipt = "\x1b\x40\x1b\x61\x01"; // Reset & Center

                                receipt += `\x1b\x21\x30${this.shop.name}\n\x1b\x21\x00${this.shop.address}\n`;

                                if(this.shop.phone) { receipt += `Telp: ${this.shop.phone}\n`; }

                                

                                receipt += "\x1b\x61\x00"; // Left Align

                                receipt += "================================\n";

                                

                                const formatHeader = (label, value) => {

                                    return label.padEnd(14) + ": " + value + "\n";

                                };



                                receipt += formatHeader("No. Nota", this.lastTransactionNumber);
                                receipt += formatHeader("Pelanggan", (this.customerName || "Pelanggan Umum").substring(0, 16));
                                receipt += formatHeader("Tanggal", new Date().toLocaleDateString('id-ID') + " " + new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}));
                                receipt += formatHeader("Kasir", document.getElementById('cashier-name').innerText.trim());

                                receipt += "--------------------------------\n";

                                

                                this.cart.forEach(item => {

                                    receipt += `${item.name.toUpperCase()}\n`;

                                    let subtotal = this.formatNumber(item.price * item.quantity);

                                    let mathLine = `${item.quantity}  x  ${this.formatNumber(item.price)}`;

                                    let spaces = 32 - mathLine.length - subtotal.length;

                                    receipt += mathLine + " ".repeat(Math.max(1, spaces)) + subtotal + "\n";

                                });

                                

                                receipt += "--------------------------------\n";

                                

                                const formatIndo = (label, value) => {

                                    let lbl = label.padEnd(14); 

                                    let val = value.toString();

                                    let spaces = 32 - lbl.length - 2 - val.length; 

                                    return lbl + ": " + " ".repeat(Math.max(0, spaces)) + val + "\n";

                                };



                                receipt += formatIndo("TOTAL BELANJA", this.formatNumber(this.total));

                                receipt += formatIndo("METODE BAYAR", this.paymentMethod.toUpperCase());

                                

                                if (this.paymentMethod === 'cash') {

                                    receipt += formatIndo("TUNAI", this.formatNumber(this.amountPaid));

                                    receipt += formatIndo("KEMBALI", this.formatNumber(this.change));

                                }



                                receipt += "================================\n";

                                receipt += "\x1b\x61\x01"; // Center

                                receipt += `${this.shop.footer}\n\n\n\n`; 

                                

                                window.bluetoothSerial.write(receipt, () => {

                                    window.bluetoothSerial.disconnect();

                                    this.isPrinting = false;

                                }, () => { this.isPrinting = false; });

                            }, (err) => { this.isPrinting = false; });

                        }, (err) => { this.isPrinting = false; });

                    }, () => {

                        this.isPrinting = false;

                    });

                },



                printReceiptWeb() {

                    const iframe = document.getElementById('print-iframe');

                    iframe.src = `{{ url('cashier/receipt') }}/${this.lastSaleId}/print`;

                    iframe.onload = function() { iframe.contentWindow.print(); };

                },



                confirmCancelTransaction() {

                    Swal.fire({

                        title: 'Batalkan?',

                        text: "Hanya tutup jika yakin pembayaran gagal.",

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Ya, Tutup',

                        cancelButtonText: 'Cek Lagi'

                    }).then((result) => {

                        if (result.isConfirmed) {

                            clearInterval(this.statusInterval);

                            this.isQrisModalOpen = false;

                        }

                    });

                },



                handleShortcuts(e) { 

                    if (e.key === 'Escape') {

                        if (this.isSuccessModalOpen && this.paymentStatus === 'success') location.reload();

                        this.isPaymentModalOpen = false;

                        this.isQrisModalOpen = false;

                    }

                    if (e.key.toLowerCase() === 'b' && !this.isPaymentModalOpen && this.cart.length > 0) {

                        this.openPaymentModal();

                    }

                }

            }));

        });

        


    </script>

</body>