<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $sale->transaction_number }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 15mm 20mm;
        }
        body { 
            font-family: "Arial", sans-serif; 
            font-size: 10pt; 
            line-height: 1.3; 
            color: black; 
        }
        .kop { 
            width: 50%; 
            margin-bottom: 20px; 
            font-weight: bold; 
            text-transform: uppercase;
        }

        .underline-line {
            text-decoration: underline;
            display: inline-block;
        }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0; }
        .sub-judul { text-align: center; font-weight: bold; margin-top: 0; margin-bottom: 18px; }
        
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 2px 4px;
        }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid black; padding: 6px 8px; font-size: 9pt; vertical-align: top; }
        table.data th { background-color: #f2f2f2; text-transform: uppercase; }
        
        .total-row { background-color: #eeeeee; font-weight: bold; }
        
        .footer { float: right; width: 250px; text-align: center; margin-top: 30px; font-size: 9.5pt; }
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kop">
        {{ $shop['shop_name'] ?? 'TOKO ANANDA' }}<br>
        <span class="underline-line">{{ $shop['app_name'] ?? 'ADMINISTRASI KASIR SIKANDA' }}</span>
    </div>

    <p class="judul">FAKTUR / NOTA TRANSAKSI PENJUALAN</p>
    <p class="sub-judul">Nomor: {{ $sale->transaction_number }} &nbsp;|&nbsp; Tanggal: {{ $sale->created_at->format('d/m/Y H:i') }} WIB</p>

    <table class="info-table">
        <tr>
            <td width="18%"><b>Nama Pelanggan</b></td>
            <td width="2%">:</td>
            <td width="30%">{{ $sale->customer_name ?? 'Pelanggan Umum' }}</td>
            <td width="18%"><b>Metode Pembayaran</b></td>
            <td width="2%">:</td>
            <td width="30%">{{ strtoupper($sale->payment_method) }} ({{ $sale->payment_status == 'success' ? 'LUNAS' : strtoupper($sale->payment_status) }})</td>
        </tr>
        <tr>
            <td><b>{{ $signerTitle ?? $shop['cashier_officer_title'] ?? 'Petugas Kasir' }}</b></td>
            <td>:</td>
            <td>{{ $signerName ?? $sale->user->name ?? 'Kasir' }}</td>
            <td><b>Waktu Transaksi</b></td>
            <td>:</td>
            <td>{{ $sale->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <p>1. &nbsp;&nbsp; Rincian barang belanjaan pada transaksi faktur ini adalah sebagai berikut:</p>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="40%">NAMA PRODUK / BARANG</th>
                <th width="15%">HARGA SATUAN</th>
                <th width="12%">DISKON</th>
                <th width="10%">QTY</th>
                <th width="18%">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $totalItemsQty = 0; @endphp
            @forelse($sale->details as $index => $item)
            @php 
                $subtotal = ($item->price_at_transaction - ($item->discount_at_transaction ?? 0)) * $item->quantity;
                $totalItemsQty += $item->quantity;
            @endphp
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? 'Produk' }}</td>
                <td align="right">Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }}</td>
                <td align="center">{{ $item->discount_at_transaction > 0 ? 'Rp ' . number_format($item->discount_at_transaction, 0, ',', '.') : '-' }}</td>
                <td align="center">{{ $item->quantity }}</td>
                <td align="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" align="center">Tidak ada rincian item.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" align="right">TOTAL TAGIHAN BELANJA:</td>
                <td align="center">{{ $totalItemsQty }}</td>
                <td align="right">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" align="right">JUMLAH DIBAYARKAN:</td>
                <td colspan="2" align="right">Rp {{ number_format($sale->amount_paid, 0, ',', '.') }}</td>
            </tr>
            @if($sale->payment_method === 'cash')
            <tr class="total-row">
                <td colspan="4" align="right">KEMBALIAN:</td>
                <td colspan="2" align="right">Rp {{ number_format(max(0, $sale->amount_paid - $sale->total_amount), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    <p>2. &nbsp;&nbsp; Dokumen faktur ini diterbitkan secara otomatis oleh sistem {{ $shop['app_name'] ?? 'SIKANDA' }} sebagai bukti transaksi yang sah.</p>

    <div class="footer">
        Jember, {{ $sale->created_at->translatedFormat('d F Y') }}<br>
        <b>{{ $signerTitle ?? $shop['cashier_officer_title'] ?? 'Petugas Kasir' }},</b><br>
        <div style="margin: 3px 0;">
            @if(!empty($tteQrBase64))
                <img src="{{ $tteQrBase64 }}" style="width: 75px; height: 75px; margin: 0 auto; display: block;">
            @else
                <br><br><br>
            @endif
        </div>
        <div style="font-size: 6.5pt; color: #555; margin-top: -1px; margin-bottom: 2px;">
            <i>Ditandatangani secara elektronik (TTE)</i>
        </div>
        <u><b>{{ $signerName ?? $sale->user->name ?? $shop['cashier_officer_name'] ?? 'Petugas Kasir' }}</b></u>
    </div>
</body>
</html>
