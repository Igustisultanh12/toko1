{{-- resources/views/reports/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3, .header p { margin: 0; }
        .report-title { text-align: center; margin-bottom: 20px; font-size: 1.2em; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h3>NAMA TOKO ANDA</h3>
        <p>Alamat Toko Anda, Kota, Kode Pos</p>
    </div>

    <div class="report-title">
        {{ $reportTitle }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Satuan</th>
                <th>Harga Jual</th>
                <th>Terjual</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPenjualanKeseluruhan = 0; @endphp
            @forelse($salesData as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $data->product_name }}</td>
                <td class="text-center">PCS</td>
                <td class="text-right">Rp {{ number_format($data->product_price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $data->total_quantity }}</td>
                <td class="text-right">Rp {{ number_format($data->total_sales, 0, ',', '.') }}</td>
            </tr>
            @php $totalPenjualanKeseluruhan += $data->total_sales; @endphp
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($salesData && $salesData->count() > 0)
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total Keseluruhan</th>
                <th class="text-right">Rp {{ number_format($totalPenjualanKeseluruhan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>