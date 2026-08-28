<template>
  <OnlineLayout>
    <Head title="Lacak Pesanan & Pengiriman" />

    <div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
      
      <!-- CARD PENCARIAN RESI -->
      <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 shadow-sm border border-slate-100 space-y-4">
        <div>
          <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Lacak Status Pesanan</h2>
          <p class="text-xs text-slate-500 font-medium">Masukkan nomor pesanan (contoh: ORD-20260828-XXXXX) untuk melihat progres pengiriman.</p>
        </div>

        <form @submit.prevent="searchOrder" class="flex gap-2">
          <input 
            v-model="orderNumberInput" 
            type="text" 
            placeholder="Masukkan Nomor Pesanan..." 
            required
            class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 outline-none focus:border-[#00AA13] uppercase"
          >
          <button 
            type="submit" 
            class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
          >
            Lacak
          </button>
        </form>
      </div>

      <!-- HASIL PELACAKAN PESANAN -->
      <div v-if="order" class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-slate-100 space-y-8">
        
        <!-- HEADER STATUS -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
          <div>
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Nomor Pesanan:</span>
            <h3 class="font-mono text-lg font-black text-slate-900">{{ order.order_number }}</h3>
          </div>
          <div>
            <span 
              :class="getStatusBadgeClass(order.status)"
              class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-sm inline-block"
            >
              {{ order.status_label || order.status }}
            </span>
          </div>
        </div>

        <!-- PROGRESS STEPPER -->
        <div class="space-y-4">
          <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahapan Pemrosesan:</h4>
          
          <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-black uppercase tracking-tight">
            <!-- STEP 1: MENUNGGU / DIBAYAR -->
            <div :class="getStepLevel(order.status) >= 2 ? 'text-[#00880F]' : 'text-slate-400'" class="space-y-1.5">
              <div :class="getStepLevel(order.status) >= 2 ? 'bg-[#00AA13] text-white' : 'bg-slate-100 text-slate-400'" class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs font-black shadow-sm">
                1
              </div>
              <p>Dibayar</p>
            </div>

            <!-- STEP 2: DISIAPKAN -->
            <div :class="getStepLevel(order.status) >= 3 ? 'text-blue-600' : 'text-slate-400'" class="space-y-1.5">
              <div :class="getStepLevel(order.status) >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400'" class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs font-black shadow-sm">
                2
              </div>
              <p>Disiapkan</p>
            </div>

            <!-- STEP 3: DIKIRIM -->
            <div :class="getStepLevel(order.status) >= 4 ? 'text-purple-600' : 'text-slate-400'" class="space-y-1.5">
              <div :class="getStepLevel(order.status) >= 4 ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-400'" class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs font-black shadow-sm">
                3
              </div>
              <p>Dikirim</p>
            </div>

            <!-- STEP 4: SELESAI -->
            <div :class="getStepLevel(order.status) >= 5 ? 'text-emerald-700' : 'text-slate-400'" class="space-y-1.5">
              <div :class="getStepLevel(order.status) >= 5 ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-400'" class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs font-black shadow-sm">
                4
              </div>
              <p>Selesai</p>
            </div>
          </div>
        </div>

        <!-- INFO EKSPEDISI & RESI -->
        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Ekspedisi Kurir:</span>
            <p class="font-black text-slate-800 text-sm mt-0.5">{{ order.courier }}</p>
          </div>
          <div>
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Nomor Resi:</span>
            <p class="font-mono font-bold text-[#00880F] text-sm mt-0.5">
              {{ order.tracking_number || 'Sedang diproses oleh bagian gudang...' }}
            </p>
          </div>
        </div>

        <!-- TOMBOL KONFIRMASI PESANAN DITERIMA & KOMPLAIN -->
        <div class="space-y-3 pt-2">
          <button 
            v-if="order.status === 'shipped'"
            @click="confirmReceived" 
            class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
          >
            ✅ Konfirmasi Pesanan Sudah Diterima
          </button>

          <div class="flex justify-between items-center text-xs">
            <Link :href="'/order/receipt/' + order.order_number" class="text-slate-500 hover:text-slate-800 font-bold">
              &larr; Lihat Struk Pembayaran
            </Link>

            <Link 
              v-if="order.payment_status === 'paid'"
              :href="'/order/complaint?order_number=' + order.order_number" 
              class="text-rose-600 hover:text-rose-700 font-bold"
            >
              ⚠️ Ajukan Komplain / Pengaduan
            </Link>
          </div>
        </div>

      </div>

    </div>
  </OnlineLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import OnlineLayout from '@/Layouts/OnlineLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  order: Object,
  order_number: String,
});

const orderNumberInput = ref(props.order_number || (props.order?.order_number || ''));

const searchOrder = () => {
  if (!orderNumberInput.value) return;
  router.get('/order/track', { order_number: orderNumberInput.value.trim() }, { preserveState: false });
};

const getStepLevel = (status) => {
  switch (status) {
    case 'paid': return 2;
    case 'processing': return 3;
    case 'shipped': return 4;
    case 'completed': return 5;
    default: return 1;
  }
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'paid': return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
    case 'processing': return 'bg-blue-100 text-blue-800 border border-blue-300';
    case 'shipped': return 'bg-purple-100 text-purple-800 border border-purple-300';
    case 'completed': return 'bg-slate-900 text-white';
    case 'cancelled': return 'bg-rose-100 text-rose-800 border border-rose-300';
    default: return 'bg-amber-100 text-amber-800 border border-amber-300';
  }
};

const confirmReceived = () => {
  Swal.fire({
    title: 'Konfirmasi Penerimaan',
    text: 'Apakah pesanan Anda sudah diterima dengan baik dan lengkap?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#00AA13',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Sudah Diterima',
    cancelButtonText: 'Batal'
  }).then((res) => {
    if (res.isConfirmed) {
      router.post(`/order/received/${props.order.order_number}`, {}, {
        onSuccess: () => {
          Swal.fire('Terima Kasih!', 'Status pesanan telah diperbarui menjadi Selesai.', 'success');
        }
      });
    }
  });
};
</script>
