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
            <p class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-50 inline-block px-4 py-1.5 rounded-full border border-emerald-200">
                🛡️ Sistem Verifikasi Tanda Tangan Elektronik (TTE)
            </p>
        </div>

        {{-- STATUS BANNER TERVERIFIKASI --}}
        <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] rounded-[2.5rem] p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20 flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shrink-0 border-2 border-white/40 shadow-inner">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="space-y-1">
                <div class="inline-block bg-white/20 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-md tracking-wider">
                    VALID & TERVERIFIKASI RESMI
                </div>
                <h2 class="text-xl font-black tracking-tight leading-tight">DOKUMEN & TTE DINYATAKAN SAH</h2>
                <p class="text-xs text-emerald-100 font-medium">
                    Dokumen laporan ini sah dicetak dan ditandatangani secara digital oleh petugas berwenang melalui sistem {{ $shop['app_name'] ?? 'SIKANDA' }}.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- KARTU 1: PENANGGUNG JAWAB / PENANDATANGAN DIGITAL --}}
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center space-x-3 border-b border-gray-100 pb-3">
                    <div class="bg-emerald-50 p-2.5 rounded-xl text-[#00880F]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 text-xs uppercase tracking-wider">Penanggung Jawab (TTE)</h3>
                        <p class="text-[10px] text-gray-400 font-medium">Petugas yang Mencetak Dokumen</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nama Penandatangan:</span>
                        <p class="font-black text-gray-900 text-sm mt-0.5">{{ $signerName }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Jabatan / Otoritas:</span>
                        <p class="font-bold text-[#00880F] bg-emerald-50 inline-block px-2.5 py-0.5 rounded-md mt-0.5 border border-emerald-200/60">{{ $signerTitle }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Waktu Cetak & Penandatanganan:</span>
                        <p class="font-bold text-gray-700 mt-0.5">{{ $signedAt }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Integritas Dokumen (SHA-256):</span>
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[9px] text-gray-600 break-all select-all mt-1">
                            {{ $tteHash }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: RINCIAN DOKUMEN LAPORAN --}}
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center space-x-3 border-b border-gray-100 pb-3">
                    <div class="bg-sky-50 p-2.5 rounded-xl text-sky-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 text-xs uppercase tracking-wider">Rincian Dokumen</h3>
                        <p class="text-[10px] text-gray-400 font-medium">Informasi Arsip Laporan</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Jenis Dokumen:</span>
                        <p class="font-black text-gray-900 mt-0.5">{{ $docTitle }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nomor Dokumen:</span>
                        <p class="font-mono font-bold text-gray-800 bg-gray-50 p-2 rounded-xl border border-gray-100 inline-block mt-0.5">{{ $docNo }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Penerbit:</span>
                        <p class="font-bold text-gray-700 mt-0.5">{{ $shop['shop_name'] ?? 'TOKO ANANDA' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $shop['shop_address'] ?? 'Jember, Jawa Timur' }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Status Keaslian:</span>
                        <span class="inline-flex items-center text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md font-bold text-[10px] mt-0.5 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            Terdaftar di Database Server
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- LEGAL FOOTER --}}
        <div class="bg-white rounded-[2rem] p-6 text-center shadow-sm border border-gray-200">
            <p class="text-[11px] text-gray-500 font-medium leading-relaxed">
                Tanda Tangan Elektronik (TTE) ini diterbitkan secara sah berdasarkan Undang-Undang Republik Indonesia Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik (UU ITE). Data penandatangan terverifikasi secara kriptografis dan sah secara hukum.
            </p>
        </div>

    </div>

    {{-- FOOTER HAK CIPTA --}}
    <footer class="text-center py-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest space-y-1">
        <p>&copy; {{ date('Y') }} <span class="text-gray-700 font-black">I Gusti Sultan</span>. All rights reserved.</p>
        <p class="text-[9px] text-gray-400 font-medium">Sistem Verifikasi Dokumen & Tanda Tangan Elektronik (TTE)</p>
    </footer>

</body>
</html>
