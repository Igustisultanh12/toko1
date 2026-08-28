<template>
  <AdminLayout>
    <template #header>Detail Pesanan: {{ order.order_number }}</template>
    <Head :title="'Kelola ' + order.order_number" />

    <div class="max-w-5xl mx-auto space-y-6">
      
      <!-- TOP ACTION BAR -->
      <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div>
          <div class="flex items-center space-x-3">
            <h3 class="font-mono text-lg font-black text-slate-900">{{ order.order_number }}</h3>
            <span 
              :class="getStatusBadgeClass(order.status)"
              class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm"
            >
              {{ order.status_label || order.status }}
            </span>
          </div>
          <p class="text-xs text-slate-400 font-medium mt-1">Dibuat pada: {{ order.created_at }}</p>
        </div>

        <div class="flex items-center space-x-2">
          <a 
            :href="'/order/receipt/' + order.order_number + '/pdf'" 
            target="_blank"
            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider transition"
          >
            📄 Struk PDF
          </a>
          <Link href="/admin/orders" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
            &larr; Kembali
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI: FORM PENGATURAN STATUS & EKSPEDISI (7 SPAN) -->
        <div class="lg:col-span-7 space-y-6">
          
          <!-- FORM UBAH STATUS (VISUAL RADIO CARDS) -->
          <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-5">
            <div>
              <h4 class="font-black text-xs uppercase tracking-wider text-slate-800">Status Pesanan & Pengiriman</h4>
              <p class="text-[10px] text-slate-400 font-medium">Ubah status pesanan untuk memperbarui progres di layar pembeli secara instan.</p>
            </div>

            <form @submit.prevent="updateOrderStatus" class="space-y-5">
              <!-- RADIO CARDS PILIHAN STATUS -->
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                <label 
                  v-for="st in statusOptions" 
                  :key="st.value"
                  :class="form.status === st.value ? st.activeClass + ' ring-2' : 'border-slate-200 hover:border-slate-300 bg-slate-50 text-slate-700'"
                  class="p-3.5 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between space-y-2 text-left"
                >
                  <input type="radio" v-model="form.status" :value="st.value" class="sr-only">
                  <div class="flex justify-between items-center">
                    <span class="text-base">{{ st.icon }}</span>
                    <span v-if="form.status === st.value" class="w-2 h-2 rounded-full bg-current"></span>
                  </div>
                  <div>
                    <h5 class="font-black text-xs leading-none uppercase">{{ st.label }}</h5>
                    <p class="text-[9px] opacity-70 mt-0.5 leading-tight">{{ st.desc }}</p>
                  </div>
                </label>
              </div>

              <!-- PILIHAN EKSPEDISI KURIR (CHIPS) -->
              <div class="space-y-2 pt-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilihan Ekspedisi Kurir:</label>
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-for="cur in courierOptions" 
                    :key="cur"
                    type="button"
                    @click="form.courier = cur"
                    :class="form.courier === cur ? 'bg-[#00AA13] text-white font-black shadow-md shadow-emerald-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold'"
                    class="px-3.5 py-2 rounded-xl text-xs uppercase tracking-wider transition"
                  >
                    {{ cur }}
                  </button>
                </div>
              </div>

              <!-- NOMOR RESI PENGIRIMAN -->
              <div class="space-y-1.5 pt-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Resi / AWB Ekspedisi:</label>
                <input 
                  v-model="form.tracking_number" 
                  type="text" 
                  placeholder="Contoh: JP8991283749"
                  class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-mono font-bold text-slate-800 text-xs transition uppercase"
                >
              </div>

              <button 
                type="submit" 
                :disabled="isSubmitting"
                class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
              >
                {{ isSubmitting ? 'Memperbarui...' : 'Simpan Perubahan Status' }}
              </button>
            </form>
          </div>

          <!-- KOMPLAIN PELANGGAN JIKA ADA -->
          <div v-if="order.complaint" class="bg-rose-50 rounded-3xl p-6 sm:p-8 border-2 border-rose-200 space-y-4">
            <div class="flex items-center space-x-2 text-rose-800">
              <span class="text-xl">⚠️</span>
              <h4 class="font-black text-xs uppercase tracking-wider">Pengaduan / Komplain Pelanggan</h4>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-rose-100 text-xs space-y-2 text-slate-700">
              <p class="font-medium whitespace-pre-line">{{ order.complaint.complaint_text }}</p>
              
              <div v-if="order.complaint.photos && order.complaint.photos.length > 0" class="pt-2">
                <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5">Foto Bukti Barang:</span>
                <div class="flex flex-wrap gap-2">
                  <a 
                    v-for="(p, idx) in order.complaint.photos" 
                    :key="idx" 
                    :href="'/media/' + p" 
                    target="_blank"
                    class="w-16 h-16 rounded-xl overflow-hidden border border-slate-200 block"
                  >
                    <img :src="'/media/' + p" class="w-full h-full object-cover">
                  </a>
                </div>
              </div>

              <div v-if="order.complaint.video_path" class="pt-2">
                <a :href="'/media/' + order.complaint.video_path" target="_blank" class="text-xs font-black text-rose-600 hover:underline">
                  🎥 Tonton Video Unboxing &rarr;
                </a>
              </div>
            </div>
          </div>

        </div>

        <!-- KOLOM KANAN: RINCIAN BARANG & PENERIMA (5 SPAN) -->
        <div class="lg:col-span-5 space-y-6">
          
          <!-- INFO PENERIMA -->
          <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-3 text-xs">
            <h4 class="font-black text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-100 pb-2">Data Penerima & Alamat</h4>
            <div class="space-y-1.5 text-slate-600 font-medium">
              <p><b>Nama:</b> {{ order.customer_name }}</p>
              <p><b>Telepon / WA:</b> {{ order.customer_phone }}</p>
              <p><b>Alamat Pengiriman:</b></p>
              <p class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-800 font-medium leading-relaxed">{{ order.customer_address }}</p>
              <p v-if="order.customer_notes" class="text-amber-700 italic"><b>Catatan:</b> {{ order.customer_notes }}</p>
            </div>
          </div>

          <!-- RINCIAN BARANG PESANAN -->
          <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 text-xs">
            <h4 class="font-black text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-100 pb-2">Rincian Barang Dipesan</h4>
            
            <div class="space-y-2.5">
              <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="truncate pr-2">
                  <h5 class="font-black text-slate-900 text-xs truncate uppercase">{{ item.product_name }}</h5>
                  <p class="text-[10px] text-slate-400">{{ formatRupiah(item.price) }} &times; {{ item.quantity }} pcs</p>
                </div>
                <span class="font-black text-slate-800 shrink-0">{{ formatRupiah(item.subtotal) }}</span>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
              <span class="font-black uppercase text-slate-800">Total Pembayaran:</span>
              <span class="text-base font-black text-[#00880F]">{{ formatRupiah(order.total_amount) }}</span>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  order: Object,
});

const form = ref({
  status: props.order.status,
  courier: props.order.courier || 'J&T Express',
  tracking_number: props.order.tracking_number || '',
});

const isSubmitting = ref(false);

const courierOptions = ['J&T Express', 'JNE Reguler', 'SiCepat Express', 'Kurir Toko / Ambil Sendiri'];

const statusOptions = [
  { value: 'pending_payment', label: 'Belum Bayar', icon: '⏳', desc: 'Menunggu transfer', activeClass: 'border-amber-500 bg-amber-50 text-amber-900 ring-amber-300' },
  { value: 'paid', label: 'Dibayar', icon: '✅', desc: 'Lunas QRIS', activeClass: 'border-emerald-500 bg-emerald-50 text-[#00661A] ring-emerald-300' },
  { value: 'processing', label: 'Disiapkan', icon: '📦', desc: 'Kemas barang gudang', activeClass: 'border-blue-500 bg-blue-50 text-blue-900 ring-blue-300' },
  { value: 'shipped', label: 'Dikirim', icon: '🚚', desc: 'Dalam perjalanan ekspedisi', activeClass: 'border-purple-500 bg-purple-50 text-purple-900 ring-purple-300' },
  { value: 'completed', label: 'Selesai', icon: '🏁', desc: 'Diterima pembeli', activeClass: 'border-slate-900 bg-slate-900 text-white ring-slate-400' },
  { value: 'cancelled', label: 'Batal', icon: '❌', desc: 'Pesanan dibatalkan', activeClass: 'border-rose-500 bg-rose-50 text-rose-900 ring-rose-300' },
];

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);

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

const updateOrderStatus = () => {
  isSubmitting.value = true;
  router.put(`/admin/orders/${props.order.id}`, form.value, {
    onSuccess: () => {
      isSubmitting.value = false;
      Swal.fire('Sukses', 'Status pesanan berhasil diperbarui.', 'success');
    },
    onError: (errs) => {
      isSubmitting.value = false;
      const first = Object.values(errs)[0] || 'Gagal memperbarui status.';
      Swal.fire('Error', first, 'error');
    }
  });
};
</script>
