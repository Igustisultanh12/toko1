<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="productForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nama Produk</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name') }}" required>
                            </div>

                            <div>
                                <label for="barcode" class="block font-medium text-sm text-gray-700">Barcode (Opsional)</label>
                                <div class="flex mt-1">
                                    <input type="text" name="barcode" id="barcode" x-model="barcode" class="flex-grow rounded-l-md border-gray-300 shadow-sm" value="{{ old('barcode') }}">
                                    <button @click.prevent="startScanner()" type="button" class="px-4 py-2 bg-gray-700 text-white rounded-r-md hover:bg-gray-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="price" class="block font-medium text-sm text-gray-700">Harga</label>
                                <input type="number" name="price" id="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('price') }}" required>
                            </div>

                            <div>
                                <label for="stock" class="block font-medium text-sm text-gray-700">Stok</label>
                                <input type="number" name="stock" id="stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('stock') }}" required>
                            </div>

                            <div>
                                <label for="discount_percent" class="block font-medium text-sm text-gray-700">Diskon (%) (Opsional)</label>
                                <input type="number" name="discount_percent" id="discount_percent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('discount_percent', 0) }}">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi (Opsional)</label>
                                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal untuk Scanner Barcode -->
        <div x-show="isScannerActive" @click.away="stopScanner()" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/2 lg:w-1/3">
                <h3 class="text-lg font-medium mb-4">Pindai Barcode</h3>
                <div id="admin-create-reader" class="w-full"></div>
                <button @click="stopScanner()" class="mt-4 w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productForm', () => ({
                barcode: '',
                isScannerActive: false,
                html5QrCode: null,
                startScanner() {
                    this.isScannerActive = true;
                    this.$nextTick(() => {
                        // Inisialisasi scanner hanya jika belum ada
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("admin-create-reader");
                        }
                        
                        const config = { fps: 10, qrbox: { width: 250, height: 150 } };
                        const onScanSuccess = (decodedText, decodedResult) => {
                            this.barcode = decodedText;
                            this.stopScanner();
                        };
                        
                        this.html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                            .catch(err => {
                                console.error("Gagal memulai scanner:", err);
                                alert('Gagal mengakses kamera.');
                                this.isScannerActive = false;
                            });
                    });
                },
                stopScanner() {
                    if (this.html5QrCode && this.isScannerActive) {
                        this.html5QrCode.stop().catch(err => console.error("Gagal menghentikan scanner:", err));
                    }
                    this.isScannerActive = false;
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>