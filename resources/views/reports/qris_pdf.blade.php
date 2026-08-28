<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi QRIS DOKU - {{ $shop['app_name'] ?? 'SIKANDA' }}</title>
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
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kop">
        {{ $shop['shop_name'] ?? 'TOKO ANANDA' }}<br>
        <span class="underline-line">ADMINISTRASI KASIR {{ $shop['app_name'] ?? 'SIKANDA' }}</span>
    </div>

    <p class="judul">LAPORAN REKAPITULASI PENERIMAAN DIGITAL QRIS (DOKU)</p>
    <p class="sub-judul">Nomor: {{ $docNumber ?? ('LKEU-QRIS / ' . date('d / m / Y') . ' / ' . ($shop['app_name'] ?? 'SIKANDA')) }} &nbsp;|&nbsp; Tarif MDR DOKU: 0.7% &nbsp;|&nbsp; Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>

    <p>1. &nbsp;&nbsp; Rekapitulasi transaksi pembayaran digital via QRIS DOKU dengan rincian pemotongan MDR 0.7% per transaksi adalah sebagai berikut:</p>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="16%">NOMOR INVOICE</th>
                <th width="20%">NAMA PELANGGAN</th>
                <th width="16%">TANGGAL & WAKTU</th>
                <th width="14%">NOMINAL BRUTO</th>
                <th width="14%">BIAYA DOKU (0.7%)</th>
                <th width="16%">PENERIMAAN BERSIH</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $calcGross = 0; 
                $calcFee = 0; 
                $calcNet = 0; 
            @endphp
            @forelse($transactions as $index => $trx)
            @php
                $gross = $trx->total_amount;
                $fee = round($gross * 0.007, 0);
                $net = $gross - $fee;
            @endphp
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td align="center" style="font-family: monospace;">{{ $trx->transaction_number }}</td>
                <td>{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                <td align="center">{{ $trx->created_at->format('d/m/Y - H:i') }} WIB</td>
                <td align="right">Rp {{ number_format($gross, 0, ',', '.') }}</td>
                <td align="right" style="color: #c00;">- Rp {{ number_format($fee, 0, ',', '.') }}</td>
                <td align="right" style="font-weight: bold; color: #007710;">Rp {{ number_format($net, 0, ',', '.') }}</td>
            </tr>
            @if($trx->payment_status == 'success')
                @php 
                    $calcGross += $gross;
                    $calcFee += $fee;
                    $calcNet += $net;
                @endphp
            @endif
            @empty
            <tr>
                <td colspan="7" align="center" style="padding: 20px; font-style: italic; color: #777;">
                    Tidak ada transaksi QRIS pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" align="right">TOTAL TRANSAKSI BRUTO ({{ $transactions->where('payment_status', 'success')->count() }} TRANSAKSI):</td>
                <td align="right">Rp {{ number_format($calcGross, 0, ',', '.') }}</td>
                <td align="right" style="color: #c00;">- Rp {{ number_format($calcFee, 0, ',', '.') }}</td>
                <td align="right" style="color: #007710;">Rp {{ number_format($calcNet, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background-color: #dddddd;">
                <td colspan="6" align="right"><b>TOTAL DANA BERSIH MASUK REKENING TOKO:</b></td>
                <td align="right" style="font-size: 9pt; font-weight: bold; color: #007710;">Rp {{ number_format($calcNet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p>2. &nbsp;&nbsp; Seluruh transaksi QRIS secara otomatis dipotong biaya MDR gateway sebesar 0.7% sesuai ketentuan DOKU Merchant.</p>

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
