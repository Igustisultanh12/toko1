<template>
  <AdminLayout>
    <template #header>Laporan Penjualan</template>
    <Head title="Laporan Penjualan" />

    <div class="space-y-6">
      
      <!-- TOOLBAR FILTER TANGGAL -->
      <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
          <h3 class="text-base font-black text-slate-900 uppercase">Rekapitulasi Transaksi</h3>
          <p class="text-xs text-slate-400 font-medium">Filter laporan berdasarkan rentang tanggal penjualan.</p>
        </div>

        <form @submit.prevent="filterDate" class="flex flex-wrap items-center gap-2">
          <input v-model="startDate" type="date" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
          <span class="text-xs text-slate-400 font-bold">s/d</span>
          <input v-model="endDate" type="date" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
          <button type="submit" class="px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl text-xs font-black uppercase">
            Filter
          </button>
        </form>
      </div>

      <!-- RINGKASAN TOTAL -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400">Total Transaksi</span>
          <p class="text-2xl font-black text-slate-800">{{ total_sales_count || 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400">Total Omset Pendapatan</span>
          <p class="text-2xl font-black text-[#00880F]">{{ formatRupiah(total_revenue || 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400">Total Item Terjual</span>
          <p class="text-2xl font-black text-blue-600">{{ total_items_sold || 0 }} pcs</p>
        </div>
      </div>

      <!-- TABEL REKAP TRANSAKSI -->
      <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
            <tr>
              <th class="p-4 pl-6">No. Transaksi</th>
              <th class="p-4">Tanggal</th>
              <th class="p-4">Tipe & Metode</th>
              <th class="p-4">Kasir / Pelanggan</th>
              <th class="p-4 pr-6 text-right">Total Transaksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            <tr v-for="sale in sales.data" :key="sale.id" class="hover:bg-slate-50/80 transition">
              <td class="p-4 pl-6 font-mono font-bold text-slate-900">
                {{ sale.transaction_number || sale.order_number }}
              </td>
              <td class="p-4 text-slate-500">
                {{ sale.created_at }}
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-slate-100 text-slate-700">
                  {{ sale.payment_method || 'QRIS DOKU' }}
                </span>
              </td>
              <td class="p-4 font-bold text-slate-800">
                {{ sale.user?.name || sale.customer_name || 'Umum' }}
              </td>
              <td class="p-4 pr-6 text-right font-black text-[#00880F]">
                {{ formatRupiah(sale.total_amount || sale.total_price) }}
              </td>
            </tr>

            <tr v-if="!sales.data || sales.data.length === 0">
              <td colspan="5" class="py-12 text-center text-slate-400 font-bold italic">
                Tidak ada data penjualan pada rentang tanggal ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  sales: Object,
  start_date: String,
  end_date: String,
  total_revenue: Number,
  total_sales_count: Number,
  total_items_sold: Number,
});

const startDate = ref(props.start_date || '');
const endDate = ref(props.end_date || '');

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);

const filterDate = () => {
  router.get('/admin/reports', { start_date: startDate.value, end_date: endDate.value }, { preserveState: true });
};
</script>
