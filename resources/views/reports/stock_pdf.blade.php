<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang - {{ $shop['app_name'] ?? 'SIKANDA' }}</title>
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
            margin-bottom: 25px; 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .underline-line {
            text-decoration: underline;
            display: inline-block;
        }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0; }
        .sub-judul { text-align: center; font-weight: bold; margin-top: 0; margin-bottom: 20px; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid black; padding: 6px; font-size: 8.5pt; vertical-align: top; }
        table.data th { background-color: #f2f2f2; text-transform: uppercase; }
        
        .total-row { background-color: #eeeeee; font-weight: bold; }
        
        .footer { float: right; width: 250px; text-align: center; margin-top: 30px; font-size: 9.5pt; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="kop">
        {{ $shop['shop_name'] ?? 'TOKO ANANDA' }}<br>
        <span class="underline-line">ADMINISTRASI INVENTARIS {{ $shop['app_name'] ?? 'SIKANDA' }}</span>
    </div>

    <p class="judul">LAPORAN REKAPITULASI STOK & INVENTARIS BARANG</p>
    <p class="sub-judul">Nomor: {{ $docNumber ?? ('LSTK-STOK / ' . date('d / m / Y') . ' / ' . ($shop['app_name'] ?? 'SIKANDA')) }} &nbsp;|&nbsp; Status Filter: {{ $statusLabel ?? 'Semua Stok' }} &nbsp;|&nbsp; Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>

    <p>1. &nbsp;&nbsp; Rekapitulasi data stok fisik, harga jual, dan estimasi valuasi aset inventaris produk toko adalah sebagai berikut:</p>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="15%">KODE BARCODE / SKU</th>
                <th width="33%">NAMA PRODUK / BARANG</th>
                <th width="12%">KETERANGAN</th>
                <th width="14%">HARGA JUAL</th>
                <th width="8%">STOK</th>
                <th width="14%">TOTAL VALUASI</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalStock = 0; 
                $grandTotalValuation = 0; 
            @endphp
            @forelse($products as $index => $product)
            @php
                $itemValuation = $product->stock * $product->price;
                $grandTotalStock += $product->stock;
                $grandTotalValuation += $itemValuation;
            @endphp
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td align="center" style="font-family: monospace;">{{ $product->barcode ?: 'SKU-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <b>{{ $product->name }}</b>
                </td>
                <td>{{ $product->description ?: '-' }}</td>
                <td align="right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td align="center" style="font-weight: bold; {{ $product->stock <= 0 ? 'color: red;' : ($product->stock <= 10 ? 'color: #d97706;' : '') }}">
                    {{ $product->stock }} pcs
                </td>
                <td align="right" style="font-weight: bold;">
                    Rp {{ number_format($itemValuation, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" align="center" style="padding: 20px; font-style: italic; color: #777;">
                    Tidak ada data produk yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" align="center">TOTAL KESELURUHAN ASET INVENTARIS ({{ $products->count() }} MACAM PRODUK)</td>
                <td align="center">{{ number_format($grandTotalStock, 0, ',', '.') }} pcs</td>
                <td align="right">Rp {{ number_format($grandTotalValuation, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p>2. &nbsp;&nbsp; Laporan ini dibuat secara otomatis oleh sistem {{ $shop['app_name'] ?? 'SIKANDA' }} untuk dipergunakan sebagai bahan audit dan verifikasi fisik inventaris gudang.</p>

    <div class="footer">
        Jember, {{ date('d F Y') }}<br>
        <b>{{ $signerTitle ?? $shop['cashier_officer_title'] ?? 'Petugas Kasir' }},</b><br>
        <div style="margin: 3px 0;">
            @if(!empty($tteQrBase64))
                <img src="{{ $tteQrBase64 }}" style="width: 70px; height: 70px; margin: 0 auto; display: block;">
            @else
                <br><br><br>
            @endif
        </div>
        <div style="font-size: 6.5pt; color: #555; margin-top: -1px; margin-bottom: 2px;">
            <i>Ditandatangani secara elektronik (TTE)</i>
        </div>
        <u><b>{{ $signerName ?? Auth::user()->name ?? 'Administrator' }}</b></u>
    </div>
</body>
</html>
