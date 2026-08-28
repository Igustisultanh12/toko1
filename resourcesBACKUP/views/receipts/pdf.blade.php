<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $sale->id }}</title>
    <style>
        * { font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #000; }
        body { margin: 0; padding: 0; }
        .container { width: 100%; max-width: 210px; /* Lebar struk 80mm */ margin: 0 auto; }
        .header, .footer { text-align: center; }
        .shop-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .info-table, .items-table, .totals-table { width: 100%; }
        .info-table td, .totals-table td { padding: 1px 0; }
        .items-table .item-name { text-align: left; }
        .items-table .item-total { text-align: right; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .totals-table .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="shop-name">TOKO ANANDA</div>
            <div>JL.ARGOPURO NO.77 MAYANG</div>
            <div>JEMBER, 68182</div>
            <div>TELP: 000000000000000000</div>
        </div>

        <div class="line"></div>

        <table class="info-table">
            <tr>
                <td>Tgl</td>
                <td>: {{ $sale->created_at->format('d/m/y H:i:s') }}</td>
            </tr>
            <tr>
                <td>No. Struk</td>
                <td>: {{ $sale->id }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $sale->user->name ?? 'Kasir' }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <table class="items-table">
            <tbody>
                @foreach($sale->details as $detail)
                <tr>
                    <td colspan="2" class="item-name">{{ strtoupper($detail->product->name) }}</td>
                </tr>
                <tr>
                    <td class="text-left">
                        {{ $detail->quantity }}x {{ number_format($detail->price_at_transaction, 0, ',', '.') }}
                    </td>
                    <td class="item-total">
                        {{ number_format($detail->quantity * $detail->price_at_transaction, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <table class="totals-table">
            <tr>
                <td class="label">TOTAL</td>
                <td class="text-right">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">TUNAI</td>
                <td class="text-right">Rp {{ number_format($sale->amount_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">KEMBALI</td>
                <td class="text-right">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="footer">
            <p>TERIMA KASIH</p>
            <p>Barang yang sudah dibeli</p>
            <p>tidak dapat ditukar/dikembalikan</p>
        </div>
    </div>
</body>
</html>
