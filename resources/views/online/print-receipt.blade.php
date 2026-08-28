<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan {{ $order->order_number }}</title>
    <style>
        @page { 
            margin: 0; 
        }
        body { 
            font-family: 'Courier', monospace; 
            font-size: 8pt; 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            background-color: #ffffff; 
            color: #000000; 
            line-height: 1.25;
        }
        .receipt-wrapper { 
            padding: 6pt 10pt; 
            box-sizing: border-box; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .separator { 
            border-top: 1px dashed #000000; 
            margin: 4pt 0; 
        }
        .separator-double { 
            border-top: 1px double #000000; 
            margin: 4pt 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 2pt 0; 
        }
        td { 
            vertical-align: top; 
            font-size: 7.5pt;
        }
        .header h3 { 
            margin: 0; 
            font-size: 10pt; 
            line-height: 1.2; 
        }
        .header p { 
            margin: 1pt 0; 
            font-size: 7pt; 
        }
        .badge-title {
            display: inline-block;
            border: 1px solid #000;
            padding: 2pt 6pt;
            font-size: 7pt;
            font-weight: bold;
            margin: 3pt 0;
            text-transform: uppercase;
        }
        .logo { 
            max-width: 50px; 
            max-height: 50px;
            height: auto; 
            margin: 0 auto 3px auto; 
            display: inline-block;
        }
        .item-name {
            font-size: 7.5pt;
            font-weight: bold;
        }
        .item-sub {
            font-size: 7pt;
            color: #222;
        }
        .qr-code {
            margin: 4pt auto;
            text-align: center;
        }
        .qr-code img {
            width: 70px;
            height: 70px;
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        {{-- 1. HEADER IDENTITAS TOKO --}}
        <div class="header text-center">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" class="logo">
            @endif
            
            <h3 class="uppercase bold">{{ $shop['shop_name'] ?? 'TOKO BERKAH' }}</h3>
            @if(!empty($shop['app_tagline']))
                <p>{{ $shop['app_tagline'] }}</p>
            @endif
            <p>{{ $shop['shop_address'] ?? 'Jember, Jawa Timur' }}</p>
            <p>Telp/WA: {{ $shop['shop_phone'] ?? ($shop['phone'] ?? '-') }}</p>
            
            <div class="badge-title">STRUK PESANAN ONLINE</div>
        </div>

        <div class="separator"></div>

        {{-- 2. METADATA TRANSAKSI & STATUS --}}
        <table>
            <tr>
                <td class="bold">NO. PESANAN</td>
                <td class="text-right bold">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td>TANGGAL</td>
                <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td>PEMBAYARAN</td>
                <td class="text-right bold">QRIS DOKU (LUNAS)</td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td class="text-right bold">{{ strtoupper($order->status_label) }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        {{-- 3. INFORMASI PENERIMA & PENGIRIMAN (LEBIH LENGKAP) --}}
        <table>
            <tr>
                <td colspan="2" class="bold uppercase" style="font-size: 7pt; padding-bottom: 2pt;">[ TUJUAN PENGIRIMAN ]</td>
            </tr>
            <tr>
                <td style="width: 28%;">PENERIMA</td>
                <td class="bold">: {{ $order->customer_name }}</td>
            </tr>
            <tr>
                <td>TELEPON/WA</td>
                <td>: {{ $order->customer_phone }}</td>
            </tr>
            <tr>
                <td>ALAMAT</td>
                <td>: {{ $order->customer_address }}</td>
            </tr>
            <tr>
                <td>EKSPEDISI</td>
                <td class="bold">: {{ strtoupper($order->courier) }}</td>
            </tr>
            @if(!empty($order->tracking_number))
            <tr>
                <td>NO. RESI</td>
                <td class="bold">: {{ $order->tracking_number }}</td>
            </tr>
            @endif
            @if(!empty($order->customer_notes))
            <tr>
                <td>CATATAN</td>
                <td>: {{ $order->customer_notes }}</td>
            </tr>
            @endif
        </table>

        <div class="separator"></div>

        {{-- 4. DAFTAR ITEM BARANG YANG DIBELI --}}
        <table>
            <tr>
                <td colspan="2" class="bold uppercase" style="font-size: 7pt; padding-bottom: 2pt;">[ RINCIAN BARANG ]</td>
            </tr>
            @foreach($order->items as $item)
            <tr>
                <td colspan="2" class="item-name uppercase">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td class="item-sub">
                    {{ $item->quantity }} pcs x {{ number_format($item->price, 0, ',', '.') }}
                </td>
                <td class="text-right item-sub bold">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="separator"></div>

        {{-- 5. TOTAL TAGIHAN --}}
        <table>
            <tr>
                <td>SUBTOTAL BARANG</td>
                <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>ONGKOS KIRIM</td>
                <td class="text-right bold">TERMASUK</td>
            </tr>
            <tr class="bold" style="font-size: 8.5pt;">
                <td style="padding-top: 3pt;">TOTAL BAYAR</td>
                <td class="text-right" style="padding-top: 3pt;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>METODE BAYAR</td>
                <td class="text-right bold">QRIS (LUNAS)</td>
            </tr>
        </table>

        <div class="separator-double"></div>

        {{-- 6. FOOTER & QR CODE LACAK PESANAN --}}
        <div class="header text-center" style="margin-top: 4pt;">
            <p class="bold uppercase">TERIMA KASIH TELAH BERBELANJA!</p>
            <p>{{ $shop['receipt_footer'] ?? 'Simpan struk ini sebagai bukti pembelian sah.' }}</p>
            
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('order.track', $order->order_number)) }}" alt="QR Lacak">
                <p style="font-size: 6pt; margin-top: 2pt;">Scan QR Code di atas untuk lacak paket</p>
                <p style="font-size: 6pt; font-family: monospace;">{{ route('order.track', $order->order_number) }}</p>
            </div>
            
            <p style="font-size: 6pt; color: #555; margin-top: 3pt;">Dicetak otomatis pada {{ now()->format('d/m/Y H:i:s') }} WIB</p>
        </div>
    </div>
</body>
</html>
