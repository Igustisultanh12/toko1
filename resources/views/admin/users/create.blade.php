@extends('layouts.admin')

@section('title', 'Tambah Akun Pengguna')
@section('header_title', 'Tambah Akun Pengguna Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-20">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/></svg>
            Kembali ke Daftar Akun
        </a>
    </div>

    <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <div class="mb-8">
            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Form Akun Baru</h3>
            <p class="text-xs text-gray-400 font-medium mt-1">Daftarkan akun kasir atau administrator baru untuk mengakses sistem SIKANDA POS.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6" x-data="{ selectedRole: '{{ old('role', 'cashier') }}' }">
            @csrf

            {{-- NAMA PENGGUNA --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap Petugas / Pengguna <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Sultan / Kasir Shift Pagi" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3.5 text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#00AA13] focus:bg-white transition-all @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- EMAIL LOGIN --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Alamat Email (Untuk Login) <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="kasir2@ananda.site" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3.5 text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#00AA13] focus:bg-white transition-all @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- PERAN / ROLE --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Peran Akun (Role Hak Akses) <span class="text-rose-500">*</span></label>
                <select name="role" x-model="selectedRole" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3.5 text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#00AA13] focus:bg-white transition-all @error('role') border-rose-500 @enderror">
                    <option value="cashier">🛒 Kasir (POS) - Hanya Transaksi Kasir</option>
                    <option value="admin">👑 Administrator - Akses Penuh Laporan, Produk, Kasir & Pengaturan</option>
                </select>
                @error('role')
                    <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- FORM ALIAS / GELAR JABATAN TTE (TERSEDIA UNTUK ADMIN DAN KASIR) --}}
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-5 space-y-2">
                <label class="block text-xs font-black text-[#00880F] uppercase tracking-wider">
                    Alias / Jabatan TTD (TTE) <span class="text-gray-400 font-normal text-[10px] lowercase">(opsional)</span>
                </label>
                <input type="text" name="alias" value="{{ old('alias') }}" 
                       :placeholder="selectedRole === 'admin' ? 'Contoh: Pemilik Toko / Owner / Manager / Kepala Cabang' : 'Contoh: Kasir Shift Pagi / Kasir Utama / Staff Pelayanan'"
                       class="w-full bg-white border border-emerald-300 rounded-xl px-4 py-3 text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#00AA13] transition-all outline-none @error('alias') border-rose-500 @enderror">
                <p class="text-[10px] text-gray-500 font-medium">
                    🛡️ Gelar/jabatan alias inilah yang akan tercetak otomatis di atas TTD / TTE pada nota faktur dan laporan PDF saat akun ini mencetak dokumen (menggantikan tulisan default "Administrator" atau "Petugas Kasir").
                </p>
                @error('alias')
                    <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Kata Sandi / Password (Min. 6 Karakter) <span class="text-rose-500">*</span></label>
                <input type="password" name="password" placeholder="••••••••" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3.5 text-xs font-bold text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all @error('password') border-rose-500 @enderror">
                @error('password')
                    <p class="text-rose-500 text-[11px] font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 bg-[#00AA13] hover:bg-[#00880F] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5"/></svg>
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
