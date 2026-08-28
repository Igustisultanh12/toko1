<div class="p-6 pb-4">
    <div class="flex items-center space-x-3">
        @if(!empty($shop['app_favicon']))
            <img src="{{ route('media.file', ['path' => $shop['app_favicon']]) }}" class="w-10 h-10 rounded-2xl bg-white p-1 object-contain shadow-md shadow-emerald-950/40">
        @elseif(!empty($shop['shop_logo']))
            <img src="{{ route('media.file', ['path' => $shop['shop_logo']]) }}" class="w-10 h-10 rounded-2xl bg-white p-1 object-contain shadow-md shadow-emerald-950/40">
        @else
            <div class="w-10 h-10 rounded-2xl bg-[#00AA13] flex items-center justify-center font-black text-white text-lg shadow-md border border-emerald-400/30">
                {{ strtoupper(substr($shop['app_name'] ?? 'S', 0, 1)) }}
            </div>
        @endif
        <div>
            <h1 class="text-xl font-black tracking-tight text-white uppercase leading-none">{{ $shop['app_name'] ?? 'SIKANDA' }}</h1>
            <p class="text-[9px] text-emerald-300 uppercase tracking-widest font-black mt-1">{{ $shop['app_tagline'] ?? 'Sultan Web Engine' }}</p>
        </div>
    </div>
</div>

<div class="px-6 py-2">
    <div class="h-px bg-white/10 w-full"></div>
</div>

<nav class="flex-grow px-3 py-2 space-y-1.5 overflow-y-auto">
    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}" 
       class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('dashboard') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('dashboard') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <span>Dashboard</span>
    </a>

    {{-- KASIR POS (BISA DIAKSES OLEH KASIR & ADMIN) --}}
    <a href="{{ route('cashier.pos.index') }}" 
       class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('cashier.pos.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('cashier.pos.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <span>Kasir POS</span>
    </a>

    {{-- PESANAN ONLINE (NOTIFIKASI REALTIME - KASIR & ADMIN) --}}
    <a href="{{ route('admin.orders.index') }}" 
       class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.orders.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <span class="flex-1">Pesanan Online</span>
        <span id="sidebarOrderBadge" class="bg-rose-500 text-white px-2 py-0.5 rounded-full text-[9px] font-black hidden animate-pulse">0</span>
    </a>

    @if(Auth::user()->role === 'admin')
        {{-- MANAJEMEN PRODUK --}}
        <a href="{{ route('admin.products.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.products.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.products.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span>Produk & Stok</span>
        </a>

        {{-- LAPORAN PENJUALAN --}}
        <a href="{{ route('admin.reports.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ (request()->routeIs('admin.reports.index') || request()->routeIs('admin.reports.sales')) ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ (request()->routeIs('admin.reports.index') || request()->routeIs('admin.reports.sales')) ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M9 17v-2m3 2v-4m3 2v-6m-8 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span>Laporan Penjualan</span>
        </a>

        {{-- LAPORAN STOK BARANG --}}
        <a href="{{ route('admin.reports.stock') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.reports.stock*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.reports.stock*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <span>Laporan Stok</span>
        </a>

        {{-- LAPORAN KEUANGAN --}}
        <a href="{{ route('admin.reports.finance') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ (request()->routeIs('admin.reports.finance*') || request()->routeIs('admin.reports.qris*')) ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ (request()->routeIs('admin.reports.finance*') || request()->routeIs('admin.reports.qris*')) ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span>Laporan Keuangan</span>
        </a>

        {{-- MANAJEMEN AKUN --}}
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.users.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.users.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span>Manajemen Akun</span>
        </a>

        {{-- IDENTITAS & PENGATURAN TOKO --}}
        <a href="{{ route('admin.settings.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.settings.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
            </div>
            <span>Pengaturan Toko</span>
        </a>

        {{-- PUSAT BACKUP & MIGRASI DATA --}}
        <a href="{{ route('admin.backup.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.backup.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.backup.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <span>Backup & Migrasi</span>
        </a>
    @endif

    {{-- BUKU PANDUAN PENGGUNA (KASIR & ADMIN) --}}
    <a href="{{ route('admin.manual.index') }}" 
       class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition font-black text-xs uppercase tracking-wider {{ request()->routeIs('admin.manual.*') ? 'bg-white text-[#00661A] shadow-lg shadow-black/10' : 'text-emerald-100 hover:bg-white/10' }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ request()->routeIs('admin.manual.*') ? 'bg-emerald-100 text-[#00880F]' : 'bg-white/10 text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <span>Buku Panduan</span>
    </a>
</nav>

{{-- FOOTER PROFILE & LOGOUT --}}
<div class="p-4 border-t border-white/10 bg-black/10 text-center space-y-3">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 rounded-2xl bg-white/10 hover:bg-rose-600 active:scale-95 text-white font-black text-xs uppercase tracking-wider transition-all">
            <svg class="w-4 h-4 text-rose-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Keluar</span>
        </button>
    </form>
    <p class="text-[9px] font-bold text-emerald-200/60 uppercase tracking-widest">
        &copy; {{ date('Y') }} I Gusti Sultan
    </p>
</div>