<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Sandi | SIKANDA Kasir Modern</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tighter mb-2">SIKANDA</h1>
            <p class="text-indigo-100 text-sm font-medium uppercase tracking-widest">Atur Ulang Kata Sandi</p>
        </div>

        <div class="glass-effect rounded-[2rem] shadow-2xl p-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Lupa Sandi?</h2>
            
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                Jangan khawatir. Masukkan alamat email Anda dan kami akan mengirimkan tautan pemulihan agar Anda bisa membuat sandi baru.
            </p>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl text-sm font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-8">
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Email Terdaftar</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700" 
                           placeholder="nama@toko.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 active:scale-[0.98] transition-all">
                        KIRIM TAUTAN RESET
                    </button>
                    
                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-400 hover:text-indigo-600 transition flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Kembali ke halaman Login
                        </a>
                    </div>
                </div>
            </form>

            <div class="mt-10 text-center border-t border-gray-100 pt-6">
                <p class="text-gray-400 text-[10px] uppercase tracking-tighter">Powered by <span class="font-bold text-indigo-400">Sultan Web</span></p>
            </div>
        </div>
    </div>

</body>
</html>