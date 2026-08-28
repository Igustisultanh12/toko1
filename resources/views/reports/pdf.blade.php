<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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

        /* Span khusus untuk memberikan underline hanya pada baris kedua */
        .underline-line {
            text-decoration: underline;
            display: inline-block;
        }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0; }
        .sub-judul { text-align: center; font-weight: bold; margin-top: 0; margin-bottom: 20px; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid black; padding: 6px; font-size: 8.5pt; vertical-align: top; }
        table.data th { background-color: #f2f2f2; text-transform: uppercase; }
        
        /* Gaya untuk baris total */
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

    <p class="judul">LAPORAN REKAPITULASI PENJUALAN</p>
    <p class="sub-judul">Nomor: {{ $docNumber ?? ('LPK-JUAL / ' . date('d / m / Y') . ' / ' . ($shop['app_name'] ?? 'SIKANDA')) }} &nbsp;|&nbsp; Periode: {{ $periodLabel ?? 'Semua' }} &nbsp;|&nbsp; Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>

    <p>1. &nbsp;&nbsp; Laporan transaksi penjualan toko pada periode {{ $periodLabel ?? '' }} dengan rincian sebagai berikut:</p>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="16%">NOMOR INVOICE</th>
                <th width="15%">NAMA PELANGGAN</th>
                <th width="32%">RINCIAN BARANG TERJUAL</th>
                <th width="6%">QTY</th>
                <th width="13%">NOMINAL</th>
                <th width="14%">TANGGAL & WAKTU</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNominal = 0; $totalQtySemua = 0; @endphp
            @foreach($transactions as $index => $trx)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td align="center">{{ $trx->transaction_number }}</td>
                <td>{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                <td>
                    @if($trx->details && $trx->details->count() > 0)
                        @foreach($trx->details as $item)
                            &bull; {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }} pcs &times; Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }})<br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td align="center">{{ $trx->details ? $trx->details->sum('quantity') : 0 }}</td>
                <td align="right">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                <td align="center">{{ $trx->created_at->format('d/m/Y - H:i') }} WIB</td>
            </tr>
            @if($trx->payment_status == 'success')
                @php 
                    $totalNominal += $trx->total_amount; 
                    $totalQtySemua += ($trx->details ? $trx->details->sum('quantity') : 0);
                @endphp
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" align="center">TOTAL PEMASUKAN (SUKSES)</td>
                <td align="center">{{ $totalQtySemua }}</td>
                <td align="right">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td style="background-color: white; border: none;"></td>
            </tr>
        </tfoot>
    </table>

    <p>2. &nbsp;&nbsp; Laporan ini dibuat secara otomatis oleh sistem {{ $shop['app_name'] ?? 'SIKANDA' }} untuk dipergunakan sebagaimana mestinya.</p>

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
