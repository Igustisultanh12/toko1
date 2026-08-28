@extends('layouts.admin')

@section('title', 'Manajemen Akun Pengguna')
@section('header_title', 'Manajemen Akun & Kasir')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-20">

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5"/></svg>
        <span class="font-bold text-xs">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
        <span class="font-bold text-xs">{{ session('error') }}</span>
    </div>
    @endif

    {{-- HEADER ACTION & STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] p-6 rounded-[2rem] shadow-xl text-white">
            <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Total Akun Terdaftar</p>
            <h3 class="text-3xl font-black">{{ $stats['total'] ?? 0 }} <span class="text-sm font-medium text-emerald-200">Pengguna</span></h3>
            <p class="text-[10px] text-emerald-200 mt-1 font-bold">Hak akses sistem SIKANDA</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Akun Kasir (POS)</p>
            <h3 class="text-3xl font-black text-[#00880F]">{{ $stats['cashiers'] ?? 0 }} <span class="text-xs text-gray-400">Petugas</span></h3>
            <p class="text-[10px] text-emerald-600 mt-1 font-bold">● Akses Kasir Transaksi</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Administrator</p>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['admins'] ?? 0 }} <span class="text-xs text-gray-400">Admin</span></h3>
            <p class="text-[10px] text-sky-600 mt-1 font-bold">● Akses Penuh Sistem</p>
        </div>
    </div>

    {{-- CARD TABEL & FILTER --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Daftar Akun Pengguna</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Kelola akun kasir dan administrator yang dapat login ke aplikasi.</p>
            </div>
            
            <a href="{{ route('admin.users.create') }}" 
               class="px-5 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5"/></svg>
                Tambah Akun Kasir / Admin
            </a>
        </div>

        {{-- FILTER & SEARCH --}}
        <div class="p-6 bg-gray-50/50 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau email..."
                           class="w-full bg-white border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <div class="absolute left-3.5 top-3.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                    </div>
                </div>

                <div class="w-full sm:w-48">
                    <select name="role" onchange="this.form.submit()" 
                            class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Semua Peran (Role)</option>
                        <option value="cashier" {{ ($role ?? '') == 'cashier' ? 'selected' : '' }}>Kasir (POS)</option>
                        <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <button type="submit" class="px-5 py-3 bg-gray-900 text-white rounded-2xl font-bold text-xs uppercase hover:bg-black transition-all">
                    Cari
                </button>
            </form>
        </div>

        {{-- TABEL PENGGUNA --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                        <th class="p-5 text-center">No</th>
                        <th class="p-5">Nama Lengkap</th>
                        <th class="p-5">Email (Login)</th>
                        <th class="p-5 text-center">Peran (Role)</th>
                        <th class="p-5">Terdaftar Sejak</th>
                        <th class="p-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    @forelse($users as $index => $u)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="p-5 text-center font-bold text-gray-400">
                            {{ $users->firstItem() ? $users->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="p-5 font-bold text-gray-800 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs {{ $u->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $u->name }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    @if(Auth::id() === $u->id)
                                        <span class="text-[9px] text-[#00880F] font-black bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">Akun Anda (Aktif)</span>
                                    @endif
                                    @if(!empty($u->alias))
                                        <span class="text-[9px] text-indigo-700 font-black bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200/60">TTD: {{ $u->alias }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-5 font-mono text-gray-600 font-medium">
                            {{ $u->email }}
                        </td>
                        <td class="p-5 text-center">
                            @if($u->role === 'admin')
                                <span class="bg-indigo-100 text-indigo-700 font-black px-3 py-1 rounded-full text-[9px] uppercase tracking-wider">
                                    ADMINISTRATOR
                                </span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 font-black px-3 py-1 rounded-full text-[9px] uppercase tracking-wider">
                                    PETUGAS KASIR
                                </span>
                            @endif
                        </td>
                        <td class="p-5 text-gray-500 font-medium whitespace-nowrap">
                            {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }} WIB
                        </td>
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $u->id) }}" 
                                   class="p-2.5 bg-gray-100 text-gray-700 hover:bg-indigo-600 hover:text-white rounded-xl transition-all font-bold text-xs" title="Edit Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                                </a>

                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Apakah Anda yakin ingin menghapus akun {{ addslashes($u->name) }}? Akun ini tidak akan dapat login lagi.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all font-bold text-xs" title="Hapus Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-gray-400 font-medium">
                            Tidak ada akun pengguna yang sesuai dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-6 border-t border-gray-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
