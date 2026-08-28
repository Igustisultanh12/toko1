<template>
  <AdminLayout>
    <template #header>Laporan Stok & Inventaris</template>
    <Head title="Laporan Stok Barang" />

    <div class="space-y-6 pb-12">
      
      <!-- HEADER ACTION BAR -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div>
          <div class="inline-flex items-center space-x-2 px-3 py-1 bg-emerald-50 rounded-full text-[10px] font-black uppercase tracking-wider text-[#00880F] border border-emerald-200 mb-2">
            <span>📦</span>
            <span>Inventaris & Kuantitas Fisik</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">Rekapitulasi Stok & Valuasi Barang</h2>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Monitoring kuantitas persediaan fisik, peringatan barang menipis, dan total estimasi aset toko.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
          <a 
            :href="`/admin/reports/stock/pdf?${buildQueryString()}`" 
            target="_blank"
            class="flex-1 sm:flex-none px-5 py-3 bg-[#EE2737] hover:bg-rose-700 active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 transition flex items-center justify-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Cetak PDF Stok (Landscape)</span>
          </a>

          <a 
            :href="`/admin/reports/stock/excel?${buildQueryString()}`" 
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
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- 1. STATUS STOK -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status Kuantitas Stok</label>
              <select v-model="filterForm.stock_status" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
                <option value="all">📦 Semua Kondisi Stok</option>
                <option value="available">🟢 Stok Tersedia (> 10 pcs)</option>
                <option value="low">🟡 Stok Menipis (1 - 10 pcs)</option>
                <option value="empty">🔴 Stok Habis (0 pcs)</option>
              </select>
            </div>

            <!-- 2. URUTAN DATA -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Urutan Data</label>
              <select v-model="filterForm.sort_by" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
                <option value="name_asc">Nama Produk (A - Z)</option>
                <option value="stock_desc">Stok Terbanyak ↓</option>
                <option value="stock_asc">Stok Paling Sedikit ↑</option>
                <option value="price_desc">Harga Tertinggi ↓</option>
                <option value="price_asc">Harga Terendah ↑</option>
                <option value="latest">Produk Terbaru Ditambahkan</option>
              </select>
            </div>

            <!-- 3. PENCARIAN -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Cari Nama / Barcode</label>
              <input v-model="filterForm.search" type="text" placeholder="Ketik nama atau barcode..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition">
            </div>
          </div>

          <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
            <div class="flex items-center gap-2">
              <button type="submit" class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                🔍 Filter Stok
              </button>
              <button type="button" @click="resetFilter" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-xs uppercase transition">
                Reset
              </button>
            </div>
            <p class="text-xs text-slate-400 font-bold hidden sm:block">
              Filter Aktif: <span class="text-[#00880F]">{{ statusLabel }}</span>
            </p>
          </div>
        </form>
      </div>

      <!-- STATISTIK RINGKASAN STOK -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
        <!-- Total Valuasi (4 Span di Desktop) -->
        <div class="lg:col-span-4 bg-gradient-to-r from-[#00360D] via-[#004D13] to-[#00661A] p-6 rounded-3xl shadow-xl text-white space-y-1 relative overflow-hidden flex flex-col justify-between">
          <div>
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/10 text-emerald-200 text-[10px] font-black uppercase mb-1 border border-white/10">
              <span>💎</span>
              <span>Estimasi Valuasi Aset</span>
            </div>
            <h3 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-white mt-1 break-words">
              {{ formatRupiah(stats.total_valuation) }}
            </h3>
          </div>
          <p class="text-[10px] text-emerald-200/80 font-bold mt-2">● Nilai Total Aset Fisik di Gudang Toko</p>
        </div>

        <!-- 4 Sub Kartu (Masing-masing 2 Span di Desktop) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1 flex flex-col justify-between">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Macam Produk</p>
          <h3 class="text-2xl font-black text-slate-900">{{ stats.total_products_count }} <span class="text-xs text-slate-400 font-bold">Item</span></h3>
          <p class="text-[9px] text-slate-400 font-bold">Katalog Terdaftar</p>
        </div>

        <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1 flex flex-col justify-between">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Kuantitas Fisik</p>
          <h3 class="text-2xl font-black text-blue-600">{{ stats.total_physical_stock }} <span class="text-xs text-slate-400 font-bold">pcs</span></h3>
          <p class="text-[9px] text-blue-500 font-bold">Seluruh Unit Barang</p>
        </div>

        <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1 flex flex-col justify-between">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Stok Menipis (≤10)</p>
          <h3 class="text-2xl font-black text-amber-600">{{ stats.low_stock_count }} <span class="text-xs text-slate-400 font-bold">Item</span></h3>
          <p class="text-[9px] text-amber-600 font-bold">Perlu Restock</p>
        </div>

        <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1 flex flex-col justify-between">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Stok Habis (0)</p>
          <h3 class="text-2xl font-black text-rose-600">{{ stats.empty_stock_count }} <span class="text-xs text-slate-400 font-bold">Item</span></h3>
          <p class="text-[9px] text-rose-500 font-bold">Kosong di Gudang</p>
        </div>
      </div>

      <!-- TABEL DAFTAR STOK BARANG -->
      <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 uppercase">Daftar Persediaan & Nilai Inventaris</h3>
          <span class="text-xs font-bold text-slate-400">Menampilkan {{ products.data?.length || 0 }} barang</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
              <tr>
                <th class="p-4 pl-6">Produk / Barcode</th>
                <th class="p-4">Kategori</th>
                <th class="p-4 text-center">Sisa Kuantitas</th>
                <th class="p-4">Status Kondisi</th>
                <th class="p-4 text-right">Harga Jual</th>
                <th class="p-4 pr-6 text-right">Estimasi Valuasi Aset</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-slate-50/80 transition">
                <td class="p-4 pl-6">
                  <div class="flex items-center space-x-3">
                    <img 
                      :src="product.image_url || '/media-file?path=' + (product.image || 'products/placeholder.png')" 
                      class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0 bg-slate-100"
                      onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2394a3b8%22 stroke-width=%222%22><rect width=%2218%22 height=%2218%22 x=%223%22 y=%223%22 rx=%222%22 ry=%222%22/><circle cx=%229%22 cy=%229%22 r=%222%22/><path d=%22m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21%22/></svg>'"
                    >
                    <div>
                      <p class="font-black text-slate-900 text-xs">{{ product.name }}</p>
                      <p class="font-mono text-[10px] text-slate-400 mt-0.5">{{ product.barcode || 'NO-BARCODE' }}</p>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-xl text-[10px] font-bold">
                    {{ product.category || 'Umum' }}
                  </span>
                </td>
                <td class="p-4 text-center font-black text-sm">
                  {{ product.stock }} <span class="text-[10px] text-slate-400 font-normal">pcs</span>
                </td>
                <td class="p-4">
                  <span 
                    v-if="product.stock > 10" 
                    class="px-2.5 py-1 bg-emerald-50 text-[#00880F] border border-emerald-200 rounded-full text-[10px] font-black uppercase"
                  >
                    🟢 Tersedia
                  </span>
                  <span 
                    v-else-if="product.stock > 0" 
                    class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-black uppercase"
                  >
                    🟡 Menipis
                  </span>
                  <span 
                    v-else 
                    class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-[10px] font-black uppercase"
                  >
                    🔴 Habis
                  </span>
                </td>
                <td class="p-4 text-right font-bold text-slate-800">
                  {{ formatRupiah(product.price) }}
                </td>
                <td class="p-4 pr-6 text-right font-black text-[#00880F] text-sm">
                  {{ formatRupiah(product.price * product.stock) }}
                </td>
              </tr>

              <tr v-if="!products.data || products.data.length === 0">
                <td colspan="6" class="py-16 text-center text-slate-400 font-bold italic">
                  Tidak ada barang pada kriteria filter stok yang dipilih.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div v-if="products.links && products.links.length > 3" class="p-6 border-t border-slate-100 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-bold">
            Total {{ products.total }} produk terdata
          </p>
          <div class="flex items-center space-x-1">
            <Link 
              v-for="(link, i) in products.links" 
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
  products: Object,
  stats: Object,
  statusLabel: String,
  filters: Object,
});

const filterForm = ref({
  stock_status: props.filters?.stock_status || 'all',
  sort_by: props.filters?.sort_by || 'name_asc',
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
  router.get('/admin/reports/stock', filterForm.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilter = () => {
  filterForm.value = {
    stock_status: 'all',
    sort_by: 'name_asc',
    search: '',
  };
  applyFilter();
};
</script>
