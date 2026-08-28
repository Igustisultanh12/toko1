<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $sale->id }}</title>
    <style>
        /* PERBAIKAN: Menggunakan font Consolas dan ukuran yang lebih optimal */
        body {
            font-family: 'Consolas', 'monospace', 'Courier New', Courier;
            font-size: 11pt; /* Sedikit diperbesar agar lebih mudah dibaca */
            color: #000;
            width: 58mm; /* Lebar kertas printer thermal */
            margin: 0;
            padding: 5px;
            box-sizing: border-box;
            font-weight: normal; /* Menggunakan ketebalan normal agar tidak buram */
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
            font-size: 13pt; /* Disesuaikan */
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
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
            margin-top: 5px;
        }
        .summary-table td {
            font-size: 11pt;
            padding: 1px 0;
            font-weight: bold;
        }
        .summary-label {
            text-align: left;
        }
        .summary-value {
            text-align: right;
        }
        /* Mengatur style saat di-print */
        @media print {
            @page {
                size: 58mm auto; /* Mengatur ukuran kertas saat dialog print muncul */
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
            <h3>TOKO ANANDA</h3>
            <p>Jalan Argopuro No.77 Mayang</p>
            <p>Jember, 68182</p>
            <p>Telp:00000000000000</p>
        </div>

        <div class="separator"></div>

        <div>
            <p style="margin: 2px 0; font-size: 9pt;">
                No: {{ $sale->id }} <br>
                Kasir: {{ $sale->user->name ?? 'N/A' }} <br>
                Tanggal: {{ $sale->created_at->format('d/m/Y H:i') }}
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

        <table class="summary-table">
            <tr>
                <td class="summary-label">Total</td>
                <td class="summary-value">{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            @if($sale->payment_method === 'cash')
            <tr>
                <td class="summary-label">Bayar</td>
                <td class="summary-value">{{ number_format($sale->amount_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Kembali</td>
                <td class="summary-value">{{ number_format($sale->amount_paid - $sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
        
        <div class="separator"></div>

        <div class="footer">
            <p style="font-size: 9pt;">Terima Kasih!</p>
            <p style="font-size: 8pt;">Barang yang dibeli tidak dapat dikembalikan.</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>