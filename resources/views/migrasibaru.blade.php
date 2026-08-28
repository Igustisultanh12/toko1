<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Migrasi & Pemulihan Sistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden">

    <!-- SUBTLE BACKGROUND GLOW -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-100/60 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-100/60 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-6 sm:p-10 space-y-6 relative z-10">
        
        @if(!$hasSubmitted)
            <!-- ======================================================== -->
            <!-- STATE 1: FORM UPLOAD FILE & OPSI MIGRASI                -->
            <!-- ======================================================== -->
            
            <!-- HEADER -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#00AA13] text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-emerald-600/30">
                        ⚡
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                            Portal Migrasi Sistem
                        </h1>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">Unggah file backup untuk memindahkan semua data & foto</p>
                    </div>
                </div>
                <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-[#00661A] border border-emerald-200">
                    Satu-Klik
                </span>
            </div>

            <!-- FORM MIGRASI -->
            <form id="migrationForm" action="/migrasibaru" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- DROPZONE FILE UPLOAD -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-slate-700 uppercase tracking-wider">
                        1. Pilih File Backup (.ZIP / .JSON / .SQL) <span class="text-rose-500">*</span>
                    </label>
                    
                    <div class="p-6 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 hover:border-[#00AA13] transition text-center space-y-2 cursor-pointer relative" onclick="document.getElementById('backupFileInput').click()">
                        <div class="w-12 h-12 bg-white rounded-2xl border border-slate-200 flex items-center justify-center text-2xl mx-auto shadow-sm">
                            📦
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800" id="fileLabel">Klik untuk memilih file paket migrasi</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">Mendukung file .ZIP (paket lengkap dengan foto), .JSON, atau .SQL (Maks. 200MB)</p>
                        </div>
                        <input 
                            type="file" 
                            name="backup_file" 
                            id="backupFileInput" 
                            accept=".zip,.json,.sql"
                            class="hidden"
                            onchange="updateFileName(this)"
                        >
                    </div>
                </div>

                <!-- PILIHAN METODE PEMULIHAN -->
                <div class="space-y-2 pt-1">
                    <label class="block text-[11px] font-black text-slate-700 uppercase tracking-wider">
                        2. Pilih Metode Pemulihan Data:
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-4 rounded-2xl border-2 border-[#00AA13] bg-emerald-50/40 cursor-pointer transition flex flex-col justify-between space-y-2 text-left">
                            <div class="flex justify-between items-center">
                                <span class="text-base">🧹</span>
                                <input type="radio" name="mode" value="replace" checked class="w-4 h-4 text-[#00AA13] focus:ring-[#00AA13]">
                            </div>
                            <div>
                                <h5 class="font-black text-xs text-slate-900 uppercase">Timpa Bersih (Fresh Replace)</h5>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Sangat direkomendasikan untuk setup server/domain baru.</p>
                            </div>
                        </label>

                        <label class="p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:border-purple-300 cursor-pointer transition flex flex-col justify-between space-y-2 text-left">
                            <div class="flex justify-between items-center">
                                <span class="text-base">🔀</span>
                                <input type="radio" name="mode" value="merge" class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                            </div>
                            <div>
                                <h5 class="font-black text-xs text-slate-900 uppercase">Gabungkan (Merge Data)</h5>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Menyisipkan data tanpa menghapus catatan yang sudah ada.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- TOMBOL EKSEKUSI UTAMA -->
                <div class="space-y-2 pt-2">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-emerald-500/25 transition flex items-center justify-center space-x-2"
                    >
                        <span>🚀 Unggah File & Mulai Proses Migrasi</span>
                    </button>
                </div>
            </form>

            <!-- OPSI TANPA FILE -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs gap-3">
                <span class="text-slate-400 font-medium text-[11px]">Belum punya file backup?</span>
                <form action="/migrasibaru" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-[11px] uppercase tracking-wider transition text-center"
                    >
                        ⚡ Setup Database Kosong Saja (Tanpa File)
                    </button>
                </form>
            </div>

        @else
            <!-- ======================================================== -->
            <!-- STATE 2: HASIL EKSEKUSI SELESAI (CHECKLIST)             -->
            <!-- ======================================================== -->
            
            <!-- HEADER STATUS -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl {{ $hasError ? 'bg-rose-500' : 'bg-[#00AA13]' }} text-white flex items-center justify-center text-2xl font-black shadow-lg">
                        {{ $hasError ? '⚠️' : '✅' }}
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                            {{ $hasError ? 'Migrasi Ada Peringatan' : 'Migrasi Berhasil Selesai' }}
                        </h1>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">Waktu Selesai: {{ $time }}</p>
                    </div>
                </div>
                <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $hasError ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-[#00661A] border border-emerald-200' }}">
                    {{ $hasError ? 'Perlu Diperiksa' : 'Siap Digunakan' }}
                </span>
            </div>

            <!-- CHECKLIST HASIL -->
            <div class="space-y-3">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Rincian Hasil Migrasi:</h3>
                
                <div class="space-y-2.5">
                    @foreach($results as $res)
                        <div class="p-4 rounded-2xl border {{ $res['status'] === 'SUCCESS' ? 'bg-emerald-50/50 border-emerald-200/80 text-emerald-950' : ($res['status'] === 'ERROR' ? 'bg-rose-50 border-rose-200 text-rose-950' : 'bg-slate-50 border-slate-200 text-slate-800') }} flex items-start space-x-3">
                            <span class="text-base shrink-0 mt-0.5">
                                @if($res['status'] === 'SUCCESS')
                                    ✅
                                @elseif($res['status'] === 'ERROR')
                                    ❌
                                @else
                                    ℹ️
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-black text-xs uppercase tracking-tight">{{ $res['title'] }}</h4>
                                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md {{ $res['status'] === 'SUCCESS' ? 'bg-emerald-600 text-white' : ($res['status'] === 'ERROR' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700') }}">
                                        {{ $res['status'] }}
                                    </span>
                                </div>
                                <p class="text-[11px] opacity-85 font-medium mt-1 whitespace-pre-line leading-relaxed font-mono">
                                    {{ $res['message'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- STATISTIK RINGKASAN DATABASE TERKINI -->
            <div class="p-5 bg-slate-900 text-white rounded-3xl space-y-3 shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-wider">📊 Ringkasan Data Saat Ini</span>
                    <span class="text-[10px] text-slate-400 font-bold">Database Aktif</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-3 bg-white/10 rounded-2xl border border-white/10">
                        <span class="text-[10px] text-emerald-300 font-black uppercase block">Produk:</span>
                        <p class="font-black text-lg text-white mt-0.5">{{ $stats['products'] }} Item</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-2xl border border-white/10">
                        <span class="text-[10px] text-purple-300 font-black uppercase block">Pesanan:</span>
                        <p class="font-black text-lg text-white mt-0.5">{{ $stats['orders'] }} Data</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-2xl border border-white/10">
                        <span class="text-[10px] text-blue-300 font-black uppercase block">Petugas:</span>
                        <p class="font-black text-lg text-white mt-0.5">{{ $stats['users'] }} Akun</p>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <a href="/login" class="py-3.5 px-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition text-center flex items-center justify-center space-x-1.5">
                    <span>🔑 Masuk ke Login</span>
                </a>

                <a href="/order" class="py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs uppercase tracking-wider rounded-2xl transition text-center flex items-center justify-center space-x-1.5">
                    <span>🛍️ Katalog Toko</span>
                </a>

                <a href="/migrasibaru" class="py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs uppercase tracking-wider rounded-2xl transition text-center flex items-center justify-center space-x-1.5">
                    <span>🔄 Ulangi / Unggah Lagi</span>
                </a>
            </div>

        @endif

    </div>

    <!-- SCRIPT PREVIEW FILE & LOADING STATE -->
    <script>
        function updateFileName(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                document.getElementById('fileLabel').innerHTML = `✅ <b>${file.name}</b> (${sizeMb} MB) siap diproses.`;
            }
        }

        const migrationForm = document.getElementById('migrationForm');
        if (migrationForm) {
            migrationForm.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = '<span>⏳ Sedang Mengunggah & Memulihkan Data... Mohon Tunggu...</span>';
            });
        }
    </script>

</body>
</html>
