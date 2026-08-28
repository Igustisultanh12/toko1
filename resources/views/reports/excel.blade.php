<table>
    <tr><td colspan="2" style="font-weight: bold;">{{ $shop['shop_name'] ?? 'TOKO' }}</td></tr>
    <tr><td colspan="2" style="font-weight: bold; text-decoration: underline;">ADMINISTRASI KASIR {{ $shop['app_name'] ?? 'POS' }}</td></tr>
    <tr><td></td></tr>
    <tr><td colspan="9" style="text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline;">LAPORAN PENJUALAN TOKO</td></tr>
    <tr><td colspan="9" style="text-align: center; font-weight: bold;">Periode: {{ $periodLabel }} &nbsp;|&nbsp; Dicetak: {{ date('d/m/Y H:i') }} WIB</td></tr>
    <tr><td></td></tr>
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">NO</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">NOMOR INVOICE</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">TANGGAL & WAKTU</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: left;">NAMA PELANGGAN</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: left;">RINCIAN BARANG TERJUAL</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">TOTAL QTY</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">METODE</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: right;">TOTAL NOMINAL</th>
            <th style="border: 1px solid black; background-color: #e2e8f0; font-weight: bold; text-align: center;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $index => $trx)
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->transaction_number }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->created_at->format('d/m/Y H:i') }} WIB</td>
            <td style="border: 1px solid black; text-align: left;">{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
            <td style="border: 1px solid black; text-align: left;">
                @if($trx->details && $trx->details->count() > 0)
                    @foreach($trx->details as $item)
                        {{ $item->product->name ?? 'Produk Dihapus' }} ({{ $item->quantity }} pcs @ {{ $item->price_at_transaction }}){{ !$loop->last ? '; ' : '' }}
                    @endforeach
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $trx->details ? $trx->details->sum('quantity') : 0 }}
            </td>
            <td style="border: 1px solid black; text-align: center;">{{ strtoupper($trx->payment_method) }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ $trx->total_amount }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if($trx->payment_status == 'success')
                    SUKSES
                @elseif($trx->payment_status == 'pending')
                    PENDING
                @else
                    GAGAL
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="border: 1px solid black; text-align: center;">Tidak ada data penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">TOTAL KESELURUHAN (SUKSES):</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; background-color: #cbd5e1;">{{ $totalQty }}</td>
            <td style="border: 1px solid black; background-color: #cbd5e1;"></td>
            <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">{{ $totalNominal }}</td>
            <td style="border: 1px solid black; background-color: #cbd5e1;"></td>
        </tr>
    </tfoot>
</table>
