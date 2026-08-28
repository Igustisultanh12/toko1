<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>SIKANDA - Kasir Modern</title>
    
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
        
        {{-- HEADER --}}
        <header class="glass-header border-b p-4 flex justify-between items-center shadow-sm z-50">
            <div class="flex items-center space-x-6">
                <h1 class="text-2xl font-black text-indigo-600 tracking-tighter uppercase leading-none">SIKANDA</h1>
                <div class="hidden md:flex items-center space-x-3 text-gray-400">
                    <span class="text-xs font-bold text-gray-500 uppercase flex items-center bg-gray-100 px-3 py-1.5 rounded-full">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                        <span id="cashier-name">{{ Auth::user()->name }}</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                {{-- TOMBOL KONTROL SUARA MP3 --}}
                <div class="flex items-center bg-gray-100 rounded-2xl p-1 space-x-1">
                    <button @click="testVoice()" class="flex items-center space-x-1 px-3 py-1.5 bg-white rounded-xl shadow-sm hover:bg-indigo-50 transition-all text-indigo-600 border border-transparent active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[10px] font-black uppercase">Tes MP3</span>
                    </button>
                    <button @click="toggleMute()" class="p-1.5 rounded-xl transition-all active:scale-90" :class="isMuted ? 'text-red-500 bg-red-50' : 'text-green-500 bg-green-50'">
                        <template x-if="!isMuted">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H3a1 1 0 01-1-1V8a1 1 0 011-1h1.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.984 3.984 0 00-1.172-2.828 1 1 0 010-1.415z"/></svg>
                        </template>
                        <template x-if="isMuted">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M12.12 10l3.53 3.53a1 1 0 01-1.41 1.41L10.71 11.41l-3.53 3.53a1 1 0 01-1.41-1.41L9.29 10 5.76 6.47a1 1 0 011.41-1.41L10.71 8.59l3.53-3.53a1 1 0 011.41 1.41L12.12 10zM10 4.58l-3.71 3.71L5.59 7.59 10 3.17l4.41 4.42-1.41 1.41L10 5.29v-.71z" /></svg>
                        </template>
                    </button>
                </div>

                <div class="text-right pr-4 border-r hidden sm:block">
                    <p class="font-black text-gray-800 text-sm" x-text="currentTime"></p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 text-red-500 hover:text-red-700 transition-all">
                        <div class="bg-red-50 p-2 rounded-xl border border-red-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </button>
                </form>
            </div>
        </header>

        {{-- MAIN --}}
        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden">
            <div class="w-full lg:w-3/5 p-4 flex flex-col overflow-y-auto custom-scroll space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative" @click.away="searchResults = []">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Cari Produk</label>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts()" 
                               class="w-full p-4 bg-white shadow-sm rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold transition-all" placeholder="Nama item...">
                        <div x-show="searchResults.length > 0" class="absolute z-[60] w-full mt-2 bg-white rounded-2xl shadow-2xl border overflow-hidden" x-cloak>
                            <template x-for="p in searchResults" :key="p.id">
                                <div @click="addToCart(p)" class="p-4 hover:bg-indigo-600 hover:text-white cursor-pointer flex justify-between items-center border-b">
                                    <span class="font-bold uppercase" x-text="p.name"></span>
                                    <span class="font-black" x-text="formatCurrency(p.price)"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Scanner Barcode</label>
                        <div class="flex shadow-sm rounded-2xl overflow-hidden">
                            <input type="text" x-model="manualBarcode" x-ref="manualBarcode" @keydown.enter.prevent="scanBarcode()" 
                                   class="flex-grow p-4 bg-white border-none outline-none font-mono font-bold text-indigo-600 uppercase">
                            <button @click="toggleScanner()" class="bg-indigo-600 text-white px-6 hover:bg-indigo-700 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2.5"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border overflow-hidden flex-grow flex flex-col cart-height">
                    <div class="overflow-y-auto flex-grow custom-scroll">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                <tr>
                                    <th class="p-5 text-left font-black text-gray-400 uppercase text-[10px]">Item</th>
                                    <th class="p-5 text-center font-black text-gray-400 uppercase text-[10px]">Qty</th>
                                    <th class="p-5 text-right font-black text-gray-400 uppercase text-[10px]">Subtotal</th>
                                    <th class="p-5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-if="cart.length === 0">
                                    <tr><td colspan="4" class="p-20 text-center text-gray-300 font-bold uppercase italic text-sm">Belum ada item terpilih</td></tr>
                                </template>
                                <template x-for="(item, index) in cart" :key="index">
                                    <tr class="hover:bg-indigo-50/50 transition-colors">
                                        <td class="p-5 font-black text-gray-800 uppercase" x-text="item.name"></td>
                                        <td class="p-5 text-center">
                                            <div class="inline-flex items-center space-x-2 bg-gray-50 p-1 rounded-xl">
                                                <button @click="updateQty(index, -1)" class="w-8 h-8 bg-white rounded-lg font-bold shadow-sm">-</button>
                                                <span class="font-black text-lg w-6 text-center text-indigo-600" x-text="item.quantity"></span>
                                                <button @click="updateQty(index, 1)" class="w-8 h-8 bg-white rounded-lg font-bold shadow-sm">+</button>
                                            </div>
                                        </td>
                                        <td class="p-5 text-right font-black text-indigo-600 text-lg" x-text="formatCurrency(item.price * item.quantity)"></td>
                                        <td class="p-5 text-center"><button @click="removeItem(index)" class="text-red-300 hover:text-red-500 font-black text-xl">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-2/5 bg-white p-6 border-l shadow-2xl flex flex-col justify-between">
                <div class="space-y-6">
                    <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Ringkasan Tagihan</h2>
                    <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 text-white text-center">
                        <p class="text-[10px] font-bold text-indigo-200 uppercase mb-1 tracking-widest leading-none">Total Bayar</p>
                        <div class="text-5xl font-black tracking-tighter leading-none">
                            <span class="text-xl mr-1 opacity-70">Rp</span><span x-text="formatNumber(total)"></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button @click="openPaymentModal()" :disabled="cart.length === 0" 
                            class="w-full bg-indigo-600 text-white py-6 rounded-[1.5rem] text-xl font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 disabled:bg-gray-200 transition-all uppercase tracking-widest text-sm">
                        PROSES BAYAR (B)
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL PEMBAYARAN --}}
        <div x-show="isPaymentModalOpen" class="fixed inset-0 bg-indigo-900/90 backdrop-blur-sm flex items-center justify-center z-[100] p-4" x-cloak x-transition>
            <div @click.away="!isLoading && (isPaymentModalOpen = false)" class="bg-white rounded-[3rem] p-10 w-full max-w-lg shadow-2xl">
                <div class="text-center mb-8">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Metode Pembayaran</p>
                    <h2 class="text-4xl font-black text-indigo-600 mt-2" x-text="formatCurrency(total)"></h2>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-indigo-600 text-white shadow-lg scale-105' : 'bg-gray-100 text-gray-400'" class="p-5 rounded-2xl font-black transition-all uppercase tracking-widest">TUNAI</button>
                    <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-indigo-600 text-white shadow-lg scale-105' : 'bg-gray-100 text-gray-400'" class="p-5 rounded-2xl font-black transition-all text-xs tracking-widest uppercase leading-tight">QRIS / DOKU</button>
                </div>

                <div x-show="paymentMethod === 'cash'" x-transition>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest text-left">Uang Diterima</label>
                    <input type="number" x-model.number="amountPaid" x-ref="amountPaidInput"
                           class="w-full text-4xl font-black p-6 bg-gray-50 border-2 border-indigo-100 rounded-3xl text-center outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-50 transition-all">
                    
                    <div x-show="amountPaid > 0" class="mt-4 p-4 rounded-2xl border-2 border-dashed border-indigo-200 text-center pulse-green" x-transition>
                         <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest leading-none">Uang Kembali</p>
                         <p class="text-3xl font-black text-indigo-600" x-text="formatCurrency(change)"></p>
                    </div>
                </div>

                <div class="mt-10 flex space-x-4">
                    <button @click="isPaymentModalOpen = false" :disabled="isLoading" class="flex-1 py-5 font-black text-gray-400 uppercase text-xs tracking-widest">BATAL</button>
                    <button @click="completeTransaction()" :disabled="isLoading || (paymentMethod === 'cash' && amountPaid < total)" 
                            class="flex-[2.5] bg-green-600 text-white py-5 rounded-[1.5rem] font-black text-lg uppercase shadow-lg hover:bg-green-700 transition-all flex justify-center items-center">
                        <template x-if="isLoading"><div class="loader h-6 w-6 border-4"></div></template>
                        <template x-if="!isLoading"><span>KONFIRMASI</span></template>
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL QRIS DINAMIS --}}
        <div x-show="isQrisModalOpen" class="fixed inset-0 bg-indigo-950/95 backdrop-blur-md flex items-center justify-center z-[150] p-4" x-cloak x-transition>
            <div class="bg-white rounded-[3.5rem] p-4 w-full max-w-lg text-center shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600"></div>
                
                {{-- PERBAIKAN: Bungkus dengan ID qris-container untuk fungsi cetak --}}
                <div id="qris-container">
                    <div id="qris-placeholder" class="bg-gray-50 rounded-[2.5rem] overflow-hidden border-4 border-gray-100 h-[500px] flex items-center justify-center relative">
                        <p class="animate-pulse font-bold text-gray-300">Menyiapkan QRIS...</p>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    {{-- DATA TERSEMBUNYI UNTUK DIBACA FUNGSI CETAK --}}
                    <div class="hidden">
                        <span id="invoice-display" x-text="lastTransactionNumber"></span>
                        <span id="amount-display" x-text="formatCurrency(total)"></span>
                    </div>

                    <button @click="confirmCancelTransaction()" class="w-full bg-red-50 text-red-500 py-4 rounded-2xl font-black hover:bg-red-100 transition-all uppercase tracking-widest text-[10px]">
                        BATALKAN PEMBAYARAN INI
                    </button>
                    
                    {{-- TOMBOL CETAK QRIS --}}
                    <button @click="printQrisCode()" class="w-full py-4 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase shadow-lg hover:bg-indigo-700 transition-all flex items-center justify-center">
                        <svg style="width:16px;height:16px;" class="mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak QRIS (Thermal)
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
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-widest leading-none">LUNAS!</h3>
                    <div class="space-y-3">
                        <button @click="isMobileApp() ? printReceiptBluetooth() : printReceiptWeb()" :disabled="isPrinting"
                                class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg hover:bg-indigo-700 flex justify-center items-center">
                            <span x-show="!isPrinting">CETAK NOTA</span>
                            <div x-show="isPrinting" class="loader h-4 w-4 border-2"></div>
                        </button>
                        <button @click="location.reload()" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-black transition-all">BARU (ESC)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <iframe id="print-iframe" style="display:none;"></iframe>

    <script>
        {{-- LOGIKA PEMUTARAN AUDIO MP3 --}}
        function playPaymentSound(soundUrl, isMuted) {
            if (isMuted || !soundUrl || soundUrl === "") return;
            const audio = new Audio(soundUrl);
            audio.play().catch(e => {
                console.error("Audio Playback Error:", e);
            });
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

                cart: [], searchQuery: '', searchResults: [], manualBarcode: '',
                isPaymentModalOpen: false, isQrisModalOpen: false, 
                paymentMethod: 'cash', amountPaid: '', isSuccessModalOpen: false,
                lastSaleId: null, lastTransactionNumber: null,
                isLoading: false, isPrinting: false, currentTime: '',
                paymentStatus: 'pending', statusInterval: null,
                isMuted: !(@json($shop['is_voice_enabled'] ?? true)),

                get total() { return this.cart.reduce((a, b) => a + (b.price * b.quantity), 0); },
                get change() { return Math.max(0, (this.amountPaid || 0) - this.total); },

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    this.codeReader = new ZXing.BrowserMultiFormatReader();
                    this.$nextTick(() => this.$refs.manualBarcode?.focus());
                },

                updateTime() { this.currentTime = new Date().toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }); },
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
                    if (!this.shop.payment_sound) {
                        Swal.fire({ title: 'MP3 Kosong', text: 'Upload file MP3 di Admin dulu!', icon: 'warning', toast: true, position: 'top-end', timer: 3000 });
                        return;
                    }
                    Swal.fire({ title: 'Mengetes Audio...', icon: 'info', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                    playPaymentSound(this.shop.payment_sound, false);
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
                    this.unlockAudio();

                    this.isLoading = true;
                    const payload = {
                        items: this.cart.map(i => ({ id: i.id, quantity: i.quantity })),
                        total: this.total, payment_method: this.paymentMethod,
                        amount_paid: this.paymentMethod === 'cash' ? this.amountPaid : this.total,
                        _token: '{{ csrf_token() }}'
                    };

                    try {
                        const res = await fetch(`{{ route('cashier.pos.store') }}`, {
                            method: 'POST', headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');

                        this.lastSaleId = data.sale.id;
                        this.lastTransactionNumber = data.sale.transaction_number;
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
                            setTimeout(() => playPaymentSound(this.shop.payment_sound, this.isMuted), 300);
                            this.paymentStatus = 'success';
                            this.isSuccessModalOpen = true;
                            if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 1000);
                        }
                    } catch (e) { Swal.fire('Gagal', e.message, 'error'); } finally { this.isLoading = false; }
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
                                playPaymentSound(this.shop.payment_sound, this.isMuted);
                                if (this.isMobileApp()) setTimeout(() => this.printReceiptBluetooth(), 500);
                            }
                        } catch (e) { }
                    }, 3000); 
                },

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
        
        {{-- FUNGSI CETAK QRIS (Thermal) --}}
        function printQrisCode() {
            // Mengambil element gambar QRIS di dalam placeholder
            const placeholder = document.querySelector('#qris-placeholder');
            let qrSource = '';
            
            // Cek jika QRIS berupa Iframe (Doku) atau Canvas/Img (QRCode.js)
            const iframe = placeholder.querySelector('iframe');
            const img = placeholder.querySelector('img');
            const canvas = placeholder.querySelector('canvas');

            if (img) qrSource = img.src;
            else if (canvas) qrSource = canvas.toDataURL("image/png");
            else if (iframe) qrSource = iframe.src; // Catatan: Iframe mungkin terbatas kebijakan CORS

            const invoice = document.getElementById('invoice-display').innerText;
            const amount = document.getElementById('amount-display').innerText;

            const printWindow = window.open('', '_blank', 'width=300,height=600');
            printWindow.document.write(`
                <html>
                <head>
                    <style>
                        body { font-family: 'Courier New', Courier, monospace; text-align: center; padding: 10px; width: 58mm; }
                        .bold { font-weight: bold; font-size: 16px; }
                        img { width: 100%; margin: 10px 0; }
                        hr { border-top: 1px dashed black; margin: 10px 0; }
                        .info { font-size: 12px; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="bold">TOKO ANANDA</div>
                    <div class="info">Pembayaran QRIS DOKU</div>
                    <hr>
                    <div class="info">${invoice}</div>
                    ${qrSource ? `<img src="${qrSource}">` : '<div style="padding:20px; border:1px solid #000;">Scan QR di Layar</div>'}
                    <div class="bold">TOTAL: ${amount}</div>
                    <hr>
                    <div style="font-size: 10px;">Silakan scan untuk bayar.</div>
                    <div style="font-size: 9px; margin-top:10px;">SIKANDA POS</div>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</body>
</html>