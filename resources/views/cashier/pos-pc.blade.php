<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPOS - Mode PC</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-900 text-gray-300">

    <div x-data="posSystem()" x-init="init()" 
         @keydown.window.escape.prevent="handleEscapeKey()"
         @keydown.window.enter="handleEnterKey($event)"
         @keydown.window="handleGeneralKeys($event)"
         class="flex flex-col lg:flex-row h-screen">

        <!-- Bagian Kiri: Keranjang Belanja -->
        <div class="w-full lg:w-3/5 p-4 lg:p-6 flex flex-col">
             <header class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">SmartPOS (Mode PC)</h1>
                    <p class="text-gray-400">Selamat datang, {{ Auth::user()->name ?? 'Kasir' }}!</p>
                </div>
                <div class="flex items-center space-x-3">
                    @php
                        $unconfirmedCount = \App\Models\Order::where('status', 'paid')->count();
                    @endphp
                    <a href="{{ route('admin.orders.index') }}" 
                       class="inline-flex items-center space-x-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-lg shadow transition">
                        <span>🛒 Pesanan Online</span>
                        <span id="posPcOrderBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white shadow animate-pulse {{ $unconfirmedCount > 0 ? '' : 'hidden' }}">
                            {{ $unconfirmedCount }}
                        </span>
                    </a>
                    <a href="{{ route('cashier.pos.index') }}" class="bg-gray-700 text-white px-3.5 py-2 rounded-lg shadow hover:bg-gray-600 transition text-xs font-bold uppercase">Mode Normal</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-gray-600 text-white px-3.5 py-2 rounded-lg shadow hover:bg-gray-500 transition text-xs font-bold uppercase">Logout</button>
                    </form>
                </div>
            </header>

            <div class="mb-4">
                <form @submit.prevent="addProductByManualBarcode()">
                    <label for="manual_barcode" class="block text-sm font-medium text-gray-400 mb-1">Input Barcode (Otomatis)</label>
                    <div class="flex">
                        <input type="text" id="manual_barcode" x-model="manualBarcode" @input.debounce.50ms="handleBarcodeInputChange()" x-ref="manualBarcode" placeholder="Scan atau masukkan kode barcode..." class="flex-grow p-2 bg-gray-800 border border-gray-700 rounded-l-lg text-white">
                        <button type="submit" class="bg-gray-700 text-white px-4 rounded-r-lg hover:bg-gray-600">Tambah</button>
                    </div>
                </form>
            </div>
            
            <div class="mb-4 space-y-2">
                <div class="relative" @click.away="searchResults = []">
                    <label for="product_search" class="block text-sm font-medium text-gray-400 mb-1">Cari Produk Manual</label>
                    <input type="text" id="product_search" x-model="searchQuery" @input.debounce.300ms="searchProducts()" autocomplete="off" placeholder="Ketik nama produk..." class="w-full p-2 bg-gray-800 border border-gray-700 rounded-lg text-white">
                    <div x-show="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-gray-700 rounded-md shadow-lg" x-cloak>
                        <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                            <template x-for="product in searchResults" :key="product.id">
                                <li @click="selectProduct(product)" class="text-gray-200 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                    <span class="font-normal block truncate" x-text="product.name"></span>
                                    <span class="text-gray-400 absolute inset-y-0 right-0 flex items-center pr-4" x-text="formatCurrency(product.price)"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div x-show="selectedProduct" class="p-4 border border-gray-700 rounded-lg bg-gray-800" x-transition>
                    <p class="font-semibold text-lg text-white" x-text="selectedProduct?.name"></p>
                    <form @submit.prevent="addProductManually()" class="flex items-end space-x-4 mt-2">
                        <div class="flex-grow">
                            <label for="manual_quantity" class="block text-sm font-medium text-gray-400">Jumlah</label>
                            <input type="number" id="manual_quantity" x-model.number="manualQuantity" x-ref="manualQuantityInput" min="1" class="mt-1 w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        </div>
                        <div>
                            <button type="submit" class="h-full px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-md flex-grow overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="p-3 text-left text-sm font-semibold text-gray-300">Produk</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-300">Jumlah</th>
                            <th class="p-3 text-right text-sm font-semibold text-gray-300">Subtotal</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <template x-if="cart.length === 0">
                            <tr><td colspan="4" class="p-6 text-center text-gray-500">Keranjang masih kosong.</td></tr>
                        </template>
                        <template x-for="(item, index) in cart" :key="item.id">
                            <tr>
                                <td class="p-3">
                                    <p class="font-medium text-white" x-text="item.name"></p>
                                    <p class="text-sm text-gray-400" x-text="formatCurrency(item.price)"></p>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center">
                                        <button @click="updateQuantity(index, -1)" class="px-2 py-1 bg-gray-600 rounded-l">-</button>
                                        <span class="px-4 py-1 bg-gray-800 border-t border-b border-gray-700" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(index, 1)" class="px-2 py-1 bg-gray-600 rounded-r">+</button>
                                    </div>
                                </td>
                                <td class="p-3 text-right font-semibold text-white" x-text="formatCurrency(item.price * item.quantity)"></td>
                                <td class="p-3 text-center">
                                    <button @click="removeItem(index)" class="text-red-400 hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian Kanan & Modal -->
        <div class="w-full lg:w-2/5 bg-gray-800 p-4 lg:p-6 border-l border-gray-700 flex flex-col justify-between">
             <div>
                <h2 class="text-xl font-bold text-white mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-400"><span>Subtotal</span><span x-text="formatCurrency(subtotal)"></span></div>
                    <div class="flex justify-between text-gray-400"><span>Diskon</span><span class="text-green-400" x-text="'-' + formatCurrency(totalDiscount)"></span></div>
                    <hr class="border-gray-700">
                    <div class="flex justify-between text-2xl font-bold text-white"><span>Total</span><span x-text="formatCurrency(total)"></span></div>
                </div>
            </div>
            <div class="mt-6">
                 <button @click="openPaymentModal()" :disabled="cart.length === 0" class="w-full bg-green-600 text-white text-lg font-bold py-4 rounded-lg shadow-lg hover:bg-green-500 transition disabled:bg-gray-500">
                    BAYAR (Enter)
                </button>
            </div>
        </div>

        <!-- Modal Pembayaran -->
        <div x-show="isPaymentModalOpen" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" x-cloak>
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl p-6 w-full max-w-md">
                <h2 class="text-2xl font-bold text-center text-white mb-4">Pembayaran</h2>
                <div class="text-center mb-6">
                    <p class="text-gray-400">Total Tagihan</p>
                    <p class="text-4xl font-extrabold text-indigo-400" x-text="formatCurrency(total)"></p>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <button @click="selectPaymentMethod('cash')" x-ref="cashButton" :class="{'bg-indigo-600 text-white': paymentMethod === 'cash', 'bg-gray-700 text-gray-300': paymentMethod !== 'cash'}" class="p-4 rounded-lg text-center font-semibold transition"><u>T</u>unai (Cash)</button>
                    <button @click="selectPaymentMethod('qris')" x-ref="qrisButton" :class="{'bg-indigo-600 text-white': paymentMethod === 'qris', 'bg-gray-700 text-gray-300': paymentMethod !== 'qris'}" class="p-4 rounded-lg text-center font-semibold transition"><u>Q</u>RIS</button>
                </div>
                <div x-show="paymentMethod">
                    <div x-show="paymentMethod === 'cash'" class="space-y-4">
                        <div>
                            <label for="amountPaid" class="block text-sm font-medium text-gray-400">Uang Diterima</label>
                            <input type="number" id="amountPaid" x-model.number="amountPaid" x-ref="amountPaidInput" placeholder="Contoh: 50000" class="mt-1 block w-full text-center text-xl p-2 bg-gray-700 border-gray-600 rounded-md shadow-sm text-white">
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg text-center">
                            <p class="text-sm text-gray-400">Kembalian</p>
                            <p class="text-2xl font-bold text-white" x-text="formatCurrency(change)"></p>
                        </div>
                        <button @click="completeTransaction()" :disabled="!isCashPaymentValid || isLoading" class="w-full mt-4 bg-green-600 text-white py-3 rounded-lg font-bold text-lg disabled:bg-gray-500 flex items-center justify-center">
                            <span x-show="!isLoading">Selesaikan Transaksi (Enter)</span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>
                    <div x-show="paymentMethod === 'qris'" class="text-center">
                        <img src="https://placehold.co/300x300/1f2937/ffffff?text=SCAN+QRIS" alt="QRIS Code" class="mx-auto rounded-lg">
                        <p class="mt-4 text-gray-400">Pindai kode QR untuk membayar.</p>
                        <button @click="completeTransaction()" :disabled="isLoading" class="w-full mt-4 bg-green-600 text-white py-3 rounded-lg font-bold text-lg disabled:bg-gray-500 flex items-center justify-center">
                            <span x-show="!isLoading">Konfirmasi Pembayaran (Enter)</span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Struk/Sukses -->
        <div x-show="isSuccessModalOpen" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" x-cloak>
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-200">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-white mt-4">Pembayaran Berhasil!</h3>
                <div class="mt-6 space-y-3">
                    <a :href="receiptUrl" target="_blank" x-ref="downloadButton" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 font-medium text-white hover:bg-indigo-500"><u>D</u>ownload Struk (PDF)</a>
                    <a :href="whatsappUrl" target="_blank" x-ref="whatsappButton" class="inline-flex justify-center w-full rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 font-medium text-white hover:bg-gray-600">Kirim via <u>W</u>hatsApp</a>
                    <button @click="startNewTransaction()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 font-medium text-white hover:bg-gray-600">Transaksi Baru (Enter)</button>
                </div>
            </div>
        </div>
        
        <!-- Notifikasi -->
        <div x-show="notification.show" x-transition :class="notification.type === 'error' ? 'bg-red-600' : 'bg-gray-800'" class="fixed bottom-5 right-5 text-white py-3 px-5 rounded-lg shadow-lg" x-cloak>
            <p x-text="notification.message"></p>
        </div>
    </div>

    <script>
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

    function posSystem() {
        return {
            cart: [], isLoading: false, notification: { show: false, message: '', type: 'success' },
            manualBarcode: '', searchQuery: '', searchResults: [], selectedProduct: null, manualQuantity: 1,
            isPaymentModalOpen: false, paymentMethod: null, amountPaid: '',
            isSuccessModalOpen: false, lastSaleId: null, lastTotalAmount: 0,
            
            init() {
                this.$nextTick(() => this.$refs.manualBarcode.focus());
                this.startOnlineOrderPolling();
            },

            startOnlineOrderPolling() {
                let lastNotified = {{ $unconfirmedCount ?? 0 }};
                const checkOrders = async () => {
                    try {
                        const res = await fetch("{{ route('orders.realtime-check') }}");
                        if (res.ok) {
                            const data = await res.json();
                            const badge = document.getElementById('posPcOrderBadge');
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
            
            handleEnterKey(event) {
                if (this.isSuccessModalOpen) {
                    this.startNewTransaction();
                    return;
                }
                if (this.isPaymentModalOpen) {
                    if ((this.paymentMethod === 'cash' && this.isCashPaymentValid) || this.paymentMethod === 'qris') {
                        this.completeTransaction();
                    }
                    return;
                }
                if (this.cart.length > 0 && document.activeElement.tagName !== 'INPUT') {
                    this.openPaymentModal();
                }
            },

            handleEscapeKey() {
                if (this.isSuccessModalOpen) return;
                if (this.isPaymentModalOpen) {
                    this.resetPayment();
                } else if (this.searchResults.length > 0) {
                    this.searchResults = [];
                }
            },

            handleGeneralKeys(event) {
                if (['INPUT', 'TEXTAREA'].includes(event.target.tagName)) return;

                if (this.isPaymentModalOpen && !this.isSuccessModalOpen) {
                    if (event.key.toLowerCase() === 't') { this.selectPaymentMethod('cash'); } 
                    else if (event.key.toLowerCase() === 'q') { this.selectPaymentMethod('qris'); }
                }

                if (this.isSuccessModalOpen) {
                    if (event.key.toLowerCase() === 'd') { this.$refs.downloadButton.click(); } 
                    else if (event.key.toLowerCase() === 'w') { this.$refs.whatsappButton.click(); }
                }
            },

            handleBarcodeInputChange() {
                if (this.manualBarcode.length >= 13) {
                    this.addProductByManualBarcode();
                }
            },

            formatCurrency(amount) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount); },
            
            async addProductByBarcode(barcode) {
                try {
                    const response = await fetch(`{{ route('cashier.pos.checkProduct') }}?barcode=${barcode}`);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Terjadi kesalahan');
                    }
                    const product = await response.json();
                    this.addToCart(product);
                    this.showNotification(`'${product.name}' ditambahkan.`);
                } catch (error) {
                    this.showNotification(error.message, 'error');
                }
            },

            addToCart(product) {
                const existingItem = this.cart.find(item => item.id === product.id);
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    this.cart.push({ ...product, quantity: 1 });
                }
            },

            addProductByManualBarcode() {
                if (!this.manualBarcode.trim()) return;
                this.addProductByBarcode(this.manualBarcode.trim());
                this.manualBarcode = '';
            },

            async searchProducts() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                try {
                    const response = await fetch(`{{ route('cashier.pos.search') }}?query=${this.searchQuery}`);
                    this.searchResults = await response.json();
                } catch (error) { console.error('Error searching products:', error); this.searchResults = []; }
            },

            selectProduct(product) {
                this.selectedProduct = product;
                this.searchQuery = product.name;
                this.searchResults = [];
                this.$nextTick(() => this.$refs.manualQuantityInput.focus());
            },

            addProductManually() {
                if (!this.selectedProduct || this.manualQuantity < 1) return;
                const productToAdd = { ...this.selectedProduct };
                const quantity = parseInt(this.manualQuantity);
                const existingItem = this.cart.find(item => item.id === productToAdd.id);
                if (existingItem) { existingItem.quantity += quantity; } else { productToAdd.quantity = quantity; this.cart.push(productToAdd); }
                this.showNotification(`${quantity} x '${productToAdd.name}' ditambahkan.`);
                this.resetManualSearch();
            },

            resetManualSearch() { this.selectedProduct = null; this.searchQuery = ''; this.searchResults = []; this.manualQuantity = 1; },
            updateQuantity(index, change) { if (this.cart[index].quantity + change > 0) { this.cart[index].quantity += change; } else { this.removeItem(index); } },
            removeItem(index) { this.cart.splice(index, 1); },
            showNotification(message, type = 'success') { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => { this.notification.show = false; }, 3000); },
            
            openPaymentModal() {
                if (this.cart.length === 0) return;
                this.isPaymentModalOpen = true;
                this.paymentMethod = null;
                this.amountPaid = '';
            },
            selectPaymentMethod(method) {
                this.paymentMethod = method;
                if (method === 'cash') {
                    this.$nextTick(() => { this.$refs.amountPaidInput.focus(); });
                }
            },
            resetPayment() {
                this.isPaymentModalOpen = false;
                this.paymentMethod = null;
                this.amountPaid = '';
                this.$nextTick(() => this.$refs.manualBarcode.focus());
            },
            async completeTransaction() {
                if (this.isLoading) return;
                this.isLoading = true;
                const payload = { 
                    items: this.cart.map(item => ({ id: item.id, quantity: item.quantity })),
                    payment_method: this.paymentMethod,
                    amount_paid: this.amountPaid,
                    _token: '{{ csrf_token() }}' 
                };
                try {
                    const response = await fetch('{{ route("cashier.pos.store") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.error || errorData.message || 'Gagal menyimpan transaksi';
                        if (errorData.errors) {
                            errorMessage += `\n- ${Object.values(errorData.errors)[0][0]}`;
                        }
                        throw new Error(errorMessage);
                    }
                    const result = await response.json();
                    
                    this.lastSaleId = result.sale_id;
                    this.lastTotalAmount = this.total;
                    this.isSuccessModalOpen = true;
                    this.resetPayment();

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal',
                        text: error.message,
                        confirmButtonColor: '#EE2737'
                    });
                } finally {
                    this.isLoading = false;
                }
            },
            startNewTransaction() {
                this.isSuccessModalOpen = false;
                this.cart = [];
                this.lastSaleId = null;
                this.lastTotalAmount = 0;
                this.$nextTick(() => this.$refs.manualBarcode.focus());
            }
        }
    }
    </script>
</body>
</html>