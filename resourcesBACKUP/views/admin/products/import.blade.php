<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Impor Produk dari Excel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="file" class="block font-medium text-sm text-gray-700">Pilih File Excel (.xlsx, .xls)</label>
                            <input type="file" name="file" id="file" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>

                            <x-primary-button>
                                {{ __('Impor Produk') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold">Petunjuk Format File Excel</h3>
                        <p class="mt-2 text-sm text-gray-600">Pastikan file Excel Anda memiliki header pada baris pertama dengan nama kolom sebagai berikut (huruf kecil):</p>
                        <ul class="list-disc list-inside mt-2 text-sm text-gray-600">
                            <li>`nama` (Wajib): Nama produk</li>
                            <li>`barcode` (Opsional): Barcode produk, harus unik jika diisi</li>
                            <li>`harga` (Wajib): Harga jual produk (hanya angka)</li>
                            <li>`stok` (Wajib): Jumlah stok awal (hanya angka)</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
