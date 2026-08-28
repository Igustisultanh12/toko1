<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-between text-slate-800">
    
    <!-- NAVBAR ATAS TOKO ONLINE -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <!-- LOGO & BRAND -->
        <Link href="/order" class="flex items-center space-x-3 group">
          <div class="w-10 h-10 rounded-2xl bg-[#00AA13] flex items-center justify-center text-white text-xl font-black shadow-md shadow-emerald-600/30 group-hover:scale-105 transition">
            ⚡
          </div>
          <div>
            <h1 class="font-black text-sm uppercase tracking-tight text-slate-900 leading-tight">
              {{ $page.props.shop?.shop_name || 'SIBALOG STORE' }}
            </h1>
            <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Toko Online Resmi &bull; Pembayaran QRIS</p>
          </div>
        </Link>

        <!-- MENU LINK -->
        <div class="flex items-center space-x-2 sm:space-x-3">
          <Link 
            href="/order" 
            :class="isActive('/order') && !isActive('/order/track') && !isActive('/order/complaint') ? 'bg-emerald-50 text-[#00661A] font-black border-emerald-200' : 'text-slate-600 hover:text-slate-900 font-bold border-transparent'"
            class="px-3 sm:px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition border flex items-center space-x-1.5"
          >
            <span>🛍️</span>
            <span class="hidden sm:inline">Belanja</span>
          </Link>

          <Link 
            href="/order/track" 
            :class="isActive('/order/track') ? 'bg-emerald-50 text-[#00661A] font-black border-emerald-200' : 'text-slate-600 hover:text-slate-900 font-bold border-transparent'"
            class="px-3 sm:px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition border flex items-center space-x-1.5"
          >
            <span>📦</span>
            <span>Lacak Pesanan</span>
          </Link>

          <Link 
            href="/login" 
            class="px-3 sm:px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition"
          >
            Staff Login
          </Link>
        </div>
      </div>
    </header>

    <!-- FLASH NOTIFICATIONS -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 w-full pt-4">
      <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-50 border-2 border-emerald-200 text-[#00661A] rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-2">
          <span>✅</span>
          <span>{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <div v-if="$page.props.flash?.error" class="p-4 bg-rose-50 border-2 border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-2">
          <span>⚠️</span>
          <span>{{ $page.props.flash.error }}</span>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 w-full">
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-400 font-medium space-y-2 mt-12">
      <p class="font-bold text-slate-600 uppercase tracking-wider">&copy; {{ new Date().getFullYear() }} {{ $page.props.shop?.shop_name || 'SIBALOG' }} &bull; Seluruh Hak Cipta Dilindungi</p>
      <p class="text-[11px] text-slate-400">Pembayaran Resmi didukung oleh DOKU Payment Gateway (QRIS Semua Bank & E-Wallet)</p>
    </footer>

  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const isActive = (path) => {
  return page.url.startsWith(path);
};
</script>
