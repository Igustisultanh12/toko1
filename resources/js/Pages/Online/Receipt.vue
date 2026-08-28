<template>
  <OnlineLayout>
    <Head :title="'Struk Pembayaran ' + order.order_number" />

    <div class="max-w-xl mx-auto px-4 py-8">
      <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-slate-100 space-y-6">
        
        <!-- STATUS LUNAS BADGE -->
        <div class="text-center space-y-2">
          <div class="w-16 h-16 bg-emerald-100 text-[#00AA13] rounded-full flex items-center justify-center text-3xl mx-auto shadow-inner">
            ✓
          </div>
          <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Pembayaran Berhasil</h2>
          <p class="text-xs text-slate-500 font-medium">Terima kasih, pesanan Anda telah berhasil dibayar lunas.</p>
        </div>

        <!-- INFO TRANSAKSI KOTAK STRUK -->
        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 space-y-4 font-mono text-xs">
          <div class="flex justify-between border-b border-dashed border-slate-300 pb-3">
            <span class="text-slate-500">NO. PESANAN</span>
            <span class="font-black text-slate-800">{{ order.order_number }}</span>
          </div>

          <div class="flex justify-between border-b border-dashed border-slate-300 pb-3">
            <span class="text-slate-500">WAKTU BAYAR</span>
            <span class="font-bold text-slate-800">{{ order.paid_at || '-' }}</span>
          </div>

          <div class="flex justify-between border-b border-dashed border-slate-300 pb-3">
            <span class="text-slate-500">METODE PEMBAYARAN</span>
            <span class="font-bold text-[#00880F] uppercase">QRIS DOKU (LUNAS)</span>
          </div>

          <div class="space-y-2 pt-1 border-b border-dashed border-slate-300 pb-3">
            <span class="text-slate-500 block font-bold">DAFTAR BARANG:</span>
            <div v-for="item in order.items" :key="item.id" class="flex justify-between text-slate-700">
              <span class="truncate pr-2">{{ item.quantity }}x {{ item.product_name }}</span>
              <span class="font-bold shrink-0">{{ formatRupiah(item.subtotal) }}</span>
            </div>
          </div>

          <div class="flex justify-between text-sm pt-1">
            <span class="font-black text-slate-800">TOTAL PEMBAYARAN</span>
            <span class="font-black text-base text-[#00880F]">{{ formatRupiah(order.total_amount) }}</span>
          </div>
        </div>

        <!-- INFORMASI PENERIMA -->
        <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 text-xs space-y-1 text-slate-700 font-sans">
          <p class="font-black text-emerald-950 uppercase text-[11px]">Tujuan Pengiriman:</p>
          <p><b>Penerima:</b> {{ order.customer_name }} ({{ order.customer_phone }})</p>
          <p><b>Alamat:</b> {{ order.customer_address }}</p>
          <p><b>Ekspedisi:</b> {{ order.courier }}</p>
          <p v-if="order.tracking_number" class="text-[#00880F] font-bold"><b>No. Resi:</b> {{ order.tracking_number }}</p>
        </div>

        <!-- TOMBOL AKSI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 font-sans">
          <Link 
            :href="'/order/track?order_number=' + order.order_number" 
            class="py-3.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider text-center shadow-lg shadow-emerald-500/25 transition"
          >
            📦 Lacak Status Pesanan
          </Link>

          <a 
            :href="'/order/receipt/' + order.order_number + '/pdf'" 
            target="_blank"
            class="py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider text-center transition"
          >
            📄 Cetak / Unduh PDF
          </a>
        </div>

      </div>
    </div>
  </OnlineLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OnlineLayout from '@/Layouts/OnlineLayout.vue';

const props = defineProps({
  order: Object,
});

const formatRupiah = (val) => {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
};
</script>
