<template>
  <AdminLayout>
    <template #header>Kelola Pesanan Toko</template>
    <Head title="Kelola Pesanan" />

    <div class="space-y-6">
      
      <!-- TOOLBAR FILTER & SEARCH -->
      <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="w-full sm:w-80 relative">
          <input 
            v-model="searchQuery" 
            @input="handleSearch"
            type="text" 
            placeholder="Cari No. Pesanan / Nama..."
            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
          >
          <span class="absolute left-3.5 top-3 text-slate-400 text-xs">🔍</span>
        </div>

        <!-- FILTER STATUS -->
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
          <button 
            v-for="st in statusFilters" 
            :key="st.key"
            @click="filterStatus(st.key)"
            :class="activeStatus === st.key ? 'bg-[#00AA13] text-white font-black shadow-md shadow-emerald-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold'"
            class="px-3.5 py-2 rounded-xl text-[10px] uppercase tracking-wider transition shrink-0"
          >
            {{ st.label }}
          </button>
        </div>
      </div>

      <!-- TABEL PESANAN -->
      <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
              <tr>
                <th class="p-4 pl-6">No. Pesanan</th>
                <th class="p-4">Pelanggan</th>
                <th class="p-4">Ekspedisi</th>
                <th class="p-4">Total</th>
                <th class="p-4">Status</th>
                <th class="p-4 pr-6 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50/80 transition">
                <td class="p-4 pl-6">
                  <Link :href="'/admin/orders/' + order.id" class="font-mono font-black text-slate-900 hover:text-[#00AA13] block">
                    {{ order.order_number }}
                  </Link>
                  <span class="text-[10px] text-slate-400">{{ order.created_at }}</span>
                </td>

                <td class="p-4">
                  <h4 class="font-black text-slate-800 text-xs">{{ order.customer_name }}</h4>
                  <p class="text-[10px] text-slate-400">{{ order.customer_phone }}</p>
                </td>

                <td class="p-4 font-bold text-slate-600">
                  <span>{{ order.courier }}</span>
                  <span v-if="order.tracking_number" class="block font-mono text-[9px] text-[#00880F]">Resi: {{ order.tracking_number }}</span>
                </td>

                <td class="p-4 font-black text-[#00880F]">
                  {{ formatRupiah(order.total_amount) }}
                </td>

                <td class="p-4">
                  <span 
                    :class="getStatusBadgeClass(order.status)"
                    class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider inline-block"
                  >
                    {{ order.status_label || order.status }}
                  </span>
                </td>

                <td class="p-4 pr-6 text-right">
                  <Link 
                    :href="'/admin/orders/' + order.id" 
                    class="px-4 py-2 bg-emerald-50 hover:bg-[#00AA13] text-[#00661A] hover:text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition inline-block border border-emerald-200"
                  >
                    Kelola Pesanan &rarr;
                  </Link>
                </td>
              </tr>

              <tr v-if="orders.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400 font-bold italic">
                  Tidak ada data pesanan yang sesuai.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINASI -->
        <div v-if="orders.links && orders.links.length > 3" class="p-4 border-t border-slate-100 flex justify-center items-center gap-1.5">
          <template v-for="(link, key) in orders.links" :key="key">
            <Link 
              v-if="link.url" 
              :href="link.url" 
              v-html="link.label"
              :class="link.active ? 'bg-[#00AA13] text-white font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold'"
              class="px-3 py-1.5 rounded-xl text-xs uppercase transition border border-slate-200"
            />
            <span v-else v-html="link.label" class="px-3 py-1.5 text-xs text-slate-300"></span>
          </template>
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
  orders: Object,
  search: String,
  status: String,
});

const searchQuery = ref(props.search || '');
const activeStatus = ref(props.status || 'all');

const statusFilters = [
  { key: 'all', label: 'Semua' },
  { key: 'pending_payment', label: 'Belum Bayar' },
  { key: 'paid', label: 'Dibayar' },
  { key: 'processing', label: 'Disiapkan' },
  { key: 'shipped', label: 'Dikirim' },
  { key: 'completed', label: 'Selesai' },
];

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);

let searchTimer = null;
const handleSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    router.get('/admin/orders', { search: searchQuery.value, status: activeStatus.value }, { preserveState: true, replace: true });
  }, 400);
};

const filterStatus = (st) => {
  activeStatus.value = st;
  router.get('/admin/orders', { search: searchQuery.value, status: st }, { preserveState: true, replace: true });
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
</script>
