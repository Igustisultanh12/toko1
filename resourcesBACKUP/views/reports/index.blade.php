{{-- resources/views/reports/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Pilih periode untuk melihat atau mencetak laporan penjualan.</p>

                    {{-- Form Filter --}}
                    <form action="{{ route('reports.index') }}" method="GET" class="border p-4 rounded-md mb-6">
                        <input type="hidden" name="filter" value="true">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="period" class="block font-medium text-sm text-gray-700">Pilih Periode:</label>
                                <select name="period" id="period" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="daily" @if($period == 'daily') selected @endif>Harian</option>
                                    <option value="weekly" @if($period == 'weekly') selected @endif>Mingguan</option>
                                    <option value="monthly" @if($period == 'monthly') selected @endif>Bulanan</option>
                                    <option value="yearly" @if($period == 'yearly') selected @endif>Tahunan</option>
                                </select>
                            </div>
                            <div id="date-input">
                                <label for="date" class="block font-medium text-sm text-gray-700">Tanggal:</label>
                                <input type="date" name="date" value="{{ $date }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <div id="month-input" style="display: none;">
                                <label for="month" class="block font-medium text-sm text-gray-700">Bulan:</label>
                                <input type="month" name="month" value="{{ $month }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <div id="year-input" style="display: none;">
                                <label for="year" class="block font-medium text-sm text-gray-700">Tahun:</label>
                                <input type="number" name="year" value="{{ $year }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Contoh: 2025">
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" name="action" value="view" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Tampilkan</button>
                                <button type="submit" name="action" value="download_pdf" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">Cetak PDF</button>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel Hasil Laporan --}}
                    @if(request()->has('filter'))
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $reportTitle }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terjual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php $totalPenjualanKeseluruhan = 0; @endphp
                                    @forelse($salesData as $index => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">PCS</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($data->product_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->total_quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($data->total_sales, 0, ',', '.') }}</td>
                                    </tr>
                                    @php $totalPenjualanKeseluruhan += $data->total_sales; @endphp
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center">Tidak ada data penjualan untuk periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($salesData->count() > 0)
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <th colspan="5" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Keseluruhan</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Rp {{ number_format($totalPenjualanKeseluruhan, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="mt-6 text-center text-gray-500">
                        <p>Silakan pilih periode dan klik "Tampilkan" untuk melihat laporan.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.getElementById('period');
            const dateInput = document.getElementById('date-input');
            const monthInput = document.getElementById('month-input');
            const yearInput = document.getElementById('year-input');

            function toggleInputs() {
                const period = periodSelect.value;
                dateInput.style.display = 'none';
                monthInput.style.display = 'none';
                yearInput.style.display = 'none';

                if (period === 'daily' || period === 'weekly') {
                    dateInput.style.display = 'block';
                } else if (period === 'monthly') {
                    monthInput.style.display = 'block';
                } else if (period === 'yearly') {
                    yearInput.style.display = 'block';
                }
            }

            periodSelect.addEventListener('change', toggleInputs);
            toggleInputs(); // Run on page load
        });
    </script>
</x-app-layout>