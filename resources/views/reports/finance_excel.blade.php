<table>
    <tr><td colspan="2" style="font-weight: bold;">{{ $shop['shop_name'] ?? 'TOKO' }}</td></tr>
    <tr><td colspan="2" style="font-weight: bold; text-decoration: underline;">ADMINISTRASI KASIR {{ $shop['app_name'] ?? 'POS' }}</td></tr>
    <tr><td></td></tr>
    <tr><td colspan="7" style="text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline;">LAPORAN KEUANGAN & ARUS KAS</td></tr>
    <tr><td colspan="7" style="text-align: center; font-weight: bold;">Periode: {{ $periodLabel }} &nbsp;|&nbsp; Tarif MDR DOKU: 0.7% &nbsp;|&nbsp; Dicetak: {{ date('d/m/Y H:i') }} WIB</td></tr>
    <tr><td></td></tr>
    <thead>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <th style="border: 1px solid black; text-align: center;">NO</th>
            <th style="border: 1px solid black; text-align: center;">NOMOR INVOICE</th>
            <th style="border: 1px solid black; text-align: left;">NAMA PELANGGAN</th>
            <th style="border: 1px solid black; text-align: center;">TANGGAL & WAKTU</th>
            <th style="border: 1px solid black; text-align: center;">KANAL BAYAR</th>
            <th style="border: 1px solid black; text-align: right;">NOMINAL BRUTO (RP)</th>
            <th style="border: 1px solid black; text-align: right;">BIAYA DOKU 0.7% (RP)</th>
            <th style="border: 1px solid black; text-align: right;">NOMINAL BERSIH (RP)</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $calcCash = 0; 
            $calcQrisGross = 0;
            $calcQrisFee = 0;
            $calcQrisNet = 0;
            $calcTotalNet = 0;
        @endphp
        @forelse($transactions as $index => $trx)
        @php
            $isQris = strtolower($trx->payment_method) === 'qris';
            $gross = $trx->total_amount;
            $fee = $isQris ? round($gross * 0.007, 0) : 0;
            $net = $gross - $fee;
        @endphp
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->transaction_number }}</td>
            <td style="border: 1px solid black; text-align: left;">{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->created_at->format('d/m/Y H:i') }} WIB</td>
            <td style="border: 1px solid black; text-align: center;">{{ $isQris ? 'QRIS DOKU' : 'KAS TUNAI' }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ $gross }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ $fee > 0 ? '-' . $fee : '0' }}</td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">{{ $net }}</td>
        </tr>
        @if($trx->payment_status == 'success')
            @php 
                $calcTotalNet += $net;
                if($isQris) {
                    $calcQrisGross += $gross;
                    $calcQrisFee += $fee;
                    $calcQrisNet += $net;
                } else {
                    $calcCash += $gross;
                }
            @endphp
        @endif
        @empty
        <tr>
            <td colspan="8" style="border: 1px solid black; text-align: center;">Tidak ada transaksi keuangan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">TOTAL PENERIMAAN KAS TUNAI (100%):</td>
            <td colspan="3" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">{{ $calcCash }}</td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">TOTAL TRANSAKSI QRIS BRUTO:</td>
            <td colspan="3" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">{{ $calcQrisGross }}</td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">TOTAL BIAYA MDR DOKU (0.7%):</td>
            <td colspan="3" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1; color: red;">-{{ $calcQrisFee }}</td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">TOTAL PENERIMAAN QRIS BERSIH:</td>
            <td colspan="3" style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #cbd5e1;">{{ $calcQrisNet }}</td>
        </tr>
        <tr style="background-color: #94a3b8; font-weight: bold;">
            <td colspan="5" style="border: 1px solid black; text-align: right;">TOTAL PEMASUKAN BERSIH TOKO (KAS & REKENING):</td>
            <td colspan="3" style="border: 1px solid black; text-align: right;">{{ $calcTotalNet }}</td>
        </tr>
    </tfoot>
</table>
