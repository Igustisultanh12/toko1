<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3, .header p { margin: 0; }
        .report-title { text-align: center; margin-bottom: 5px; font-size: 1.2em; font-weight: bold; text-transform: uppercase; }
        .print-time { text-align: center; margin-bottom: 20px; font-size: 0.9em; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>{{ $shop['shop_name'] ?? 'TOKO' }}</h3>
        <p>Jalan Argopuro No.77 Mayang</p>
        <p>Jember, 68182</p>
    </div>

    <div class="report-title">
        {{ $reportTitle }}
    </div>
    
    {{-- Waktu Cetak Ditambahkan di Sini --}}
    <div class="print-time">
        Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
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
                <td>{{ $data->product_name }}</td>
                <td class="text-right">Rp {{ number_format($data->product_price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $data->total_quantity }}</td>
                <td class="text-right">Rp {{ number_format($data->total_sales, 0, ',', '.') }}</td>
            </tr>
            @php $totalPenjualanKeseluruhan += $data->total_sales; @endphp
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($salesData && $salesData->count() > 0)
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Keseluruhan</th>
                <th class="text-right">Rp {{ number_format($totalPenjualanKeseluruhan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Copyright Ditambahkan di Sini --}}
    <div class="footer">
        <p>&copy; {{ date('Y') }} Nama Toko Anda. Semua hak dilindungi undang-undang.</p>
    </div>
</body>
</html>
