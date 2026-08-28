<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | SIKANDA Kasir Modern</title>
    
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
<body class="min-h-screen flex items-center justify-center p-6 my-10">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tighter mb-2">SIKANDA</h1>
            <p class="text-indigo-100 text-sm font-medium uppercase tracking-widest">Registrasi Pengguna Baru</p>
        </div>

        <div class="glass-effect rounded-[2rem] shadow-2xl p-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Akun</h2>
            <p class="text-gray-500 text-sm mb-8">Lengkapi data di bawah untuk mulai mengelola kasir.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-5">
                    <label for="name" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Nama Lengkap</label>
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700" 
                           placeholder="Masukkan nama Anda">
                    @error('name')
                        <p class="text-red-500 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700" 
                           placeholder="nama@toko.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Kata Sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700" 
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700" 
                           placeholder="Ketik ulang kata sandi">
                </div>

                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 active:scale-[0.98] transition-all">
                        DAFTAR SEKARANG
                    </button>
                    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-400 hover:text-indigo-600 transition