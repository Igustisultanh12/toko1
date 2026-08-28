<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPOS - Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- PERBAIKAN: Memindahkan script ke bagian bawah sebelum body berakhir -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        #reader {
            width: 100%;
            border: 2px dashed #cbd5e1;
            border-radius: 0.5rem;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div x-data="posSystem" 
         class="flex flex-col lg:flex-row h-screen">

        <!-- Bagian Kiri: Keranjang Belanja -->
        <div class="w-full lg:w-3/5 p-4 lg:p-6 flex flex-col">
             <header class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">SmartPOS</h1>
                    <p class="text-gray-500">Selamat datang, {{ Auth::user()->name ?? 'Kasir' }}!</p>
                </div>
                <div class="flex items-center space-x-4">
                     <a href="{{ route('cashier.pos.pcIndex') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg shadow hover:bg-black transition">
                       Mode PC
                       </a>
                    <button @click="toggleContinuousScan()" :class="{'bg-red-600 hover:bg-red-700': isContinuousScanActive, 'bg-indigo-600 hover:bg-indigo-700': !isContinuousScanActive}" class="text-white px-4 py-2 rounded-lg shadow transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span x-text="isContinuousScanActive ? 'Hentikan Scan' : 'Mulai Scan'"></span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition">Logout</button>
                    </form>
                </div>
            </header>

            <div x-show="isContinuousScanActive" class="mb-4" x-transition>
                <div id="reader"></div>
            </div>

            <div class="mb-4">
                <label for="manual_barcode" class="block text-sm font-medium text-gray-700 mb-1">Input Barcode Manual</label>
                <div class="flex">
                    <input type="text" id="manual_barcode" x-model="manualBarcode" @keydown.enter.prevent="addProductByManualBarcode()" placeholder="Masukkan kode barcode..." class="flex-grow p-2 border border-gray-300 rounded-l-lg">
                    <button @click="addProductByManualBarcode()" class="bg-gray-700 text-white px-4 rounded-r-lg hover:bg-gray-800">Tambah</button>
                </div>
            </div>

            <div class="mb-4 space-y-2">
                <div class="relative" @click.away="searchResults = []">
                    <label for="product_search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk Manual</label>
                    <input type="text" id="product_search" x-model="searchQuery" @input.debounce.300ms="searchProducts()" autocomplete="off" placeholder="Ketik nama produk..." class="w-full p-2 border border-gray-300 rounded-lg">
                    <div x-show="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg" x-cloak>
                        <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                            <template x-for="product in searchResults" :key="product.id">
                                <li @click="selectProduct(product)" class="text-gray-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                    <span class="font-normal block truncate" x-text="product.name"></span>
                                    <span class="text-gray-500 absolute inset-y-0 right-0 flex items-center pr-4" x-text="formatCurrency(product.price)"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div x-show="selectedProduct" class="p-4 border rounded-lg bg-indigo-50" x-transition>
                    <p class="font-semibold text-lg" x-text="selectedProduct?.name"></p>
                    <div class="flex items-end space-x-4 mt-2">
                        <div class="flex-grow">
                            <label for="manual_quantity" class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" id="manual_quantity" x-model.number="manualQuantity" x-ref="manualQuantityInput" @keydown.enter.prevent="addProductManually()" min="1" class="mt-1 w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <button @click="addProductManually()" class="h-full px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tambah</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md flex-grow overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Produk</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Jumlah</th>
                            <th class="p-3 text-right text-sm font-semibold text-gray-600">Subtotal</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="cart.length === 0">
                            <tr><td colspan="4" class="p-6 text-center text-gray-400">Keranjang masih kosong.</td></tr>
                        </template>
                        <template x-for="(item, index) in cart" :key="item.id">
                            <tr class="border-b">
                                <td class="p-3">
                                    <p class="font-medium text-gray-800" x-text="item.name"></p>
                                    <p class="text-sm text-gray-500" x-text="formatCurrency(item.price)"></p>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center">
                                        <button @click="updateQuantity(index, -1)" class="px-2 py-1 bg-gray-200 rounded-l">-</button>
                                        <span class="px-4 py-1 bg-white border-t border-b" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(index, 1)" class="px-2 py-1 bg-gray-200 rounded-r">+</button>
                                    </div>
                                </td>
                                <td class="p-3 text-right font-semibold" x-text="formatCurrency(item.price * item.quantity)"></td>
                                <td class="p-3 text-center">
                                    <button @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian Kanan & Modal Pembayaran -->
        <div class="w-full lg:w-2/5 bg-white p-4 lg:p-6 border-l border-gray-200 flex flex-col justify-between">
             <div>
                <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span x-text="formatCurrency(subtotal)"></span></div>
                    <div class="flex justify-between text-gray-600"><span>Diskon</span><span class="text-green-600" x-text="'-' + formatCurrency(totalDiscount)"></span></div>
                    <hr>
                    <div class="flex justify-between text-2xl font-bold text-gray-900"><span>Total</span><span x-text="formatCurrency(total)"></span></div>
                </div>
            </div>
            <div class="mt-6">
                 <button @click="openPaymentModal()" :disabled="cart.length === 0" class="w-full bg-green-600 text-white text-lg font-bold py-4 rounded-lg shadow-lg hover:bg-green-700 transition disabled:bg-gray-400">
                    BAYAR
                </button>
            </div>
        </div>

        <!-- Modal Pembayaran -->
        <div x-show="isPaymentModalOpen" @keydown.escape.window="resetPayment()" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-cloak>
            <div @click.away="resetPayment()" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h2 class="text-2xl font-bold text-center mb-4">Pembayaran</h2>
                <div class="text-center mb-6">
                    <p class="text-gray-600">Total Tagihan</p>
                    <p class="text-4xl font-extrabold text-indigo-600" x-text="formatCurrency(total)"></p>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <button @click="selectPaymentMethod('cash')" :class="{'bg-indigo-600 text-white': paymentMethod === 'cash', 'bg-gray-200': paymentMethod !== 'cash'}" class="p-4 rounded-lg text-center font-semibold transition">Tunai (Cash)</button>
                    <button @click="selectPaymentMethod('qris')" :class="{'bg-indigo-600 text-white': paymentMethod === 'qris', 'bg-gray-200': paymentMethod !== 'qris'}" class="p-4 rounded-lg text-center font-semibold transition">QRIS</button>
                </div>
                <div x-show="paymentMethod">
                    <div x-show="paymentMethod === 'cash'" class="space-y-4">
                        <div>
                            <label for="amountPaid" class="block text-sm font-medium text-gray-700">Uang Diterima</label>
                            <input type="number" id="amountPaid" x-model.number="amountPaid" x-ref="amountPaidInput" placeholder="Contoh: 50000" class="mt-1 block w-full text-center text-xl p-2 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="text-sm text-gray-600">Kembalian</p>
                            <p class="text-2xl font-bold" x-text="formatCurrency(change)"></p>
                        </div>
                        <button @click="completeTransaction()" :disabled="!isCashPaymentValid || isLoading" class="w-full mt-4 bg-green-600 text-white py-3 rounded-lg font-bold text-lg disabled:bg-gray-400 flex items-center justify-center">
                            <span x-show="!isLoading">Selesaikan Transaksi</span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>
                    <div x-show="paymentMethod === 'qris'" class="text-center">
                        <img src="https://placehold.co/300x300/ffffff/000000?text=SCAN+QRIS" alt="QRIS Code" class="mx-auto rounded-lg">
                        <p class="mt-4 text-gray-600">Pindai kode QR untuk membayar.</p>
                        <button @click="completeTransaction()" :disabled="isLoading" class="w-full mt-4 bg-green-600 text-white py-3 rounded-lg font-bold text-lg disabled:bg-gray-400 flex items-center justify-center">
                            <span x-show="!isLoading">Konfirmasi Pembayaran QRIS</span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Struk/Sukses -->
        <div x-show="isSuccessModalOpen" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Pembayaran Berhasil!</h3>
                <div class="mt-6 space-y-3">
                    <button @click="directPrintReceipt()" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700">Print Struk (Thermal)</button>
                    <a :href="receiptUrl" target="_blank" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 font-medium text-white hover:bg-indigo-700">Download Struk (PDF)</a>
                    <a :href="whatsappUrl" target="_blank" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50">Kirim via WhatsApp</a>
                    <button @click="startNewTransaction()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50">Transaksi Baru</button>
                </div>
            </div>
        </div>
        
        <!-- Notifikasi -->
        <div x-show="notification.show" x-transition :class="notification.type === 'error' ? 'bg-red-600' : 'bg-gray-800'" class="fixed bottom-5 right-5 text-white py-3 px-5 rounded-lg shadow-lg" x-cloak>
            <p x-text="notification.message"></p>
        </div>
    </div>

    <!-- PERBAIKAN: Memindahkan semua script ke sini dan menggunakan metode inisialisasi yang direkomendasikan -->
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                // State
                cart: [], isLoading: false, notification: { show: false, message: '', type: 'success' },
                isContinuousScanActive: false,
                manualBarcode: '', searchQuery: '', searchResults: [], selectedProduct: null, manualQuantity: 1,
                isPaymentModalOpen: false, paymentMethod: null, amountPaid: '',
                isSuccessModalOpen: false, lastSaleId: null, lastTotalAmount: 0,
                html5QrCode: null,
                
                // URLs akan diisi dari Blade
                urls: {
                    baseUrl: '{{ url("/") }}',
                    checkProduct: '{{ route("cashier.pos.checkProduct") }}',
                    searchProducts: '{{ route("cashier.pos.search") }}',
                    storeTransaction: '{{ route("cashier.pos.store") }}',
                },

                // Computed Properties
                get subtotal() { return this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0); },
                get totalDiscount() { return this.cart.reduce((acc, item) => acc + ((item.price * (item.discount_percent || 0 / 100)) * item.quantity), 0); },
                get total() { return this.subtotal - this.totalDiscount; },
                get change() {
                    const paid = parseFloat(this.amountPaid);
                    if (isNaN(paid) || paid < this.total) return 0;
                    return paid - this.total;
                },
                get isCashPaymentValid() {
                    const paid = parseFloat(this.amountPaid);
                    return !isNaN(paid) && paid >= this.total;
                },
                get receiptUrl() { return this.lastSaleId ? `${this.urls.baseUrl}/cashier/receipt/${this.lastSaleId}` : '#'; },
                get printUrl() { return this.lastSaleId ? `${this.urls.baseUrl}/cashier/receipt/${this.lastSaleId}/print` : '#'; },
                get whatsappUrl() {
                    if (!this.lastSaleId) return '#';
                    const message = `Terima kasih telah berbelanja.\nNo. Struk: ${this.lastSaleId}\nTotal: ${this.formatCurrency(this.lastTotalAmount)}\n\nLihat struk: ${this.receiptUrl}`;
                    return `https://wa.me/?text=${encodeURIComponent(message)}`;
                },

                // --- Fungsi-fungsi UTAMA ---
                init() {
                    console.log('POS System Initialized.');
                },
                formatCurrency(amount) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount); },
                
                directPrintReceipt() {
                    if (!this.lastSaleId) return;
                    const printWindow = window.open(this.printUrl, '_blank', 'width=300,height=500');
                    printWindow.focus();
                },

                addToCart(product, quantity = 1) {
                    if (!product || !product.id) {
                        console.error("Produk tidak valid saat ditambahkan ke keranjang.");
                        return;
                    }
                    const existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        this.cart.push({ ...product, quantity: quantity });
                    }
                },

                async addProductByBarcode(barcode) {
                    try {
                        const response = await fetch(`${this.urls.checkProduct}?barcode=${barcode}`);
                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error || 'Terjadi kesalahan');
                        }
                        const product = await response.json();
                        this.addToCart(product, 1);
                        this.showNotification(`'${product.name}' ditambahkan.`);
                    } catch (error) {
                        this.showNotification(error.message, 'error');
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
                        const response = await fetch(`${this.urls.searchProducts}?query=${this.searchQuery}`);
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
                    const quantity = parseInt(this.manualQuantity);
                    this.addToCart(this.selectedProduct, quantity);
                    this.showNotification(`${quantity} x '${this.selectedProduct.name}' ditambahkan.`);
                    this.resetManualSearch();
                },

                resetManualSearch() { this.selectedProduct = null; this.searchQuery = ''; this.searchResults = []; this.manualQuantity = 1; },
                updateQuantity(index, change) { if (this.cart[index].quantity + change > 0) { this.cart[index].quantity += change; } else { this.removeItem(index); } },
                removeItem(index) { this.cart.splice(index, 1); },
                showNotification(message, type = 'success') { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => { this.notification.show = false; }, 3000); },
                
                toggleContinuousScan() {
                    if (this.isContinuousScanActive) {
                        this.stopContinuousScanner();
                    } else {
                        this.startContinuousScanner();
                    }
                },
                startContinuousScanner() {
                    this.isContinuousScanActive = true;
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode("reader");
                        const onScanSuccess = (decodedText, decodedResult) => {
                            this.addProductByBarcode(decodedText);
                        };
                        const config = { fps: 10, qrbox: { width: 250, height: 150 } };
                        this.html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                            .catch(err => {
                                console.error("Gagal memulai kamera belakang, mencoba kamera depan.", err);
                                this.html5QrCode.start({ facingMode: "user" }, config, onScanSuccess)
                                    .catch(err => {
                                        alert("Tidak dapat memulai kamera scanner. Pastikan Anda memberikan izin kamera.");
                                        this.isContinuousScanActive = false;
                                    });
                            });
                    });
                },
                stopContinuousScanner() {
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop()
                            .then(ignore => { console.log("Scanner dihentikan."); })
                            .catch(err => console.error("Gagal menghentikan scanner.", err));
                    }
                    this.isContinuousScanActive = false;
                },
                
                openPaymentModal() { if (this.cart.length === 0) return; this.stopContinuousScanner(); this.isPaymentModalOpen = true; this.paymentMethod = null; this.amountPaid = ''; },
                selectPaymentMethod(method) { this.paymentMethod = method; if (method === 'cash') { this.$nextTick(() => { this.$refs.amountPaidInput.focus(); }); } },
                resetPayment() { this.isPaymentModalOpen = false; this.paymentMethod = null; this.amountPaid = ''; },
                async completeTransaction() {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    const payload = { items: this.cart.map(item => ({ id: item.id, quantity: item.quantity })), total: this.total, payment_method: this.paymentMethod, amount_paid: this.amountPaid, _token: '{{ csrf_token() }}' };
                    try {
                        const response = await fetch(this.urls.storeTransaction, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error || 'Gagal menyimpan transaksi');
                        }
                        const result = await response.json();
                        
                        this.lastSaleId = result.sale.id;
                        this.lastTotalAmount = this.total;
                        this.isSuccessModalOpen = true;
                        this.resetPayment();
                    } catch (error) {
                        alert(`Error: ${error.message}`);
                    } finally {
                        this.isLoading = false;
                    }
                },
                startNewTransaction() {
                    this.isSuccessModalOpen = false;
                    this.cart = [];
                    this.lastSaleId = null;
                    this.lastTotalAmount = 0;
                }
            }));
        });
    </script>
</body>
</html>