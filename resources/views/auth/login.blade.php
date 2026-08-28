<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $shop['app_name'] ?? 'SIKANDA' }} POS</title>
    @if(!empty($shop['app_favicon']))
        <link rel="icon" href="{{ route('media.file', ['path' => $shop['app_favicon']]) }}">
    @endif
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @if(\App\Services\TurnstileService::isEnabled())
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(145deg, #002B0A 0%, #004D13 50%, #007A1C 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-gray-800">

    <div class="w-full max-w-md">
        {{-- HEADER LOGO --}}
        <div class="text-center mb-6 space-y-2">
            @if(!empty($shop['shop_logo']))
                <img src="{{ route('media.file', ['path' => $shop['shop_logo']]) }}" class="h-16 mx-auto object-contain mb-2 drop-shadow-md">
            @elseif(!empty($shop['app_favicon']))
                <img src="{{ route('media.file', ['path' => $shop['app_favicon']]) }}" class="w-16 h-16 rounded-3xl mx-auto p-2 bg-white object-contain shadow-xl">
            @else
                <div class="w-16 h-16 rounded-3xl bg-[#00AA13] text-white font-black text-2xl flex items-center justify-center mx-auto shadow-xl border-2 border-emerald-300/40">
                    {{ strtoupper(substr($shop['app_name'] ?? 'S', 0, 1)) }}
                </div>
            @endif
            <h1 class="text-3xl font-black text-white tracking-tight uppercase">{{ $shop['app_name'] ?? 'SIKANDA' }}</h1>
            <p class="text-emerald-200 text-xs font-black uppercase tracking-widest">{{ $shop['app_tagline'] ?? 'Sistem Kasir Modern' }}</p>
        </div>

        {{-- LOGIN CARD --}}
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 sm:p-10 border border-white/20">
            <div class="mb-6">
                <span class="px-3 py-1 bg-emerald-50 text-[#00880F] rounded-full text-[10px] font-black uppercase tracking-wider">
                    🟢 Masuk Petugas Kasir & Admin
                </span>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mt-2">Selamat Datang</h2>
                <p class="text-gray-400 text-xs font-medium">Silakan masuk dengan akun toko Anda.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-3 bg-emerald-50 text-[#00880F] rounded-2xl font-bold text-xs">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Email Akun</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all text-xs font-bold text-gray-800" 
                           placeholder="nama@toko.com">
                    @error('email')
                        <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                           class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all text-xs font-bold text-gray-800" 
                           placeholder="••••••••">
                    @error('password')
                        <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded-lg border-gray-300 text-[#00AA13] focus:ring-[#00AA13]">
                        <span class="ml-2 text-xs font-bold text-gray-500">Ingat saya</span>
                    </label>
                </div>

                {{-- WIDGET CLOUDFLARE TURNSTILE (JIKA AKTIF DI SETTING ADMIN) --}}
                @if(\App\Services\TurnstileService::isEnabled())
                    <div class="pt-2 flex flex-col items-center justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ \App\Services\TurnstileService::getSiteKey() }}" data-theme="light"></div>
                        @error('turnstile')
                            <p class="text-rose-500 text-[10px] font-bold text-center mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <button type="submit" 
                        class="w-full bg-[#00AA13] hover:bg-[#00880F] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/25 active:scale-95 transition-all mt-2">
                    Masuk ke Kasir
                </button>
            </form>

            <div class="mt-8 text-center border-t border-gray-100 pt-4 space-y-1">
                <p class="text-gray-600 text-[10px] font-black uppercase tracking-wider">{{ $shop['shop_name'] ?? 'SISTEM KASIR' }}</p>
                <p class="text-gray-400 text-[9px] font-bold">&copy; {{ date('Y') }} <span class="text-gray-600 font-extrabold">I Gusti Sultan</span>. All rights reserved.</p>
            </div>
        </div>
    </div>

</body>
</html>
