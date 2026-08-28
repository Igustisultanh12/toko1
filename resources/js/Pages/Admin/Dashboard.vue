<template>
  <AdminLayout>
    <template #header>Dashboard Ringkasan Penjualan</template>
    <Head title="Dashboard Admin" />

    <div class="space-y-8">
      
      <!-- STATS CARDS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <!-- CARD 1: PENDAPATAN HARI INI -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
          <div class="space-y-1">
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Omset Hari Ini</p>
            <h3 class="text-2xl font-black text-[#00880F]">{{ formatRupiah(today_revenue || 0) }}</h3>
            <p class="text-[10px] text-emerald-600 font-bold">🟢 Transaksi Lunas</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#00AA13] flex items-center justify-center text-2xl">
            💰
          </div>
        </div>

        <!-- CARD 2: PENDAPATAN BULAN INI -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
          <div class="space-y-1">
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Omset Bulan Ini</p>
            <h3 class="text-2xl font-black text-slate-800">{{ formatRupiah(month_revenue || 0) }}</h3>
            <p class="text-[10px] text-slate-400 font-bold">Periode {{ currentMonthName }}</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
            📅
          </div>
        </div>

        <!-- CARD 3: TOTAL PESANAN ONLINE -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
          <div class="space-y-1">
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pesanan Online</p>
            <h3 class="text-2xl font-black text-slate-800">{{ total_orders || 0 }}</h3>
            <p class="text-[10px] text-purple-600 font-bold">{{ pending_orders || 0 }} Perlu Diproses</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
            📦
          </div>
        </div>

        <!-- CARD 4: TOTAL PRODUK & STOK KRITIS -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
          <div class="space-y-1">
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Katalog Produk</p>
            <h3 class="text-2xl font-black text-slate-800">{{ total_products || 0 }} Item</h3>
            <p :class="(low_stock_products?.length || 0) > 0 ? 'text-amber-600' : 'text-slate-400'" class="text-[10px] font-bold">
              {{ (low_stock_products?.length || 0) }} Stok Menipis
            </p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
            🏷️
          </div>
        </div>
      </div>

      <!-- PESANAN TERBARU & PERINGATAN STOK -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- PESANAN ONLINE TERBARU (7 SPAN) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
          <div class="flex justify-between items-center pb-2 border-b border-slate-100">
            <h3 class="font-black text-xs uppercase tracking-wider text-slate-800">Pesanan Online Terbaru</h3>
            <Link href="/admin/orders" class="text-[10px] font-black text-[#00880F] hover:underline uppercase">Lihat Semua &rarr;</Link>
          </div>

          <div class="space-y-2.5">
            <div 
              v-for="order in recent_orders" 
              :key="order.id"
              class="p-3.5 bg-slate-50 hover:bg-slate-100/80 rounded-2xl border border-slate-100 flex justify-between items-center transition"
            >
              <div class="space-y-0.5">
                <Link :href="'/admin/orders/' + order.id" class="font-mono text-xs font-black text-slate-900 hover:text-[#00AA13]">{{ order.order_number }}</Link>
                <p class="text-[10px] text-slate-500 font-medium">{{ order.customer_name }} &bull; {{ order.courier }}</p>
              </div>
              <div class="text-right space-y-1">
                <span class="text-xs font-black text-[#00880F] block">{{ formatRupiah(order.total_amount) }}</span>
                <span 
                  :class="order.payment_status === 'paid' ? 'bg-emerald-100 text-[#00661A]' : 'bg-amber-100 text-amber-800'"
                  class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider"
                >
                  {{ order.status_label || order.status }}
                </span>
              </div>
            </div>

            <div v-if="!recent_orders || recent_orders.length === 0" class="text-center py-8 text-slate-400 font-bold text-xs italic">
              Belum ada pesanan terbaru.
            </div>
          </div>
        </div>

        <!-- PERINGATAN STOK MENIPIS (5 SPAN) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
          <div class="flex justify-between items-center pb-2 border-b border-slate-100">
            <h3 class="font-black text-xs uppercase tracking-wider text-amber-700">⚠️ Peringatan Stok Menipis</h3>
            <Link href="/admin/products" class="text-[10px] font-black text-[#00880F] hover:underline uppercase">Kelola Stok &rarr;</Link>
          </div>

          <div class="space-y-2.5">
            <div 
              v-for="product in low_stock_products" 
              :key="product.id"
              class="p-3 bg-amber-50/60 rounded-2xl border border-amber-200/80 flex justify-between items-center"
            >
              <div class="truncate pr-2">
                <h4 class="font-black text-xs text-slate-800 truncate">{{ product.name }}</h4>
                <p class="font-mono text-[9px] text-slate-400 font-bold">{{ product.barcode || '-' }}</p>
              </div>
              <span class="px-2.5 py-1 bg-amber-500 text-white rounded-xl text-xs font-black shrink-0">
                Sisa: {{ product.stock }}
              </span>
            </div>

            <div v-if="!low_stock_products || low_stock_products.length === 0" class="text-center py-8 text-emerald-600 font-bold text-xs italic">
              ✅ Semua stok produk dalam kondisi aman.
            </div>
          </div>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  today_revenue: Number,
  month_revenue: Number,
  total_orders: Number,
  pending_orders: Number,
  total_products: Number,
  recent_orders: Array,
  low_stock_products: Array,
});

const currentMonthName = computed(() => {
  return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date());
});

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
</script>
