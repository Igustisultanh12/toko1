<table>
    <tr><td colspan="2" style="font-weight: bold;">{{ $shop['shop_name'] ?? 'TOKO' }}</td></tr>
    <tr><td colspan="2" style="font-weight: bold; text-decoration: underline;">ADMINISTRASI KASIR {{ $shop['app_name'] ?? 'POS' }}</td></tr>
    <tr><td></td></tr>
    <tr><td colspan="7" style="text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline;">LAPORAN TRANSAKSI DIGITAL QRIS (DOKU)</td></tr>
    <tr><td colspan="7" style="text-align: center; font-weight: bold;">Tarif MDR DOKU: 0.7% &nbsp;|&nbsp; Dicetak: {{ date('d/m/Y H:i') }} WIB</td></tr>
    <tr><td></td></tr>
    <thead>
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <th style="border: 1px solid black; text-align: center;">NO</th>
            <th style="border: 1px solid black; text-align: center;">NOMOR INVOICE</th>
            <th style="border: 1px solid black; text-align: center;">TANGGAL & WAKTU</th>
            <th style="border: 1px solid black;">NAMA PELANGGAN</th>
            <th style="border: 1px solid black; text-align: right;">NOMINAL BRUTO (RP)</th>
            <th style="border: 1px solid black; text-align: right;">BIAYA DOKU 0.7% (RP)</th>
            <th style="border: 1px solid black; text-align: right;">PENERIMAAN BERSIH (RP)</th>
            <th style="border: 1px solid black; text-align: center;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $calcGross = 0; 
            $calcFee = 0;
            $calcNet = 0;
        @endphp
        @foreach($transactions as $index => $trx)
        @php
            $gross = $trx->total_amount;
            $fee = round($gross * 0.007, 0);
            $net = $gross - $fee;
        @endphp
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->transaction_number }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $trx->created_at->format('d/m/Y - H:i') }} WIB</td>
            <td style="border: 1px solid black;">{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ $gross }}</td>
            <td style="border: 1px solid black; text-align: right;">-{{ $fee }}</td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">{{ $net }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ strtoupper($trx->payment_status) }}</td>
        </tr>
        @if($trx->payment_status == 'success')
            @php 
                $calcGross += $gross;
                $calcFee += $fee;
                $calcNet += $net;
            @endphp
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td colspan="4" style="border: 1px solid black; text-align: right;">TOTAL TRANSAKSI BRUTO:</td>
            <td style="border: 1px solid black; text-align: right;">{{ $calcGross }}</td>
            <td style="border: 1px solid black; text-align: right;">-{{ $calcFee }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ $calcNet }}</td>
            <td style="border: 1px solid black;"></td>
        </tr>
        <tr style="background-color: #cbd5e1; font-weight: bold;">
            <td colspan="6" style="border: 1px solid black; text-align: right;">TOTAL DANA BERSIH MASUK REKENING:</td>
            <td style="border: 1px solid black; text-align: right;">{{ $calcNet }}</td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tfoot>
</table>
