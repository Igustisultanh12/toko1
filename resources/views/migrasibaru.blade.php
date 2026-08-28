<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Migrasi Sistem & Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-6 sm:p-10 space-y-6">
        
        <!-- HEADER STATUS -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl {{ $hasError ? 'bg-rose-500' : 'bg-[#00AA13]' }} text-white flex items-center justify-center text-2xl font-black shadow-lg">
                    {{ $hasError ? '⚠️' : '⚡' }}
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                        {{ $hasError ? 'Migrasi Ada Kendala' : 'Migrasi Berhasil Selesai' }}
                    </h1>
                    <p class="text-xs text-slate-400 font-bold mt-0.5">Waktu Eksekusi: {{ $time }}</p>
                </div>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $hasError ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-[#00661A] border border-emerald-200' }}">
                {{ $hasError ? 'Ada Peringatan' : 'Siap Digunakan' }}
            </span>
        </div>

        <!-- LIST HASIL PROSES MIGRASI -->
        <div class="space-y-3">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Rincian Hasil Eksekusi:</h3>
            
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

        <!-- INFORMASI AKUN LOGIN PETUGAS -->
        <div class="p-5 bg-slate-900 text-white rounded-3xl space-y-3 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-wider">🔐 Akun Akses Default</span>
                <span class="text-[10px] text-slate-400 font-bold">Harap simpan info ini</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white/10 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-emerald-300 font-black uppercase block">Akun Administrator:</span>
                    <p class="font-mono font-bold mt-0.5">admin@sultanweb.id</p>
                    <p class="text-[10px] text-slate-400">Password: <code class="text-white font-bold">password</code></p>
                </div>
                <div class="p-3 bg-white/10 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-emerald-300 font-black uppercase block">Akun Kasir (POS):</span>
                    <p class="font-mono font-bold mt-0.5">kasir@sultanweb.id</p>
                    <p class="text-[10px] text-slate-400">Password: <code class="text-white font-bold">password</code></p>
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

            <a href="/admin/backup" class="py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs uppercase tracking-wider rounded-2xl transition text-center flex items-center justify-center space-x-1.5">
                <span>🔄 Pusat Backup</span>
            </a>
        </div>

    </div>

</body>
</html>
