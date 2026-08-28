<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Baris Statistik Kunci -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card Pendapatan Hari Ini -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                    </div>
                    <!-- Card Transaksi Hari Ini -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Transaksi Hari Ini</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $transaksiHariIni }}</p>
                    </div>
                    <!-- Card Produk Terjual -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Produk Terjual (Hari Ini)</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $produkTerjualHariIni }}</p>
                    </div>
                    <!-- Card Total Produk -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Total Jenis Produk</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalProduk }}</p>
                    </div>
                </div>

                <!-- Baris Grafik dan Penjualan Terakhir -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Grafik Penjualan -->
                    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Penjualan (7 Hari Terakhir)</h3>
                        {{-- Bungkus canvas dengan div yang memiliki tinggi tetap dan posisi relatif --}}
                        <div class="relative h-80">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <!-- Penjualan Terakhir -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Penjualan Terakhir</h3>
                        <div class="space-y-4">
                            @forelse($penjualanTerakhir as $sale)
                                <div class="flex justify-between items-center text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $sale->customer->name ?? 'Pelanggan' }}</p>
                                        <p class="text-gray-500">{{ $sale->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="font-semibold text-green-600">+Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Belum ada penjualan hari ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Baris Produk Terlaris -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Produk Terlaris (Bulan Ini)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <tbody>
                                @forelse($produkTerlaris as $index => $product)
                                <tr class="border-b">
                                    <td class="py-2 px-4 font-medium">{{ $index + 1 }}. {{ $product->name }}</td>
                                    <td class="py-2 px-4 text-right text-gray-600">{{ $product->total_terjual }} terjual</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="py-4 text-center text-gray-500">Belum ada data penjualan bulan ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script untuk Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesData = JSON.parse('{!! $chartData !!}');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: salesData.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</x-app-layout>