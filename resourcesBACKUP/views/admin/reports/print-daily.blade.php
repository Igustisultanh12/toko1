<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <style>
        body {
            font-family: 'Consolas', 'monospace', 'Courier New', Courier;
            font-size: 11pt;
            color: #000;
            width: 58mm;
            margin: 0;
            padding: 5px;
            box-sizing: border-box;
            font-weight: normal;
        }
        .container {
            width: 100%;
            padding: 0;
        }
        .header, .footer {
            text-align: center;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .double-separator {
            border-top: 2px double #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        td, th {
            padding: 2px 0;
        }
        .item-name {
            text-align: left;
        }
        .item-qty {
            text-align: center;
        }
        .item-price, .item-subtotal {
            text-align: right;
        }
        .summary-table {
            width: 100%;
            margin-top: 8px;
        }
        .summary-table td {
            font-size: 12pt;
            padding: 2px 0;
            font-weight: bold;
        }
        .summary-label {
            text-align: left;
        }
        .summary-value {
            text-align: right;
        }
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>LAPORAN HARIAN</h3>
            <p>{{ $reportDate }}</p>
        </div>

        <div class="double-separator"></div>

        <!-- PERBAIKAN: Looping untuk setiap transaksi -->
        @forelse($sales as $sale)
            <div>
                <p style="margin: 2px 0; font-size: 9pt;">
                    No Struk: {{ $sale->id }} <br>
                    Kasir: {{ $sale->user->name ?? 'N/A' }} <br>
                    Waktu: {{ $sale->created_at->format('H:i') }}
                </p>
            </div>

            <div class="separator"></div>

            <table>
                <tbody>
                    @foreach($sale->saleDetails as $detail)
                    <tr>
                        <td colspan="4" class="item-name">{{ $detail->product->name }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="item-qty">{{ $detail->quantity }} x</td>
                        <td class="item-price">{{ number_format($detail->price_at_transaction, 0, ',', '.') }}</td>
                        <td class="item-subtotal">{{ number_format($detail->quantity * $detail->price_at_transaction, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="separator"></div>
        @empty
            <p style="text-align: center; font-size: 10pt;">Tidak ada transaksi hari ini.</p>
        @endforelse

        <div class="double-separator"></div>

        <table class="summary-table">
            <tr>
                <td class="summary-label">TOTAL PENJUALAN</td>
                <td class="summary-value">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div class="separator"></div>

        <div class="footer">
            <p style="font-size: 8pt;">Dicetak oleh: {{ Auth::user()->name }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
