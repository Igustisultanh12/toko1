<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="quickStockManager()" x-init="init()">

                    {{-- Menampilkan notifikasi sukses --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <!-- FITUR TAMBAH STOK CEPAT -->
                    <div class="mb-6 p-4 border rounded-md bg-gray-50">
                        <h3 class="text-lg font-semibold mb-2">Tambah Stok Cepat</h3>
                        <p class="text-sm text-gray-600 mb-4">Scan atau masukkan barcode untuk menambah stok produk yang sudah terdaftar.</p>
                        
                        <div x-show="message" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="p-3 rounded-md mb-4 text-sm" x-text="message"></div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="qs_barcode" class="block font-medium text-sm text-gray-700">Barcode</label>
                                <input type="text" id="qs_barcode" x-model="barcode" @keydown.enter.prevent="updateStock()" x-ref="barcodeInput" placeholder="Scan atau ketik barcode..." class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <div>
                                <label for="qs_quantity" class="block font-medium text-sm text-gray-700">Jumlah Tambahan</label>
                                <input type="number" id="qs_quantity" x-model.number="quantity" min="1" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <div>
                                <button @click="updateStock()" :disabled="isLoading" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 disabled:bg-blue-300">
                                    <span x-show="!isLoading">Tambah Stok</span>
                                    <span x-show="isLoading">Memproses...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- AKHIR FITUR TAMBAH STOK CEPAT -->

                    {{-- Form Pencarian Produk --}}
                    <div class="mb-4">
                        <form action="{{ route('admin.products.index') }}" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Cari Nama Produk atau Barcode..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ request('search') }}">
                                <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Cari</button>
                            </div>
                        </form>
                    </div>

                    {{-- Tombol Aksi Utama --}}
                    <div class="flex justify-end mb-4 space-x-2">
                        <a href="{{ route('admin.products.import.show') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Impor dari Excel
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Tambah Produk
                        </a>
                    </div>
                    
                    {{-- Tabel Daftar Produk --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->barcode }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->stock }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">Tidak ada data produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>

                </div>
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
                        this.$refs.barcodeInput.focus();
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
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            if (response.status === 404) {
                                if (confirm('Barang belum terdaftar. Apakah Anda ingin menambahkan produk baru ini?')) {
                                    window.location.href = `{{ route('admin.products.create') }}?barcode=${this.barcode}`;
                                } else {
                                    this.showMessage(data.message, 'error');
                                }
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
                        this.$refs.barcodeInput.focus();
                    }
                },

                showMessage(msg, type) {
                    this.message = msg;
                    this.messageType = type;
                    setTimeout(() => this.message = '', 3000);
                }
            }
        }
    </script>
</x-app-layout>