<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Kartu Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kartu Total Produk -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Total Produk</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600">
                            {{-- Data dinamis akan ditambahkan di sini --}}
                            150 
                        </p>
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-indigo-500 hover:underline mt-4 inline-block">Lihat Detail &rarr;</a>
                    </div>
                </div>

                <!-- Kartu Total Penjualan Hari Ini -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Penjualan Hari Ini</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600">
                            Rp 1.250.000
                        </p>
                         <a href="#" class="text-sm text-green-500 hover:underline mt-4 inline-block">Lihat Laporan &rarr;</a>
                    </div>
                </div>

                <!-- Kartu Stok Hampir Habis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Stok Hampir Habis</h3>
                        <p class="mt-1 text-3xl font-semibold text-red-600">
                            5 Produk
                        </p>
                         <a href="#" class="text-sm text-red-500 hover:underline mt-4 inline-block">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- Konten lainnya bisa ditambahkan di sini --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    Selamat datang kembali, {{ Auth::user()->name }}!
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
