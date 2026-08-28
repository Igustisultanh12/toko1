<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Elektronik (TTE) | {{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</title>
    @if(!empty($shop['app_favicon']))
        <link rel="icon" href="{{ route('media.file', ['path' => $shop['app_favicon']]) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 sm:px-6 flex flex-col justify-between">
    
    <div class="max-w-3xl mx-auto w-full space-y-6">
        
        {{-- HEADER PORTAL --}}
        <div class="text-center space-y-2">
            @if(!empty($shop['shop_logo']))
                <img src="{{ route('media.file', ['path' => $shop['shop_logo']]) }}" class="h-16 mx-auto object-contain mb-2">
            @endif
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">{{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</h1>
            <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest bg-indigo-50 inline-block px-4 py-1.5 rounded-full border border-indigo-100">
                🛡️ Sistem Verifikasi Tanda Tangan Elektronik (TTE)
            </p>
        </div>

        {{-- STATUS BANNER TERVERIFIKASI --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-[2.5rem] p-6 sm:p-8 text-white shadow-xl shadow-emerald-900/10 flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shrink-0 border-2 border-white/40 shadow-inner">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="space-y-1">
                <div class="inline-block bg-white/20 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-md tracking-wider">
                    VALID & TERVERIFIKASI RESMI
                </div>
                <h2 class="text-xl font-black tracking-tight leading-tight">DOKUMEN FAKTUR & TTE SAH</h2>
                <p class="text-xs text-emerald-100 font-medium">
                    Dokumen ini telah diverifikasi secara digital dan terdaftar di database sistem {{ $shop['app_name'] ?? 'SIKANDA' }}.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- KARTU 1: PENANGGUNG JAWAB / PENANDATANGAN DIGITAL --}}
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center space-x-3 border-b border-gray-100 pb-3">
                    <div class="bg-indigo-50 p-2.5 rounded-xl text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 text-xs uppercase tracking-wider">Penanggung Jawab (TTE)</h3>
                        <p class="text-[10px] text-gray-400 font-medium">Informasi Petugas Penandatangan</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Nama Penandatangan:</span>
                        <p class="font-black text-gray-900 text-sm mt-0.5">{{ $signerName }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Jabatan / Peran:</span>
                        <p class="font-bold text-indigo-600 bg-indigo-50 inline-block px-2.5 py-0.5 rounded-md mt-0.5">{{ $signerTitle }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Waktu Penandatanganan:</span>
                        <p class="font-bold text-gray-700 mt-0.5">{{ $signedAt ?? $sale->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB' }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Integritas Dokumen (SHA-256):</span>
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[9px] text-gray-600 break-all select-all mt-1">
                            {{ $tteHash }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: RINCIAN TRANSAKSI FAKTUR --}}
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center space-x-3 border-b border-gray-100 pb-3">
                    <div class="bg-emerald-50 p-2.5 rounded-xl text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 text-xs uppercase tracking-wider">Rincian Dokumen</h3>
                        <p class="text-[10px] text-gray-400 font-medium">Data Transaksi Penjualan</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase">Nomor Invoice:</span>
                        <span class="font-mono font-black text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-md">{{ $sale->transaction_number }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase">Nama Pelanggan:</span>
                        <span class="font-bold text-gray-800">{{ $sale->customer_name ?? 'Pelanggan Umum' }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase">Status Pembayaran:</span>
                        <span class="bg-green-100 text-green-700 font-black text-[10px] px-2.5 py-0.5 rounded-full uppercase">
                            {{ $sale->payment_status === 'success' ? 'LUNAS' : strtoupper($sale->payment_status) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase">Metode Pembayaran:</span>
                        <span class="font-bold uppercase text-gray-700">{{ $sale->payment_method }}</span>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-black text-gray-600 uppercase text-[10px]">Total Transaksi:</span>
                        <span class="font-black text-indigo-600 text-base">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABEL BARANG YANG DIBELI --}}
        <div class="bg-white rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-gray-200 space-y-4">
            <h3 class="font-black text-gray-800 text-xs uppercase tracking-wider">Rincian Barang yang Ditransaksikan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase">
                            <th class="p-3 text-center">No</th>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3 text-right">Harga Satuan</th>
                            <th class="p-3 text-center">Kuantitas</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($sale->details as $idx => $item)
                        @php $subtotal = ($item->price_at_transaction - ($item->discount_at_transaction ?? 0)) * $item->quantity; @endphp
                        <tr>
                            <td class="p-3 text-center text-gray-400 font-bold">{{ $idx + 1 }}</td>
                            <td class="p-3 font-bold text-gray-800">{{ $item->product->name ?? 'Produk' }}</td>
                            <td class="p-3 text-right text-gray-600">Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }}</td>
                            <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="p-3 text-right font-black text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER / DOWNLOAD BUTTON --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.reports.invoice.pdf', $sale->id) }}" target="_blank"
               class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-indigo-100 transition flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                Unduh Dokumen Faktur PDF
            </a>

            <p class="text-[10px] text-gray-400 font-medium text-center sm:text-right">
                Otentikasi Digital oleh {{ $shop['app_name'] ?? 'SIKANDA' }} POS
            </p>
        </div>

    </div>

    <footer class="text-center text-gray-400 text-xs mt-10 space-y-1">
        <p>&copy; {{ date('Y') }} <span class="text-gray-700 font-black">I Gusti Sultan</span>. All rights reserved.</p>
        <p class="text-[10px] text-gray-400 font-medium">Sistem Verifikasi Faktur & Tanda Tangan Elektronik (TTE)</p>
    </footer>

</body>
</html>
