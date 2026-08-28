<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Courier', monospace; 
            font-size: 8pt; 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            background-color: #ffffff; 
            color: #000; 
        }
        .receipt-wrapper { padding: 5pt 8pt; box-sizing: border-box; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .separator { border-top: 1px dashed #000; margin: 5pt 0; }
        table { width: 100%; border-collapse: collapse; margin: 2pt 0; }
        td { vertical-align: top; }
        .header h3 { margin: 0; font-size: 10pt; line-height: 1.2; }
        .header p { margin: 1pt 0; font-size: 7pt; }
        .logo { 
            max-width: 55px; 
            max-height: 55px;
            height: auto; 
            margin: 0 auto 4px auto; 
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="header text-center">
            {{-- 1. LOGO TOKO (HITAM PUTIH KHUSUS THERMAL) --}}
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" class="logo">
            @endif
            
            {{-- 2. IDENTITAS TOKO --}}
            <h3 class="uppercase">{{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</h3>
            <p>{{ $shop['shop_address'] ?? 'Alamat belum diatur' }}</p>
            {{-- Pastikan key di database adalah shop_phone --}}
            <p>Telp: {{ $shop['shop_phone'] ?? ($shop['phone'] ?? '-') }}</p>
        </div>

        <div class="separator"></div>

        <table style="font-size: 7pt;">
            <tr>
                <td class="bold">No: {{ $sale->transaction_number }}</td>
                <td class="text-right">{{ $sale->created_at->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td colspan="2">Pelanggan: {{ $sale->customer_name ?? 'Pelanggan Umum' }}</td>
            </tr>
            <tr>
                <td colspan="2">Kasir: {{ $sale->user->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td colspan="2">Metode: {{ strtoupper($sale->payment_method) }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        {{-- 3. DAFTAR ITEM --}}
        <table>
            @foreach($sale->details as $detail)
            <tr>
                <td colspan="2" class="item-name bold uppercase">{{ $detail->product->name }}</td>
            </tr>
            <tr>
                <td class="item-price">
                    {{ $detail->quantity }} x {{ number_format($detail->price_at_transaction, 0, ',', '.') }}
                </td>
                <td class="text-right item-price">
                    {{ number_format($detail->quantity * $detail->price_at_transaction, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="separator"></div>

        {{-- 4. TOTAL PEMBAYARAN --}}
        <table class="bold">
            <tr>
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: normal;">
                <td>BAYAR</td>
                <td class="text-right">{{ number_format($sale->amount_paid, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: normal;">
                <td>KEMBALI</td>
                <td class="text-right">{{ number_format($sale->amount_paid - $sale->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        <div class="footer text-center">
            <p class="bold uppercase">Terima Kasih!</p>
            <p>{{ $shop['receipt_footer'] ?? 'Barang yang sudah dibeli tidak dapat ditukar.' }}</p>
        </div>
    </div>
</body>
</html>