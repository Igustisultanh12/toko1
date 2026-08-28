<template>
  <AdminLayout>
    <template #header>Laporan Keuangan</template>
    <Head title="Laporan Keuangan" />

    <div class="space-y-6 pb-12">
      
      <!-- HEADER ACTION BAR -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div>
          <div class="inline-flex items-center space-x-2 px-3 py-1 bg-emerald-50 rounded-full text-[10px] font-black uppercase tracking-wider text-[#00880F] border border-emerald-200 mb-2">
            <span>💰</span>
            <span>Arus Kas & Penerimaan Bersih</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">Laporan Keuangan & Kas Toko</h2>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Monitoring penerimaan kas tunai, omset QRIS, potongan MDR gateway (0.7%), dan total laba kas bersih.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
          <a 
            :href="`/admin/reports/finance/pdf?${buildQueryString()}`" 
            target="_blank"
            class="flex-1 sm:flex-none px-5 py-3 bg-[#EE2737] hover:bg-rose-700 active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 transition flex items-center justify-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Cetak PDF Keuangan</span>
          </a>

          <a 
            :href="`/admin/reports/finance/excel?${buildQueryString()}`" 
            class="flex-1 sm:flex-none px-5 py-3 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition flex items-center justify-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 2v-6m-8 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Export Excel</span>
          </a>
        </div>
      </div>

      <!-- FILTER CARD -->
      <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-4">
        <form @submit.prevent="applyFilter" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. PERIODE -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pilihan Periode</label>
              <select v-model="filterForm.period" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
                <option value="all">🌐 Semua Periode</option>
                <option value="daily">📅 Harian</option>
                <option value="monthly">🗓️ Bulanan</option>
                <option value="3_months">📊 Periode 3 Bulan (Kuartal)</option>
                <option value="yearly">📈 Tahunan</option>
              </select>
            </div>

            <!-- DYNAMIC PERIODE INPUTS -->
            <div v-if="filterForm.period === 'daily'">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Tanggal</label>
              <input v-model="filterForm.date" type="date" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
            </div>

            <div v-if="filterForm.period === 'monthly'">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Bulan & Tahun</label>
              <input v-model="filterForm.month" type="month" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
            </div>

            <div v-if="filterForm.period === '3_months'" class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kuartal</label>
                <select v-model="filterForm.quarter" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] transition">
                  <option value="1">Q1 (Jan-Mar)</option>
                  <option value="2">Q2 (Apr-Jun)</option>
                  <option value="3">Q3 (Jul-Sep)</option>
                  <option value="4">Q4 (Okt-Des)</option>
                </select>
              </div>
              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
                <input v-model="filterForm.year" type="number" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] transition">
              </div>
            </div>

            <div v-if="filterForm.period === 'yearly'">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
              <input v-model="filterForm.year" type="number" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] transition">
            </div>

            <!-- 2. FILTER KANAL KAS -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Filter Kanal Kas</label>
              <select v-model="filterForm.payment_method" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
                <option value="all">Semua Kanal (Tunai & QRIS)</option>
                <option value="cash">💵 Khusus Kas Tunai</option>
                <option value="qris">📱 Khusus Kas QRIS DOKU</option>
              </select>
            </div>

            <!-- 3. PENCARIAN -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Cari No. Faktur / Nama</label>
              <input v-model="filterForm.search" type="text" placeholder="Ketik invoice atau nama..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
            </div>
          </div>

          <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
            <div class="flex items-center gap-2">
              <button type="submit" class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                🔍 Tampilkan Data
              </button>
              <button type="button" @click="resetFilter" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-xs uppercase transition">
                Reset
              </button>
            </div>
            <p class="text-xs text-slate-400 font-bold hidden sm:block">
              Periode Aktif: <span class="text-[#00880F]">{{ periodLabel }}</span>
            </p>
          </div>
        </form>
      </div>

      <!-- STATISTIK RINGKASAN KEUANGAN -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-[#00360D] via-[#004D13] to-[#005B16] p-6 rounded-3xl shadow-xl text-white space-y-1">
          <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest">Total Pemasukan Kas Bersih</p>
          <h3 class="text-xl sm:text-2xl font-black tracking-tight break-words text-white mt-1">{{ formatRupiah(stats.total_income) }}</h3>
          <p class="text-[10px] text-emerald-200/80 font-bold mt-1">● Setelah Potongan MDR (0.7%)</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kas Masuk Tunai</p>
          <h3 class="text-xl sm:text-2xl font-black text-[#00880F] tracking-tight break-words">{{ formatRupiah(stats.cash_income) }}</h3>
          <p class="text-[10px] text-emerald-600 font-bold mt-1">{{ stats.cash_count }} transaksi tunai</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">QRIS Bersih Diterima</p>
          <h3 class="text-xl sm:text-2xl font-black text-purple-700 tracking-tight break-words">{{ formatRupiah(stats.qris_income) }}</h3>
          <p class="text-[10px] text-slate-400 font-bold mt-1">MDR 0.7%: <span class="text-rose-500 font-black">-{{ formatRupiah(stats.qris_fee) }}</span></p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Belum Lunas (Pending)</p>
          <h3 class="text-xl sm:text-2xl font-black text-amber-600 tracking-tight break-words">{{ formatRupiah(stats.pending_income) }}</h3>
          <p class="text-[10px] text-amber-600 font-bold mt-1">{{ stats.pending_count }} invoice menunggu</p>
        </div>
      </div>

      <!-- TABEL REKAP ARUS KAS -->
      <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 uppercase">Jurnal Rincian Penerimaan Kas</h3>
          <span class="text-xs font-bold text-slate-400">Total {{ transactions.total || 0 }} catatan</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
              <tr>
                <th class="p-4 pl-6">No. Faktur</th>
                <th class="p-4">Waktu</th>
                <th class="p-4">Kanal Pembayaran</th>
                <th class="p-4 text-right">Nilai Bruto</th>
                <th class="p-4 text-right">Potongan Fee (0.7%)</th>
                <th class="p-4 pr-6 text-right">Penerimaan Kas Bersih</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr v-for="sale in transactions.data" :key="sale.id" class="hover:bg-slate-50/80 transition">
                <td class="p-4 pl-6 font-mono font-black text-slate-900">
                  {{ sale.transaction_number }}
                </td>
                <td class="p-4 text-slate-500 whitespace-nowrap">
                  {{ sale.created_at }}
                </td>
                <td class="p-4">
                  <span 
                    :class="sale.payment_method === 'cash' ? 'bg-emerald-50 text-[#00661A] border-emerald-200' : 'bg-purple-50 text-purple-700 border-purple-200'"
                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border"
                  >
                    {{ sale.payment_method === 'cash' ? '💵 Tunai' : '📱 QRIS DOKU' }}
                  </span>
                </td>
                <td class="p-4 text-right font-bold text-slate-800">
                  {{ formatRupiah(sale.total_amount) }}
                </td>
                <td class="p-4 text-right font-bold text-rose-500">
                  {{ sale.payment_method === 'qris' ? '-' + formatRupiah(Math.round(sale.total_amount * 0.007)) : 'Rp 0' }}
                </td>
                <td class="p-4 pr-6 text-right font-black text-[#00880F] text-sm">
                  {{ formatRupiah(sale.payment_method === 'qris' ? (sale.total_amount - Math.round(sale.total_amount * 0.007)) : sale.total_amount) }}
                </td>
              </tr>

              <tr v-if="!transactions.data || transactions.data.length === 0">
                <td colspan="6" class="py-16 text-center text-slate-400 font-bold italic">
                  Tidak ada catatan keuangan pada periode ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div v-if="transactions.links && transactions.links.length > 3" class="p-6 border-t border-slate-100 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-bold">
            Halaman {{ transactions.current_page }} dari {{ transactions.last_page }}
          </p>
          <div class="flex items-center space-x-1">
            <Link 
              v-for="(link, i) in transactions.links" 
              :key="i"
              :href="link.url || '#'"
              :class="link.active ? 'bg-[#00AA13] text-white font-black' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold'"
              class="px-3.5 py-2 rounded-xl text-xs transition"
              v-html="link.label"
            />
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

const props = defineProps({
  transactions: Object,
  stats: Object,
  chartData: Array,
  periodLabel: String,
  filters: Object,
});

const filterForm = ref({
  period: props.filters?.period || 'all',
  date: props.filters?.date || new Date().toISOString().slice(0, 10),
  month: props.filters?.month || new Date().toISOString().slice(0, 7),
  quarter: props.filters?.quarter || Math.ceil((new Date().getMonth() + 1) / 3),
  year: props.filters?.year || new Date().getFullYear(),
  payment_method: props.filters?.payment_method || 'all',
  search: props.filters?.search || '',
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val || 0);
};

const buildQueryString = () => {
  const params = new URLSearchParams();
  Object.keys(filterForm.value).forEach((key) => {
    if (filterForm.value[key] !== null && filterForm.value[key] !== '') {
      params.append(key, filterForm.value[key]);
    }
  });
  return params.toString();
};

const applyFilter = () => {
  router.get('/admin/reports/finance', filterForm.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilter = () => {
  filterForm.value = {
    period: 'all',
    date: new Date().toISOString().slice(0, 10),
    month: new Date().toISOString().slice(0, 7),
    quarter: Math.ceil((new Date().getMonth() + 1) / 3),
    year: new Date().getFullYear(),
    payment_method: 'all',
    search: '',
  };
  applyFilter();
};
</script>
