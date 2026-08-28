<template>
  <OnlineLayout>
    <Head :title="'Pembayaran QRIS Pesanan ' + order.order_number" />

    <div class="max-w-xl mx-auto px-4 py-8">
      <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-slate-100 space-y-6 text-center">
        
        <!-- HEADER -->
        <div class="space-y-2">
          <span class="px-3.5 py-1.5 bg-emerald-50 text-[#00661A] rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200 inline-block">
            ⚡ Pembayaran QRIS Resmi
          </span>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
            Pindai QRIS Untuk Membayar
          </h2>
          <p class="text-xs text-slate-500 font-medium">
            Buka aplikasi GoPay, OVO, Dana, BCA, ShopeePay, atau Mobile Banking Anda.
          </p>
        </div>

        <!-- TAGIHAN & ORDER NUMBER -->
        <div class="p-4 bg-emerald-50/60 rounded-3xl border border-emerald-200 space-y-1">
          <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Tagihan Pesanan:</span>
          <p class="text-3xl font-black text-[#00880F]">{{ formatRupiah(order.total_amount) }}</p>
          <p class="font-mono text-xs font-bold text-slate-500">No. Pesanan: {{ order.order_number }}</p>
        </div>

        <!-- DOKU CHECKOUT EMBED IFRAME OR QRIS CODE -->
        <div class="bg-slate-50 p-4 rounded-3xl border-2 border-slate-200/80 shadow-inner flex flex-col items-center justify-center min-h-[380px] relative overflow-hidden">
          <iframe 
            v-if="dokuUrl" 
            :src="dokuUrl" 
            class="w-full h-[450px] border-0 rounded-2xl bg-white shadow-sm"
          ></iframe>
          <div v-else class="space-y-3 py-12">
            <div class="w-12 h-12 border-4 border-[#00AA13] border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="text-xs font-bold text-slate-600">Menghubungkan ke DOKU Payment Gateway...</p>
          </div>
        </div>

        <!-- REALTIME STATUS INDICATOR & COUNTDOWN -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
          <div class="flex items-center space-x-2 text-[#00661A] font-bold">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>Menunggu Verifikasi Bank...</span>
          </div>
          <button 
            @click="checkStatusManual" 
            :disabled="isChecking"
            class="px-3.5 py-1.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition shadow-sm"
          >
            {{ isChecking ? 'Mengecek...' : 'Cek Status' }}
          </button>
        </div>

        <!-- RINCIAN PESANAN -->
        <div class="text-left pt-4 border-t border-slate-100 space-y-2 text-xs">
          <h4 class="font-black text-slate-800 uppercase text-[11px] tracking-wider">Rincian Penerima:</h4>
          <p class="text-slate-600 font-medium"><b>Nama:</b> {{ order.customer_name }} ({{ order.customer_phone }})</p>
          <p class="text-slate-600 font-medium"><b>Alamat:</b> {{ order.customer_address }}</p>
          <p class="text-slate-600 font-medium"><b>Ekspedisi:</b> {{ order.courier }}</p>
        </div>

      </div>
    </div>
  </OnlineLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import OnlineLayout from '@/Layouts/OnlineLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  order: Object,
  doku_payment_url: String,
});

const dokuUrl = ref(props.doku_payment_url || (props.order?.payment_payload?.url || null));
const isChecking = ref(false);
let pollTimer = null;

const formatRupiah = (val) => {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
};

const checkStatusAuto = async () => {
  try {
    const res = await fetch(`/order/check-status/${props.order.order_number}`);
    if (res.ok) {
      const data = await res.json();
      if (data.is_paid) {
        handlePaymentSuccess(data.redirect_url);
      }
    }
  } catch (e) {
    console.log('Error check status:', e);
  }
};

const checkStatusManual = async () => {
  isChecking.value = true;
  try {
    const res = await fetch(`/order/check-status/${props.order.order_number}`);
    if (res.ok) {
      const data = await res.json();
      if (data.is_paid) {
        handlePaymentSuccess(data.redirect_url);
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Menunggu Pembayaran',
          text: 'Pembayaran QRIS Anda belum terkonfirmasi oleh server bank. Silakan selesaikan pembayaran di aplikasi m-banking atau e-wallet Anda.',
          confirmButtonColor: '#00AA13'
        });
      }
    }
  } catch (e) {
    Swal.fire('Error', 'Gagal memeriksa status pembayaran.', 'error');
  } finally {
    isChecking.value = false;
  }
};

const handlePaymentSuccess = (redirectUrl) => {
  if (pollTimer) clearInterval(pollTimer);
  Swal.fire({
    icon: 'success',
    title: 'Pembayaran Berhasil!',
    text: 'Pembayaran QRIS telah diterima. Mengalihkan ke bukti pembayaran...',
    timer: 2000,
    showConfirmButton: false
  }).then(() => {
    window.location.href = redirectUrl || `/order/receipt/${props.order.order_number}`;
  });
};

onMounted(() => {
  // Polling active status check every 3 seconds
  pollTimer = setInterval(checkStatusAuto, 3000);
});

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer);
});
</script>
