@extends('layouts.admin')

@section('header_title', 'Buku Panduan Penggunaan')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-16">
    
    {{-- HERO BANNER & DOWNLOAD BUTTON --}}
    <div class="bg-gradient-to-r from-[#004D13] to-[#00880F] rounded-[2.5rem] p-8 sm:p-10 text-white shadow-2xl shadow-emerald-900/20 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-3 z-10 text-center md:text-left">
            <span class="bg-white/20 text-emerald-100 text-[10px] font-black uppercase px-3.5 py-1.5 rounded-full backdrop-blur-md inline-block">
                📖 Dokumentasi Resmi & Manual Operasional
            </span>
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight">
                Buku Panduan Lengkap Sistem {{ $shop['app_name'] ?? 'POS' }}
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100/90 max-w-2xl leading-relaxed">
                Panduan menyeluruh mencakup seluruh modul Administrator & Kasir POS, mulai dari login, katalog produk, transaksi kasir tunai & QRIS DOKU, laporan keuangan & arus kas, tanda tangan elektronik (TTE), pengaturan toko, hingga troubleshooting server.
            </p>
        </div>

        <div class="z-10 shrink-0">
            <a href="{{ route('admin.manual.pdf') }}" 
               class="inline-flex items-center space-x-3 px-8 py-5 bg-white text-[#00661A] hover:bg-emerald-50 active:scale-95 rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 text-[#00AA13]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh PDF Buku Panduan (Lengkap)</span>
            </a>
        </div>

        {{-- BACKGROUND DECORATION --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    {{-- GRID DAFTAR 10 BAB PANDUAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- BAB 1 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    01
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 1: Pengenalan & Arsitektur Sistem</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Tujuan perancangan sistem, fondasi teknologi modern (Laravel 11, Tailwind, Alpine.js, DomPDF), hak akses Administrator vs Kasir, standar keabsahan hukum TTE UU ITE No. 11/2008, dan gateway pembayaran QRIS DOKU.
            </p>
        </div>

        {{-- BAB 2 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    02
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 2: Otentikasi & Manajemen Akun</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Prosedur masuk (*login*), proteksi formulir CSRF, proteksi *brute-force*, manajemen sesi kasir, pergantian kata sandi (*password change*), dan prosedur keluar aman (*logout*).
            </p>
        </div>

        {{-- BAB 3 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    03
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 3: Dashboard & Pusat Komando</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Statistik omset *real-time*, total pendapatan tunai vs digital, indikator keaktifan toko, grafik tren arus kas 7 hari terakhir, dan pintasan cepat menuju modul transaksi kasir.
            </p>
        </div>

        {{-- BAB 4 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    04
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 4: Manajemen Produk & Stok</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Input master barang, pengaturan kode SKU/barcode, manajemen harga modal vs harga jual, upload foto barang, alert stok menipis, dan import data massal menggunakan file Excel/CSV.
            </p>
        </div>

        {{-- BAB 5 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    05
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 5: Kasir Point of Sale (POS)</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Operasional kasir layar sentuh / PC, barcode scanner, pembayaran tunai & hitung kembalian otomatis, dynamic QRIS DOKU real-time webhook, audio chime kasir, pintasan keyboard (ESC & B), struk thermal bluetooth, nota PDF grayscale, kirim nota WA, dan cetak label resi ekspedisi A6.
            </p>
        </div>

        {{-- BAB 6 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    06
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 6: Analitik & Laporan Lengkap</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Laporan Penjualan (Harian, Bulanan, Kuartal, Tahunan), Laporan Keuangan & Arus Kas (Potongan Biaya DOKU MDR 0.7% dan Penerimaan Netto Bersih), Laporan Audit QRIS, Laporan Stok & Valuasi Aset Toko, serta export resmi ke format PDF Landscape & Excel (.xlsx).
            </p>
        </div>

        {{-- BAB 7 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    07
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 7: Tanda Tangan Elektronik (TTE)</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Landasan hukum UU ITE No. 11/2008, penomoran dinamis dokumen resmi mengikuti tanggal data laporan, QR Code TTE penandatangan dinamis, portal publik verifikasi keaslian dokumen (/verify/document), dan tautan faktur sementara 24 jam.
            </p>
        </div>

        {{-- BAB 8 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    08
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 8: Pengaturan Toko & Integrasi</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Identitas toko (Nama Usaha, Alamat, Kontak WA, Logo Toko), Branding Aplikasi (Nama Sistem, Favicon), Pengaturan Suara Bel Kasir, dan Konfigurasi Gateway DOKU Merchant.
            </p>
        </div>

        {{-- BAB 9 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    09
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 9: Manajemen Akun & Gelar TTE</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Manajemen akun kasir & admin, penentuan wewenang role, dan pengisian kolom "Alias / Gelar Jabatan TTD" yang tercetak otomatis di atas TTE tanda tangan dokumen resmi.
            </p>
        </div>

        {{-- BAB 10 --}}
        <div class="bg-white p-6 sm:p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#00880F] flex items-center justify-center font-black text-sm">
                    10
                </div>
                <h3 class="font-black text-gray-900 text-base uppercase">BAB 10: Pemeliharaan & Troubleshooting</h3>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Panduan deploy server aaPanel, penanganan static file Nginx, perbaikan storage symlink via streaming route /media-file, pengecekan log error Laravel, FAQ operasional kasir, dan glosarium istilah bisnis.
            </p>
        </div>

    </div>

    {{-- CALL TO ACTION BOTTOM --}}
    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm text-center space-y-4">
        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Siap Mencetak Dokumen Panduan?</h3>
        <p class="text-xs text-gray-500 max-w-xl mx-auto">
            Buku panduan PDF disusun dengan standar buku operasional resmi, dilengkapi penomoran halaman otomatis, bagan diagram, tabel prosedur, dan penjelasan menyeluruh tanpa ada modul yang terlewatkan.
        </p>
        <a href="{{ route('admin.manual.pdf') }}" 
           class="inline-flex items-center space-x-2 px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Unduh Buku Panduan PDF Sekarang</span>
        </a>
    </div>

</div>
@endsection
