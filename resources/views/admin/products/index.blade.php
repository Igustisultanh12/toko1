@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('header_title', 'Katalog & Stok Produk')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto pb-10" x-data="quickStockManager()" x-init="init()">

    {{-- HEADER BAR: IMPOR & TAMBAH PRODUK --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Katalog Inventaris Produk</h3>
            <p class="text-xs text-gray-400 font-medium">Kelola data barang, barcode, harga jual, dan stok realtime.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.reports.stock.pdf') }}" 
               class="flex-1 sm:flex-none text-center px-4 py-3 bg-[#EE2737] hover:bg-rose-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-200 transition flex items-center justify-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                PDF Stok
            </a>
            <a href="{{ route('admin.reports.stock') }}" 
               class="flex-1 sm:flex-none text-center px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-[#00880F] rounded-2xl font-black text-xs uppercase tracking-wider transition border border-emerald-200/60 flex items-center justify-center">
                📊 Rekap Stok
            </a>
            <a href="{{ route('admin.products.import.show') }}" 
               class="flex-1 sm:flex-none text-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
                📥 Impor
            </a>
            <a href="{{ route('admin.products.create') }}" 
               class="flex-1 sm:flex-none text-center px-5 py-3 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center justify-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5"/></svg>
                Tambah
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI FLASH --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-[#00880F] border border-emerald-200/80 rounded-2xl font-bold text-xs flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-[#00AA13]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- KOTAK UPDATE STOK CEPAT --}}
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50/50 p-6 sm:p-8 rounded-[2.5rem] border border-emerald-200/60 shadow-sm space-y-4">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-[#00AA13] text-white rounded-2xl flex items-center justify-center shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">Scan / Tambah Stok Cepat</h3>
                <p class="text-[10px] text-gray-500 font-medium">Ketik atau scan barcode barang untuk menambah stok secara instan</p>
            </div>
        </div>

        <div x-show="message" x-cloak :class="messageType === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="p-3.5 rounded-xl font-bold text-xs" x-text="message"></div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-6">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Scan Barcode / Kode SKU</label>
                <input type="text" x-model="barcode" @keydown.enter.prevent="updateStock()" x-ref="barcodeInput" placeholder="Arahkan scanner ke barcode..." 
                       class="w-full px-4 py-3 bg-white border-2 border-emerald-200/60 rounded-2xl outline-none focus:border-[#00AA13] transition-all font-bold text-xs text-gray-800">
            </div>
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Jumlah Unit (+)</label>
                <input type="number" x-model.number="quantity" min="1" 
                       class="w-full px-4 py-3 bg-white border-2 border-emerald-200/60 rounded-2xl outline-none focus:border-[#00AA13] transition-all font-black text-center text-xs text-gray-800">
            </div>
            <div class="md:col-span-3">
                <button @click="updateStock()" :disabled="isLoading" 
                        class="w-full py-3.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition disabled:bg-gray-300">
                    <span x-show="!isLoading">+ Tambah Stok</span>
                    <span x-show="isLoading">Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- TABEL PRODUK --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <form action="{{ route('admin.products.index') }}" method="GET" class="relative">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                <input type="text" name="search" placeholder="Cari berdasarkan nama atau kode barcode produk..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none focus:border-[#00AA13] transition-all text-xs font-bold text-gray-800" value="{{ request('search') }}">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="p-5">Foto</th>
                        <th class="p-5">Informasi Produk</th>
                        <th class="p-5 text-center">Barcode</th>
                        <th class="p-5 text-right">Harga Jual</th>
                        <th class="p-5 text-center">Sisa Stok</th>
                        <th class="p-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($products as $product)
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            {{-- FOTO PRODUK --}}
                            <td class="p-5 w-16">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-2xl object-cover border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 text-lg border border-gray-200">
                                        🛍️
                                    </div>
                                @endif
                            </td>
                            <td class="p-5">
                                <p class="font-black text-gray-900 text-sm leading-tight uppercase">{{ $product->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">SKU #{{ $product->id }} • {{ $product->description ?: 'Umum' }}</p>
                            </td>
                            <td class="p-5 text-center">
                                <span class="font-mono text-xs bg-gray-100 px-3 py-1 rounded-xl text-gray-700 font-black tracking-wider">{{ $product->barcode ?: '-' }}</span>
                            </td>
                            <td class="p-5 text-right font-black text-[#00880F] text-sm">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="p-5 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $product->stock <= 0 ? 'bg-rose-50 text-rose-600' : ($product->stock <= 10 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-[#00880F]') }}">
                                    {{ $product->stock }} pcs
                                </span>
                            </td>
                            <td class="p-5 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="p-2 text-[#00880F] hover:bg-emerald-100 rounded-xl transition" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirmDelete(event, 'Yakin ingin menghapus produk {{ addslashes($product->name) }}? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Hapus Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-gray-300 font-bold uppercase text-xs italic">
                                Belum ada data produk terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100 bg-white">
            {{ $products->links() }}
        </div>
    </div>

</div>

<script>
function quickStockManager() {
    return {
        barcode: '',
        quantity: 1,
        isLoading: false,
        message: '',
        messageType: '',
        
        init() {
            this.$nextTick(() => {
                this.$refs.barcodeInput?.focus();
            });
        },

        async updateStock() {
            if (!this.barcode || this.quantity < 1) {
                this.showMessage('Barcode dan jumlah wajib diisi.', 'error');
                return;
            }
            
            this.isLoading = true;
            this.message = '';

            try {
                const response = await fetch('{{ route("admin.products.quick-stock") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        barcode: this.barcode,
                        quantity: this.quantity,
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.showMessage(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    if (response.status === 404) {
                        Swal.fire({
                            title: 'Barang Belum Terdaftar!',
                            text: `Barcode [${this.barcode}] belum ada di sistem. Ingin menambahkan sebagai produk baru sekarang?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#00AA13',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Ya, Tambahkan',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = `{{ route('admin.products.create') }}?barcode=${this.barcode}`;
                            } else {
                                this.showMessage(data.message, 'error');
                            }
                        });
                    } else {
                        this.showMessage(data.message || 'Terjadi kesalahan.', 'error');
                    }
                }
            } catch (error) {
                this.showMessage('Gagal terhubung ke server.', 'error');
            } finally {
                this.isLoading = false;
                this.barcode = '';
                this.quantity = 1;
                this.$refs.barcodeInput?.focus();
            }
        },

        showMessage(msg, type) {
            this.message = msg;
            this.messageType = type;
            setTimeout(() => this.message = '', 3500);
        }
    }
}
</script>
@endsection
