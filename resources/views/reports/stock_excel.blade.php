<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 14pt; font-weight: bold; text-align: center;">LAPORAN REKAPITULASI STOK & INVENTARIS BARANG</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Filter: {{ $statusLabel ?? 'Semua' }} | Tanggal Unduh: {{ date('d F Y, H:i') }} WIB</th>
        </tr>
        <tr></tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th style="border: 1px solid #000; text-align: center;">NO</th>
            <th style="border: 1px solid #000; text-align: center;">KODE BARCODE / SKU</th>
            <th style="border: 1px solid #000;">NAMA PRODUK</th>
            <th style="border: 1px solid #000;">KETERANGAN</th>
            <th style="border: 1px solid #000; text-align: right;">HARGA JUAL (RP)</th>
            <th style="border: 1px solid #000; text-align: center;">STOK (PCS)</th>
            <th style="border: 1px solid #000; text-align: right;">TOTAL VALUASI (RP)</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $grandStock = 0; 
            $grandValuation = 0; 
        @endphp
        @foreach($products as $index => $product)
        @php
            $val = $product->stock * $product->price;
            $grandStock += $product->stock;
            $grandValuation += $val;
        @endphp
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center;">'{{ $product->barcode ?: 'SKU-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td style="border: 1px solid #000;">{{ $product->name }}</td>
            <td style="border: 1px solid #000;">{{ $product->description ?: '-' }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $product->price }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $product->stock }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $val }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #eeeeee; font-weight: bold;">
            <td colspan="5" style="border: 1px solid #000; text-align: center;">TOTAL KESELURUHAN ({{ $products->count() }} PRODUK)</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $grandStock }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $grandValuation }}</td>
        </tr>
    </tfoot>
</table>
