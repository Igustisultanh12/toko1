<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman Paket - {{ $order->order_number ?? ($sale->transaction_number ?? 'RESI') }}</title>
    <style>
        @page {
            size: a6 portrait;
            margin: 6mm 6mm 6mm 6mm;
        }
        body { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; 
            font-size: 8.5pt; 
            line-height: 1.25; 
            color: #111; 
            margin: 0;
            padding: 0;
        }
        .container {
            border: 2px solid #000;
            padding: 7px;
            box-sizing: border-box;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header table {
            width: 100%;
        }
        .shop-title {
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
        }
        .badge-shipping {
            background-color: #000;
            color: #fff;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 8pt;
            display: inline-block;
            text-transform: uppercase;
        }
        .section-box {
            border: 1.5px solid #000;
            margin-bottom: 5px;
            padding: 5px 6px;
        }
        .to-box {
            background-color: #fdfdfd;
            border: 2px solid #000;
            padding: 6px;
            margin-bottom: 5px;
        }
        .section-title {
            font-size: 7.5pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #444;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .recipient-name {
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
        }
        .recipient-phone {
            font-size: 9.5pt;
            font-weight: bold;
            color: #000;
            margin-top: 2px;
        }
        .recipient-address {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 3px;
            line-height: 1.25;
            color: #111;
        }
        .tag-label {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: 900;
            background-color: #e5e5e5;
            color: #000;
            padding: 1px 4px;
            border-radius: 2px;
            margin-right: 2px;
            text-transform: uppercase;
        }
        .courier-badge {
            background-color: #000;
            color: #fff;
            font-weight: 900;
            font-size: 8.5pt;
            padding: 2px 6px;
            display: inline-block;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .sender-name {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #333;
            padding: 3px 4px;
            font-size: 7.5pt;
        }
        table.items-table th {
            background-color: #eaeaea;
            text-align: left;
            text-transform: uppercase;
        }
        .notes-box {
            border: 1.5px dashed #000;
            padding: 4px 6px;
            font-size: 7.5pt;
            margin-top: 4px;
            background-color: #fffde6;
            font-weight: bold;
        }
        .footer-note {
            font-size: 6.5pt;
            text-align: center;
            margin-top: 4px;
            color: #555;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        
        {{-- HEADER LABEL --}}
        <div class="header">
            <table>
                <tr>
                    <td width="60%" valign="middle">
                        <span class="shop-title">{{ $shop['shop_name'] ?? ($sender['name'] ?? 'TOKO') }}</span><br>
                        <span style="font-size: 7.5pt; color: #333; font-weight: bold;">TELP: {{ $shop['shop_phone'] ?? ($sender['phone'] ?? '-') }}</span>
                    </td>
                    <td width="40%" align="right" valign="middle">
                        <span class="badge-shipping">LABEL PENGIRIMAN</span><br>
                        <span style="font-family: monospace; font-size: 8.5pt; font-weight: 900;">{{ $order->order_number ?? ($sale->transaction_number ?? ($trackingNumber ?? '-')) }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- KOTAK PENERIMA (TUJUAN) - BESAR & JELAS --}}
        <div class="to-box">
            <div class="section-title">KEPADA / PENERIMA (TO):</div>
            <div class="recipient-name">{{ $recipient['name'] ?? ($recipientName ?? ($order->customer_name ?? ($sale->customer_name ?? 'Pelanggan Umum'))) }}</div>
            <div class="recipient-phone">
                <span class="tag-label">NO. TELP / WA</span> {{ $recipient['phone'] ?? ($recipientPhone ?? ($order->customer_phone ?? ($sale->customer_phone ?? '-'))) }}
            </div>
            <div class="recipient-address">
                <span class="tag-label">ALAMAT</span> {{ $recipient['address'] ?? ($recipientAddress ?? ($order->customer_address ?? 'Alamat belum diisi / Ambil di Tempat')) }}
            </div>
            <div style="margin-top: 4px;">
                <span class="courier-badge">EKSPEDISI: {{ strtoupper($courier ?? ($order->courier ?? 'REGULER')) }}</span>
            </div>
        </div>

        {{-- KOTAK PENGIRIM (DARI) --}}
        <div class="section-box">
            <div class="section-title">DARI / PENGIRIM (FROM):</div>
            <div class="sender-name">{{ $sender['name'] ?? ($senderName ?? ($shop['shop_name'] ?? '')) }}</div>
            <div style="font-size: 8pt; font-weight: bold; margin-top: 1px;">
                <span class="tag-label">NO. TELP</span> {{ $sender['phone'] ?? ($senderPhone ?? ($shop['shop_phone'] ?? '-')) }}
            </div>
            <div style="font-size: 7.5pt; color: #222; margin-top: 2px;">
                <span class="tag-label">ALAMAT</span> {{ $sender['address'] ?? ($senderAddress ?? ($shop['shop_address'] ?? 'Jember, Jawa Timur')) }}
            </div>
        </div>

        {{-- RINCIAN ISI PAKET --}}
        <div>
            <div class="section-title" style="margin-top: 2px;">ISI PAKET (ITEMS):</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="8%" style="text-align: center;">NO</th>
                        <th width="72%">NAMA PRODUK</th>
                        <th width="20%" style="text-align: center;">QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalQty = 0; 
                        $itemList = $items ?? ($order->items ?? ($sale->details ?? []));
                    @endphp
                    @forelse($itemList as $idx => $item)
                    @php 
                        $qty = $item->quantity ?? 1;
                        $totalQty += $qty; 
                        $pName = $item->product_name ?? ($item->product->name ?? 'Produk');
                    @endphp
                    <tr>
                        <td align="center">{{ $idx + 1 }}</td>
                        <td>{{ $pName }}</td>
                        <td align="center"><b>{{ $qty }} pcs</b></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" align="center">Tidak ada rincian item</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" align="right" style="font-weight: bold; font-size: 7.5pt;">TOTAL KUANTITAS:</td>
                        <td align="center" style="font-weight: bold; font-size: 7.5pt;">{{ $totalQty }} pcs</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- CATATAN KHUSUS / INSTRUKSI PENGIRIMAN --}}
        <div class="notes-box">
            <b>[PERHATIAN]:</b> {{ $notes ?? ($order->customer_notes ?? 'FRAGILE - JANGAN DIBANTING / DITINDIH') }}
        </div>

        <div class="footer-note">
            TGL CETAK: {{ isset($order) ? $order->created_at->format('d/m/Y H:i') : (isset($sale) ? $sale->created_at->format('d/m/Y H:i') : date('d/m/Y H:i')) }} WIB | DICETAK OTOMATIS SISTEM {{ $shop['app_name'] ?? 'SIKANDA' }}
        </div>

    </div>
</body>
</html>
