<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $shop['app_name'] ?? 'SIKANDA' }}</title>
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

    @php
        $method = strtolower($filters['payment_method'] ?? 'all');
        if ($method === 'cash') {
            $reportTitle = 'LAPORAN REKAPITULASI PENERIMAAN KAS TUNAI (CASH)';
            $docPrefix = 'LTNI';
        } elseif ($method === 'qris') {
            $reportTitle = 'LAPORAN REKAPITULASI PENERIMAAN DIGITAL QRIS (DOKU)';
            $docPrefix = 'LQRS';
        } else {
            $reportTitle = 'LAPORAN REKAPITULASI KEUANGAN & ARUS KAS';
            $docPrefix = 'LKEU';
        }
    @endphp

    <p class="judul">{{ $reportTitle }}</p>
    <p class="sub-judul">Nomor: {{ $docNumber ?? ($docPrefix . ' / ' . date('m / Y') . ' / ' . ($shop['app_name'] ?? 'SIKANDA')) }} &nbsp;|&nbsp; Periode: {{ $periodLabel ?? 'Semua' }} &nbsp;|&nbsp; Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>

    <p>1. &nbsp;&nbsp; Rekapitulasi transaksi penerimaan kas dan keuangan pada periode {{ $periodLabel ?? '' }} dengan rincian sebagai berikut:</p>

    <table class="data">
        <thead>
            @if($method === 'qris')
            <tr>
                <th width="4%">NO</th>
                <th width="16%">NOMOR INVOICE</th>
                <th width="20%">NAMA PELANGGAN</th>
                <th width="16%">TANGGAL & WAKTU</th>
                <th width="14%">NOMINAL BRUTO</th>
                <th width="14%">BIAYA DOKU (0.7%)</th>
                <th width="16%">PENERIMAAN BERSIH</th>
            </tr>
            @else
            <tr>
                <th width="4%">NO</th>
                <th width="18%">NOMOR INVOICE</th>
                <th width="20%">NAMA PELANGGAN</th>
                <th width="18%">TANGGAL & WAKTU</th>
                <th width="18%">KANAL BAYAR</th>
                <th width="22%">NOMINAL MASUK (NETTO)</th>
            </tr>
            @endif
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
                <td align="center">{{ $index + 1 }}</td>
                <td align="center" style="font-family: monospace;">{{ $trx->transaction_number }}</td>
                <td>{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                <td align="center">{{ $trx->created_at->format('d/m/Y - H:i') }} WIB</td>
                @if($method === 'qris')
                    <td align="right">Rp {{ number_format($gross, 0, ',', '.') }}</td>
                    <td align="right" style="color: #c00;">- Rp {{ number_format($fee, 0, ',', '.') }}</td>
                    <td align="right" style="font-weight: bold; color: #007710;">Rp {{ number_format($net, 0, ',', '.') }}</td>
                @else
                    <td align="center">
                        @if($isQris)
                            QRIS DOKU <small style="color:#777;">(Net)</small>
                        @else
                            KAS TUNAI
                        @endif
                    </td>
                    <td align="right">
                        @if($isQris)
                            Rp {{ number_format($net, 0, ',', '.') }}
                            <br><small style="color: #777; font-size: 7.5pt;">(Bruto: {{ number_format($gross, 0, ',', '.') }} - DOKU 0.7%: {{ number_format($fee, 0, ',', '.') }})</small>
                        @else
                            Rp {{ number_format($net, 0, ',', '.') }}
                        @endif
                    </td>
                @endif
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
                <td colspan="{{ $method === 'qris' ? 7 : 6 }}" align="center" style="padding: 20px; font-style: italic; color: #777;">
                    Tidak ada transaksi keuangan pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @if($method === 'cash')
                <tr class="total-row" style="background-color: #dddddd;">
                    <td colspan="4" align="right">TOTAL PENERIMAAN KAS TUNAI ({{ $transactions->where('payment_status', 'success')->count() }} TRANSAKSI):</td>
                    <td colspan="2" align="right">Rp {{ number_format($calcCash, 0, ',', '.') }}</td>
                </tr>
            @elseif($method === 'qris')
                <tr class="total-row">
                    <td colspan="4" align="right">TOTAL TRANSAKSI BRUTO ({{ $transactions->where('payment_status', 'success')->count() }} TRANSAKSI):</td>
                    <td align="right">Rp {{ number_format($calcQrisGross, 0, ',', '.') }}</td>
                    <td align="right" style="color: #c00;">- Rp {{ number_format($calcQrisFee, 0, ',', '.') }}</td>
                    <td align="right" style="color: #007710;">Rp {{ number_format($calcQrisNet, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row" style="background-color: #dddddd;">
                    <td colspan="6" align="right"><b>TOTAL DANA BERSIH MASUK REKENING:</b></td>
                    <td align="right" style="font-size: 9pt; font-weight: bold; color: #007710;">Rp {{ number_format($calcQrisNet, 0, ',', '.') }}</td>
                </tr>
            @else
                <tr class="total-row">
                    <td colspan="4" align="right">TOTAL PENERIMAAN KAS TUNAI:</td>
                    <td colspan="2" align="right">Rp {{ number_format($calcCash, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" align="right">TOTAL PENERIMAAN QRIS BERSIH (SETELAH POTONGAN DOKU 0.7%):</td>
                    <td colspan="2" align="right">Rp {{ number_format($calcQrisNet, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row" style="background-color: #dddddd;">
                    <td colspan="4" align="right"><b>TOTAL PEMASUKAN BERSIH TOKO (KAS & REKENING):</b></td>
                    <td colspan="2" align="right" style="font-size: 9pt; font-weight: bold;">Rp {{ number_format($calcTotalNet, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tfoot>
    </table>

    <p>2. &nbsp;&nbsp; Laporan ini dibuat secara otomatis oleh sistem {{ $shop['app_name'] ?? 'SIKANDA' }} untuk dipergunakan sebagai dokumen audit arus kas dan rekonsiliasi keuangan.</p>

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
