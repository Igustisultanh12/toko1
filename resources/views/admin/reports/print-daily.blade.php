<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <style>
        /* Style ini disesuaikan dari struk pembelian agar konsisten */
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
            max-width: 60%; 
            word-wrap: break-word;
        }
        .item-qty {
            text-align: center;
        }
        .item-total {
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

        <div class="separator"></div>

        <table>
            <thead>
                <tr>
                    <th class="item-name">Produk</th>
                    <th class="item-qty">Jml</th>
                    <th class="item-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaryItems as $item)
                <tr>
                    <td class="item-name">{{ $item['name'] }}</td>
                    <td class="item-qty">{{ $item['quantity'] }}</td>
                    <td class="item-total">{{ number_format($item['total_price'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

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
