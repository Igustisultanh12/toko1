<template>
  <div class="h-screen flex flex-col bg-slate-100 text-slate-800 font-sans overflow-hidden">
    <Head title="Kasir POS Modern" />

    <!-- TOPBAR KASIR POS -->
    <header class="bg-[#00360D] text-white px-6 py-3.5 flex items-center justify-between shadow-md shrink-0">
      <div class="flex items-center space-x-3">
        <Link href="/admin/dashboard" class="w-9 h-9 rounded-2xl bg-[#00AA13] flex items-center justify-center text-white text-lg font-black shadow-md hover:scale-105 transition">
          ⚡
        </Link>
        <div>
          <h1 class="font-black text-sm uppercase tracking-tight leading-none">{{ $page.props.shop?.shop_name || 'SIBALOG POS' }}</h1>
          <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mt-0.5">Kasir POS Modern &bull; Vue 3</p>
        </div>
      </div>

      <div class="flex items-center space-x-3">
        <div class="hidden sm:block text-right">
          <p class="text-xs font-black text-white leading-none">{{ $page.props.auth?.user?.name || 'Kasir' }}</p>
          <p class="text-[9px] text-emerald-400 font-bold uppercase mt-0.5">{{ currentTime }}</p>
        </div>
        <Link href="/admin/dashboard" class="px-3.5 py-2 bg-emerald-900/80 hover:bg-[#00AA13] text-white rounded-xl text-xs font-black uppercase tracking-wider transition border border-emerald-700">
          Dashboard
        </Link>
      </div>
    </header>

    <!-- POS MAIN WORKSPACE (2 PANELS: KATALOG KIRI, KERANJANG KANAN) -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden p-3 sm:p-4 gap-3 sm:gap-4">
      
      <!-- PANEL KIRI: PENCARIAN & GRID BARANG -->
      <div class="flex-1 flex flex-col bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- SEARCH & SCANNER INPUT -->
        <div class="p-4 border-b border-slate-100 bg-white flex gap-2 shrink-0">
          <div class="relative flex-1">
            <input 
              ref="barcodeInputRef"
              v-model="barcodeSearch" 
              @keydown.enter="handleBarcodeSubmit"
              type="text" 
              placeholder="Scan Barcode atau ketik nama produk (Enter)..."
              class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition"
              autofocus
            >
            <div class="absolute left-3.5 top-3.5 text-slate-400 text-sm">
              🔍
            </div>
          </div>

          <button 
            @click="clearSearch" 
            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-black transition"
          >
            Reset
          </button>
        </div>

        <!-- GRID BARANG -->
        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 content-start">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            @click="addToCart(product)"
            class="bg-slate-50 hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-300 rounded-2xl p-3 cursor-pointer transition-all flex flex-col justify-between group shadow-sm"
          >
            <div class="space-y-2">
              <div class="aspect-square w-full bg-white rounded-xl overflow-hidden border border-slate-100 flex items-center justify-center relative">
                <img v-if="product.image_url" :src="product.image_url" class="w-full h-full object-cover group-hover:scale-105 transition">
                <span v-else class="text-3xl text-slate-300">📦</span>
                <span class="absolute bottom-1 left-1 px-1.5 py-0.5 bg-black/60 text-white rounded text-[8px] font-bold">
                  Stok: {{ product.stock }}
                </span>
              </div>
              <div>
                <h4 class="font-black text-slate-900 text-xs line-clamp-2 uppercase leading-tight group-hover:text-[#00AA13]">{{ product.name }}</h4>
                <p v-if="product.barcode" class="font-mono text-[9px] text-slate-400 font-bold mt-0.5">{{ product.barcode }}</p>
              </div>
            </div>

            <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
              <span class="text-xs font-black text-[#00880F]">{{ formatRupiah(calculatePrice(product)) }}</span>
              <span class="w-6 h-6 rounded-lg bg-emerald-100 text-[#00880F] font-black text-xs flex items-center justify-center group-hover:bg-[#00AA13] group-hover:text-white transition">+</span>
            </div>
          </div>
        </div>
      </div>

      <!-- PANEL KANAN: KERANJANG TRANSAKSI & PEMBAYARAN -->
      <div class="w-full lg:w-[420px] bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col overflow-hidden shrink-0">
        
        <!-- CART HEADER -->
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <div class="flex items-center space-x-2">
            <span class="text-lg">🛒</span>
            <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">Keranjang Transaksi</h3>
          </div>
          <button 
            v-if="cart.length > 0" 
            @click="cart = []" 
            class="text-[10px] text-rose-600 hover:text-rose-800 font-black uppercase"
          >
            Kosongkan
          </button>
        </div>

        <!-- CART LIST ITEMS -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2.5">
          <div 
            v-for="(item, idx) in cart" 
            :key="item.id"
            class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center text-xs"
          >
            <div class="flex-1 pr-2 truncate">
              <h4 class="font-black text-slate-800 truncate">{{ item.name }}</h4>
              <p class="text-[10px] text-[#00880F] font-bold">{{ formatRupiah(item.price) }} &times; {{ item.quantity }} = {{ formatRupiah(item.price * item.quantity) }}</p>
            </div>

            <div class="flex items-center space-x-1.5 shrink-0">
              <button @click="decreaseQty(idx)" class="w-6 h-6 bg-white border border-slate-200 rounded-lg font-bold hover:bg-slate-100">-</button>
              <span class="w-5 text-center font-black">{{ item.quantity }}</span>
              <button @click="increaseQty(idx)" class="w-6 h-6 bg-white border border-slate-200 rounded-lg font-bold hover:bg-slate-100">+</button>
              <button @click="removeItem(idx)" class="text-rose-500 hover:text-rose-700 p-1 font-bold">🗑️</button>
            </div>
          </div>

          <div v-if="cart.length === 0" class="py-16 text-center text-slate-400 font-bold text-xs italic">
            Keranjang kasir masih kosong.<br>Scan barcode atau pilih barang di katalog.
          </div>
        </div>

        <!-- TOTAL & CHECKOUT ACTION BUTTONS -->
        <div class="p-5 border-t border-slate-100 bg-slate-50 space-y-4">
          <div class="space-y-1">
            <div class="flex justify-between text-xs font-bold text-slate-500">
              <span>Total Item:</span>
              <span>{{ totalItems }} Pcs</span>
            </div>
            <div class="flex justify-between items-center pt-1 border-t border-slate-200">
              <span class="text-xs font-black uppercase text-slate-800">Total Tagihan:</span>
              <span class="text-xl font-black text-[#00880F]">{{ formatRupiah(totalAmount) }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2.5">
            <button 
              @click="openCashModal" 
              :disabled="cart.length === 0"
              class="py-3.5 bg-slate-800 hover:bg-slate-900 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center space-x-1"
            >
              <span>💵 Tunai</span>
            </button>

            <button 
              @click="openQrisModal" 
              :disabled="cart.length === 0"
              class="py-3.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center justify-center space-x-1"
            >
              <span>⚡ QRIS DOKU</span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MODAL PEMBAYARAN TUNAI -->
    <div v-if="isCashModalOpen" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-md rounded-3xl p-6 space-y-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
          <h3 class="font-black text-base uppercase text-slate-900">💵 Pembayaran Tunai (Cash)</h3>
          <button @click="isCashModalOpen = false" class="text-slate-400 hover:text-slate-700 font-black">✕</button>
        </div>

        <div class="p-4 bg-emerald-50 rounded-2xl text-center space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400">Total Tagihan:</span>
          <p class="text-2xl font-black text-[#00880F]">{{ formatRupiah(totalAmount) }}</p>
        </div>

        <div class="space-y-2">
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Uang Diterima (Rp):</label>
          <input 
            v-model="cashGiven" 
            type="number" 
            class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl text-xl font-black text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white"
            placeholder="0"
          >
          <div class="grid grid-cols-3 gap-2 pt-2">
            <button @click="cashGiven = totalAmount" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-black">Uang Pas</button>
            <button @click="cashGiven = 50000" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-black">50.000</button>
            <button @click="cashGiven = 100000" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-black">100.000</button>
          </div>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-xs">
          <span class="font-bold text-slate-500">Kembalian:</span>
          <span class="text-base font-black text-slate-900">{{ formatRupiah(changeAmount) }}</span>
        </div>

        <button 
          @click="submitCashSale" 
          :disabled="cashGiven < totalAmount || isSubmitting"
          class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
        >
          {{ isSubmitting ? 'Memproses...' : 'Selesaikan Transaksi (Cetak Struk)' }}
        </button>
      </div>
    </div>

    <!-- MODAL PEMBAYARAN QRIS DOKU -->
    <div v-if="isQrisModalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-lg rounded-3xl p-6 space-y-6 shadow-2xl border border-slate-100 text-center">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
          <h3 class="font-black text-base uppercase text-slate-900">⚡ QRIS Payment Gateway</h3>
          <button @click="closeQrisModal" class="text-slate-400 hover:text-slate-700 font-black">✕</button>
        </div>

        <div class="p-4 bg-emerald-50 rounded-2xl space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400">Total Tagihan:</span>
          <p class="text-2xl font-black text-[#00880F]">{{ formatRupiah(totalAmount) }}</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl min-h-[350px] flex items-center justify-center">
          <iframe v-if="qrisUrl" :src="qrisUrl" class="w-full h-[400px] border-0 rounded-xl bg-white"></iframe>
          <div v-else class="space-y-2">
            <div class="w-10 h-10 border-4 border-[#00AA13] border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="text-xs font-bold text-slate-500">Membuat QRIS DOKU...</p>
          </div>
        </div>

        <div class="flex justify-between items-center text-xs">
          <div class="flex items-center space-x-2 text-[#00661A] font-bold">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>Menunggu Pembayaran Kasir...</span>
          </div>
          <button @click="forceConfirmQris" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-black text-[10px] uppercase">
            Konfirmasi Manual
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  products: Array,
});

const barcodeSearch = ref('');
const barcodeInputRef = ref(null);
const cart = ref([]);
const isCashModalOpen = ref(false);
const isQrisModalOpen = ref(false);
const cashGiven = ref(0);
const qrisUrl = ref(null);
const currentSaleId = ref(null);
const isSubmitting = ref(false);
const currentTime = ref('');

let clockTimer = null;
let qrisPollTimer = null;

const filteredProducts = computed(() => {
  if (!props.products) return [];
  if (!barcodeSearch.value) return props.products;
  const q = barcodeSearch.value.toLowerCase();
  return props.products.filter(p => 
    p.name.toLowerCase().includes(q) || (p.barcode && p.barcode.toLowerCase().includes(q))
  );
});

const totalItems = computed(() => cart.value.reduce((sum, i) => sum + i.quantity, 0));
const totalAmount = computed(() => cart.value.reduce((sum, i) => sum + (i.price * i.quantity), 0));
const changeAmount = computed(() => Math.max(0, cashGiven.value - totalAmount.value));

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);

const calculatePrice = (product) => {
  if (product.discount_percent > 0) {
    return product.price - (product.price * (product.discount_percent / 100));
  }
  return product.price;
};

const addToCart = (product) => {
  const finalPrice = calculatePrice(product);
  const existing = cart.value.find(item => item.id === product.id);
  if (existing) {
    if (existing.quantity >= product.stock) {
      Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Maksimal kuantitas adalah ${product.stock} pcs.`, confirmButtonColor: '#00AA13' });
      return;
    }
    existing.quantity++;
  } else {
    cart.value.push({
      id: product.id,
      name: product.name,
      price: finalPrice,
      max_stock: product.stock,
      quantity: 1,
      image_url: product.image_url
    });
  }
};

const handleBarcodeSubmit = () => {
  if (!barcodeSearch.value) return;
  const match = props.products?.find(p => p.barcode === barcodeSearch.value.trim());
  if (match) {
    addToCart(match);
    barcodeSearch.value = '';
  }
};

const clearSearch = () => {
  barcodeSearch.value = '';
};

const increaseQty = (idx) => {
  if (cart.value[idx].quantity < cart.value[idx].max_stock) {
    cart.value[idx].quantity++;
  }
};

const decreaseQty = (idx) => {
  if (cart.value[idx].quantity > 1) {
    cart.value[idx].quantity--;
  } else {
    removeItem(idx);
  }
};

const removeItem = (idx) => cart.value.splice(idx, 1);

const openCashModal = () => {
  cashGiven.value = totalAmount.value;
  isCashModalOpen.value = true;
};

const submitCashSale = async () => {
  isSubmitting.value = true;
  try {
    const res = await fetch('/cashier/pos/store-sale', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: JSON.stringify({
        payment_method: 'cash',
        total_amount: totalAmount.value,
        cash_given: cashGiven.value,
        items: cart.value.map(i => ({ id: i.id, quantity: i.quantity, price: i.price }))
      })
    });

    const data = await res.json();
    if (res.ok && data.success) {
      isCashModalOpen.value = false;
      cart.value = [];
      Swal.fire({
        icon: 'success',
        title: 'Transaksi Sukses',
        text: `Kembalian: ${formatRupiah(changeAmount.value)}`,
        confirmButtonColor: '#00AA13'
      });
    } else {
      Swal.fire('Error', data.message || 'Gagal menyimpan transaksi.', 'error');
    }
  } catch (e) {
    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const openQrisModal = async () => {
  isQrisModalOpen.value = true;
  qrisUrl.value = null;
  try {
    const res = await fetch('/cashier/pos/store-sale', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: JSON.stringify({
        payment_method: 'qris',
        total_amount: totalAmount.value,
        items: cart.value.map(i => ({ id: i.id, quantity: i.quantity, price: i.price }))
      })
    });

    const data = await res.json();
    if (res.ok && data.success) {
      qrisUrl.value = data.qris_url;
      currentSaleId.value = data.sale_id;
      startQrisPolling(data.sale_id);
    } else {
      Swal.fire('Error', data.message || 'Gagal membuat QRIS DOKU.', 'error');
      isQrisModalOpen.value = false;
    }
  } catch (e) {
    Swal.fire('Error', 'Gagal menghubungi server DOKU.', 'error');
    isQrisModalOpen.value = false;
  }
};

const startQrisPolling = (saleId) => {
  if (qrisPollTimer) clearInterval(qrisPollTimer);
  qrisPollTimer = setInterval(async () => {
    try {
      const res = await fetch(`/cashier/sales/${saleId}/check-status`);
      if (res.ok) {
        const data = await res.json();
        if (data.status === 'success') {
          clearInterval(qrisPollTimer);
          isQrisModalOpen.value = false;
          cart.value = [];
          Swal.fire({
            icon: 'success',
            title: 'Pembayaran QRIS Lunas!',
            text: 'Transaksi telah berhasil diverifikasi oleh server DOKU.',
            confirmButtonColor: '#00AA13'
          });
        }
      }
    } catch (e) {}
  }, 2500);
};

const forceConfirmQris = async () => {
  if (!currentSaleId.value) return;
  const res = await fetch(`/cashier/sales/${currentSaleId.value}/force-confirm`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') }
  });
  if (res.ok) {
    if (qrisPollTimer) clearInterval(qrisPollTimer);
    isQrisModalOpen.value = false;
    cart.value = [];
    Swal.fire('Sukses', 'Transaksi dikonfirmasi lunas secara manual.', 'success');
  }
};

const closeQrisModal = () => {
  if (qrisPollTimer) clearInterval(qrisPollTimer);
  isQrisModalOpen.value = false;
};

onMounted(() => {
  clockTimer = setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString('id-ID');
  }, 1000);
});

onUnmounted(() => {
  if (clockTimer) clearInterval(clockTimer);
  if (qrisPollTimer) clearInterval(qrisPollTimer);
});
</script>
