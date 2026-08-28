<template>
  <div class="min-h-screen bg-slate-50 flex flex-col md:flex-row text-slate-800">
    
    <!-- SIDEBAR (DESKTOP & MOBILE DRAWER) -->
    <aside 
      :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
      class="fixed md:sticky top-0 left-0 z-40 w-64 h-screen bg-[#00360D] text-white flex flex-col justify-between transition-transform duration-300 ease-in-out shadow-2xl shrink-0"
    >
      <div class="p-6 space-y-8 flex-1 overflow-y-auto">
        <!-- LOGO & STORE NAME -->
        <div class="flex items-center space-x-3 pb-6 border-b border-emerald-900/60">
          <div class="w-10 h-10 rounded-2xl bg-[#00AA13] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-emerald-900/50">
            ⚡
          </div>
          <div>
            <h1 class="font-black text-sm uppercase tracking-tight text-white leading-tight">
              {{ $page.props.shop?.shop_name || 'SIBALOG POS' }}
            </h1>
            <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Panel Administrasi</p>
          </div>
        </div>

        <!-- NAVIGATION LINKS -->
        <nav class="space-y-1.5">
          <Link 
            href="/admin/dashboard" 
            :class="isActive('/admin/dashboard') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">📊</span>
            <span>Dashboard</span>
          </Link>

          <Link 
            href="/cashier/pos" 
            :class="isActive('/cashier/pos') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">🖥️</span>
            <span>Kasir POS</span>
          </Link>

          <Link 
            href="/admin/orders" 
            :class="isActive('/admin/orders') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">📦</span>
            <span>Kelola Pesanan</span>
          </Link>

          <Link 
            href="/admin/products" 
            :class="isActive('/admin/products') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">🏷️</span>
            <span>Produk & Stok</span>
          </Link>

          <Link 
            href="/admin/reports" 
            :class="isActive('/admin/reports') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">📈</span>
            <span>Laporan Penjualan</span>
          </Link>

          <Link 
            href="/admin/settings" 
            :class="isActive('/admin/settings') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">⚙️</span>
            <span>Pengaturan Toko</span>
          </Link>

          <Link 
            href="/admin/users" 
            :class="isActive('/admin/users') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">👥</span>
            <span>Akun Petugas</span>
          </Link>

          <Link 
            href="/admin/manual" 
            :class="isActive('/admin/manual') ? 'bg-[#00AA13] text-white font-black shadow-lg shadow-emerald-900/30' : 'text-emerald-100/80 hover:bg-white/10 hover:text-white font-bold'"
            class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs uppercase tracking-wider transition group"
          >
            <span class="text-base">📖</span>
            <span>Buku Panduan</span>
          </Link>
        </nav>
      </div>

      <!-- SIDEBAR FOOTER (USER PROFILE & LOGOUT) -->
      <div class="p-6 border-t border-emerald-900/60 bg-[#002609]">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full bg-emerald-800 flex items-center justify-center font-black text-xs text-white uppercase border border-emerald-600">
              {{ $page.props.auth?.user?.name ? $page.props.auth.user.name.charAt(0) : 'U' }}
            </div>
            <div class="truncate max-w-[110px]">
              <p class="font-black text-xs text-white truncate">{{ $page.props.auth?.user?.name || 'Administrator' }}</p>
              <p class="text-[10px] text-emerald-400 uppercase font-bold">{{ $page.props.auth?.user?.role || 'Admin' }}</p>
            </div>
          </div>
          <Link 
            href="/logout" 
            method="post" 
            as="button" 
            class="p-2 text-rose-300 hover:text-rose-100 hover:bg-rose-900/50 rounded-xl transition"
            title="Keluar"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </Link>
        </div>
      </div>
    </aside>

    <!-- BACKDROP MOBILE MENU -->
    <div 
      v-if="isMobileMenuOpen" 
      @click="isMobileMenuOpen = false" 
      class="fixed inset-0 bg-black/50 z-30 md:hidden backdrop-blur-sm"
    ></div>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- TOPBAR -->
      <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
        <div class="flex items-center space-x-4">
          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen" 
            class="p-2 rounded-xl bg-slate-100 text-slate-700 md:hidden hover:bg-slate-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
          <div>
            <h2 class="text-sm sm:text-base font-black text-slate-800 uppercase tracking-tight">
              <slot name="header">Panel Kontrol</slot>
            </h2>
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <a 
            href="/order" 
            target="_blank" 
            class="px-4 py-2 bg-emerald-50 hover:bg-[#00AA13] text-[#00880F] hover:text-white rounded-xl text-xs font-black uppercase tracking-wider transition flex items-center space-x-1.5 border border-emerald-200"
          >
            <span>🛍️</span>
            <span class="hidden sm:inline">Lihat Toko Online</span>
          </a>
        </div>
      </header>

      <!-- FLASH NOTIFICATIONS -->
      <div v-if="$page.props.flash?.success" class="m-6 mb-0 p-4 bg-emerald-50 border-2 border-emerald-200 text-[#00661A] rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-2">
          <span>✅</span>
          <span>{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <div v-if="$page.props.flash?.error" class="m-6 mb-0 p-4 bg-rose-50 border-2 border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-2">
          <span>⚠️</span>
          <span>{{ $page.props.flash.error }}</span>
        </div>
      </div>

      <!-- PAGE CONTENT -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const isMobileMenuOpen = ref(false);

const isActive = (path) => {
  return page.url.startsWith(path);
};
</script>
