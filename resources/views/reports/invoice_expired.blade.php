<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tautan Faktur Kadaluarsa | {{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-gray-100 text-center space-y-6">
        
        {{-- ICON KADALUARSA --}}
        <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div>
            <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest bg-rose-50 px-3 py-1 rounded-full">Link Tidak Berlaku</span>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight mt-2">Tautan Faktur Telah Kadaluarsa</h1>
            <p class="text-xs text-gray-500 font-medium mt-2 leading-relaxed">
                Tautan unduh untuk faktur <b class="font-mono text-gray-800">{{ $sale->transaction_number ?? 'Transaksi' }}</b> hanya berlaku selama <span class="font-bold text-gray-800">24 Jam (1 Hari)</span> demi keamanan data transaksi.
            </p>
        </div>

        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs text-gray-600 space-y-1 text-left">
            <p class="font-black text-gray-700 uppercase text-[10px] tracking-wider">Informasi Toko:</p>
            <p class="font-bold text-gray-900">{{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</p>
            @if(!empty($shop['shop_address']))
                <p class="text-[11px] text-gray-500">{{ $shop['shop_address'] }}</p>
            @endif
            @if(!empty($shop['shop_phone']))
                <p class="text-[11px] font-bold text-indigo-600">WhatsApp / Telp: {{ $shop['shop_phone'] }}</p>
            @endif
        </div>

        <div class="space-y-2 pt-2">
            @if(!empty($shop['shop_phone']))
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $shop['shop_phone']);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                    $waText = urlencode("Halo Admin " . ($shop['shop_name'] ?? 'Toko') . ", saya ingin meminta tautan baru untuk faktur transaksi saya dengan nomor: " . ($sale->transaction_number ?? ''));
                @endphp
                <a href="https://api.whatsapp.com/send?phone={{ $cleanPhone }}&text={{ $waText }}" target="_blank"
                   class="w-full bg-green-600 hover:bg-green-700 text-white py-3.5 px-6 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-green-100 transition flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                    Minta Tautan Baru ke Toko
                </a>
            @endif
        </div>

        <div class="border-t border-gray-100 pt-3 space-y-1">
            <p class="text-[10px] text-gray-400 font-medium">
                Sistem Kasir {{ $shop['app_name'] ?? '' }}
            </p>
            <p class="text-[9px] text-gray-400 font-bold">&copy; {{ date('Y') }} <span class="text-gray-600 font-extrabold">I Gusti Sultan</span>. All rights reserved.</p>
        </div>

    </div>
</body>
</html>
