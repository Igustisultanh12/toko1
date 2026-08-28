<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Panduan Lengkap Sistem POS & Administrator - {{ $shop['app_name'] ?? 'POS' }}</title>
    <style>
        @page {
            margin: 1.8cm 1.6cm 1.8cm 1.6cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.45;
            color: #2D3748;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* COVER STYLING */
        .cover-container {
            height: 100%;
            text-align: center;
            padding-top: 20px;
        }

        .cover-badge {
            background-color: #E6F4EA;
            color: #00880F;
            font-weight: 800;
            font-size: 8.5pt;
            padding: 5px 18px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            margin-bottom: 15px;
            border: 1.5px solid #A8DAB5;
        }

        .cover-title {
            font-size: 22pt;
            font-weight: 900;
            color: #1A202C;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.25;
            margin: 0 0 10px 0;
        }

        .cover-subtitle {
            font-size: 12pt;
            font-weight: 800;
            color: #00AA13;
            margin: 0 0 18px 0;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cover-divider {
            width: 120px;
            height: 4px;
            background-color: #00AA13;
            margin: 12px auto 20px auto;
            border-radius: 2px;
        }

        .cover-desc {
            font-size: 8.8pt;
            color: #4A5568;
            max-width: 520px;
            margin: 0 auto 25px auto;
            line-height: 1.5;
            text-align: center;
        }

        .cover-meta-box {
            background-color: #F7FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px 18px;
            text-align: left;
            margin: 0 auto;
            width: 90%;
        }

        .cover-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .cover-meta-table td {
            padding: 3.5px 5px;
            vertical-align: top;
        }

        .cover-meta-label {
            font-weight: bold;
            color: #4A5568;
            width: 38%;
        }

        .cover-meta-value {
            color: #1A202C;
            font-weight: 600;
        }

        .cover-footer {
            margin-top: 35px;
            font-size: 7.5pt;
            color: #718096;
            letter-spacing: 1.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* HEADINGS */
        h1.chapter-title {
            font-size: 13.5pt;
            font-weight: 900;
            color: #00661A;
            border-bottom: 2px solid #00AA13;
            padding-bottom: 4px;
            margin-top: 0;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chapter-num {
            font-size: 8.8pt;
            font-weight: 800;
            color: #00AA13;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 2px;
        }

        h2.section-title {
            font-size: 10pt;
            font-weight: 800;
            color: #2D3748;
            margin-top: 10px;
            margin-bottom: 6px;
            border-left: 3.5px solid #00AA13;
            padding-left: 7px;
        }

        h3.subsection-title {
            font-size: 9pt;
            font-weight: 700;
            color: #4A5568;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        p {
            margin: 0 0 6px 0;
            text-align: justify;
        }

        /* TABLES */
        table.doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 10px 0;
            font-size: 7.6pt;
        }

        table.doc-table th {
            background-color: #00661A;
            color: #FFFFFF;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 7px;
            border: 1px solid #004D13;
            text-align: left;
            letter-spacing: 0.5px;
        }

        table.doc-table td {
            padding: 4.5px 7px;
            border: 1px solid #CBD5E0;
            vertical-align: top;
        }

        table.doc-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        /* CALLOUT BOXES */
        .callout {
            border-radius: 6px;
            padding: 8px 10px;
            margin: 8px 0;
            font-size: 7.8pt;
            page-break-inside: avoid;
        }

        .callout-info {
            background-color: #EBF8FF;
            border-left: 3.5px solid #3182CE;
            color: #2B6CB0;
        }

        .callout-success {
            background-color: #F0FFF4;
            border-left: 3.5px solid #38A169;
            color: #276749;
        }

        .callout-warning {
            background-color: #FFFAF0;
            border-left: 3.5px solid #DD6B20;
            color: #C05621;
        }

        .callout-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.2pt;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            display: block;
        }

        /* STEP CARDS */
        .step-box {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 6px 9px;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .step-header {
            font-weight: bold;
            font-size: 8.2pt;
            color: #1A202C;
            margin-bottom: 2px;
        }

        .step-num {
            background-color: #00AA13;
            color: #FFFFFF;
            font-size: 7pt;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
            margin-right: 4px;
            display: inline-block;
        }

        code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #EDF2F7;
            color: #C53030;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.6pt;
            font-weight: bold;
        }

        .badge-shortcut {
            background-color: #2D3748;
            color: #FFFFFF;
            font-family: monospace;
            font-size: 7pt;
            padding: 1px 5px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }

        .toc-chapter-row {
            margin-bottom: 6px;
            font-size: 8pt;
        }

        .toc-chapter-title {
            font-weight: bold;
            color: #1A202C;
            text-transform: uppercase;
        }

        .toc-sub-row {
            padding-left: 14px;
            color: #4A5568;
            font-size: 7.5pt;
            margin-top: 1.5px;
        }

        ul, ol {
            margin: 0 0 6px 0;
            padding-left: 18px;
        }

        li {
            margin-bottom: 2.5px;
        }
    </style>
</head>
<body>

    {{-- DOMPDF AUTOMATIC PAGE NUMBERING SCRIPT --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 7.5;
            $color = array(0.5, 0.5, 0.5);

            $textRight = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $widthRight = $fontMetrics->get_text_width($textRight, $font, $size);
            
            $pdf->page_text(595 - 45 - $widthRight, 842 - 30, $textRight, $font, $size, $color);
            
            $appName = strtoupper($shop['app_name'] ?? 'POS');
            $footerLeft = "Buku Panduan Lengkap Sistem " . $appName . " | © I Gusti Sultan";
            $pdf->page_text(45, 842 - 30, $footerLeft, $font, $size, $color);

            $pdf->line(45, 842 - 35, 595 - 45, 842 - 35, array(0.85, 0.85, 0.85), 0.75);
        }
    </script>

    {{-- ========================================================================= --}}
    {{-- COVER BUKU PANDUAN UTAMA --}}
    {{-- ========================================================================= --}}
    <div class="cover-container">
        <div class="cover-badge">Buku Petunjuk Operasional & Pedoman Teknis Resmi</div>
        
        <h1 class="cover-title">BUKU PANDUAN LENGKAP<br>SISTEM KASIR & ADMINISTRASI</h1>
        <div class="cover-subtitle">{{ strtoupper($shop['app_name'] ?? 'SIKANDA POS') }} &bull; {{ strtoupper($shop['shop_name'] ?? 'TOKO BERKAH') }}</div>
        
        <div class="cover-divider"></div>

        <p class="cover-desc">
            Buku panduan operasional komprehensif yang memuat seluruh tata kelola transaksi kasir Point of Sale (POS), penerimaan pembayaran digital Dynamic QRIS DOKU, pencatatan arus kas otomatis, manajemen pergudangan, sertifikasi Tanda Tangan Elektronik (TTE) berlandaskan UU ITE No. 11/2008, serta arsitektur pemeliharaan server terpusat.
        </p>

        <div class="cover-meta-box">
            <table class="cover-meta-table">
                <tr>
                    <td class="cover-meta-label">Nama Aplikasi Resmi</td>
                    <td class="cover-meta-value">: {{ $shop['app_name'] ?? 'SIKANDA' }} Point of Sale Engine</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Identitas Badan Usaha / Toko</td>
                    <td class="cover-meta-value">: {{ $shop['shop_name'] ?? 'TOKO BERKAH' }}</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Alamat Resmi Usaha</td>
                    <td class="cover-meta-value">: {{ $shop['shop_address'] ?? 'Jalan Raya Utama No. 77' }}</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Saluran Bantuan Toko</td>
                    <td class="cover-meta-value">: {{ $shop['shop_phone'] ?? '081234567890' }}</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Arsitektur Backend / Frontend</td>
                    <td class="cover-meta-value">: Laravel 11.x, Tailwind CSS, Alpine.js, DomPDF & MySQL</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Arsitek & Pengelola Server</td>
                    <td class="cover-meta-value">: <b>I Gusti Sultan</b></td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Hak Cipta Perangkat Lunak</td>
                    <td class="cover-meta-value">: &copy; {{ date('Y') }} I Gusti Sultan. All rights reserved.</td>
                </tr>
                <tr>
                    <td class="cover-meta-label">Edisi Dokumen & Tanggal Terbit</td>
                    <td class="cover-meta-value">: Edisi 2.0 (Pemutakhiran Sistem QRIS & TTE) &bull; {{ date('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="cover-footer">
            DOKUMEN RESMI SISTEM OPERASIONAL &bull; DILINDUNGI HAK CIPTA HUKUM &bull; TAHUN {{ date('Y') }}
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- LEMBAR HAK CIPTA & KATA PENGANTAR --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <h1 class="chapter-title">LEMBAR HAK CIPTA & KATA PENGANTAR</h1>
    
    <div class="callout callout-info">
        <span class="callout-title">Pernyataan Hak Kekayaan Intelektual (HAKI)</span>
        Seluruh isi dari buku panduan operasional ini, mencakup metodologi alur kerja, struktur basis data, arsitektur antarmuka pengguna (UI/UX), logika bisnis controller, serta modul kriptografi Tanda Tangan Elektronik (TTE) merupakan karya cipta yang dilindungi oleh undang-undang hak cipta Republik Indonesia. Seluruh hak cipta dan kepemilikan intelektual dipegang teguh oleh <b>I Gusti Sultan</b>.
    </div>

    <table class="doc-table">
        <tr>
            <th style="width: 32%;">Parameter Dokumen</th>
            <th>Keterangan Rinci</th>
        </tr>
        <tr>
            <td><b>Judul Resmi Dokumen</b></td>
            <td>Buku Panduan Lengkap Sistem Kasir Point of Sale (POS) dan Administrasi Toko Terpadu</td>
        </tr>
        <tr>
            <td><b>Pemilik Hak Cipta & Lead Architect</b></td>
            <td><b>I Gusti Sultan</b></td>
        </tr>
        <tr>
            <td><b>Pengelola & Pemelihara Server</b></td>
            <td><b>I Gusti Sultan</b> (Seluruh perawatan server, aaPanel, Nginx, deployment, dan database dikelola penuh)</td>
        </tr>
        <tr>
            <td><b>Versi Dokumen</b></td>
            <td>2.0 (Revisi Komprehensif Seluruh Modul Sistem)</td>
        </tr>
        <tr>
            <td><b>Target Pembaca</b></td>
            <td>Administrator Toko, Kepala Bagian Logistik/Gudang, Petugas Kasir, Auditor Keuangan, dan Pengelola Sistem IT.</td>
        </tr>
    </table>

    <h2 class="section-title">KATA PENGANTAR PENGEMBANG</h2>
    <p>
        Puji dan syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas terselesaikannya penyusunan <b>Buku Panduan Lengkap Sistem Kasir Point of Sale (POS) dan Administrasi Toko Modern</b>. Sistem ini dirancang untuk menjawab tantangan operasional bisnis ritel kontemporer yang memerlukan kecepatan transaksi di meja kasir (*checkout speed*), transparansi arus kas real-time, integrasi pembayaran digital tanpa hambatan, serta akuntabilitas dokumen laporan formal yang berkekuatan hukum.
    </p>
    <p>
        Buku panduan ini disusun secara terstruktur dan mendalam dari <b>Bab 1 hingga Bab 10</b> tanpa ada satupun fitur atau menu yang terlewatkan. Mulai dari prosedur login, pemindaian barcode, transaksi tunai & QRIS DOKU otomatis, rekapitulasi keuangan harian/bulanan/kuartal/tahunan, verifikasi keaslian Tanda Tangan Elektronik (TTE) berbasis UU ITE No. 11 Tahun 2008, hingga tata kelola pemeliharaan server produksi aaPanel yang dikelola secara eksklusif oleh <b>I Gusti Sultan</b>.
    </p>
    <p>
        Kami berharap buku ini dapat memberikan pemahaman menyeluruh dan menjadi panduan praktis harian bagi seluruh jajaran staf, mulai dari kasir garis depan hingga pimpinan manajemen toko.
    </p>
    
    <div style="margin-top: 25px; text-align: right;" class="no-break">
        <p style="margin-bottom: 30px;">Jember, {{ date('d F Y') }}<br><b>Lead Software Architect & Server Maintainer</b></p>
        <p><b><u>I Gusti Sultan</u></b><br><span style="font-size: 7.5pt; color: #718096;">Sistem Arsitek & Pengelola Server Utama</span></p>
    </div>

    {{-- ========================================================================= --}}
    {{-- DAFTAR ISI LENGKAP --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <h1 class="chapter-title">DAFTAR ISI LENGKAP BUKU PANDUAN</h1>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 1: GAMBARAN UMUM & ARSITEKTUR TEKNOLOGI SISTEM</div>
        <div class="toc-sub-row">1.1 Latar Belakang & Filosofi Rancangan Sistem Kasir Modern</div>
        <div class="toc-sub-row">1.2 Tumpukan Teknologi (Technology Stack) & Arsitektur MVC Laravel 11</div>
        <div class="toc-sub-row">1.3 Matriks Perbandingan Hak Akses Pengguna (Admin vs Kasir)</div>
        <div class="toc-sub-row">1.4 Integrasi Layanan Pembayaran Digital Dynamic QRIS DOKU Merchant</div>
        <div class="toc-sub-row">1.5 Landasan Hukum & Validitas Tanda Tangan Elektronik (UU ITE No. 11/2008)</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 2: MANAJEMEN OTENTIKASI & KEAMANAN AKUN</div>
        <div class="toc-sub-row">2.1 Akses Masuk Sistem (Halaman Login /login) & Tata Letak Antarmuka</div>
        <div class="toc-sub-row">2.2 Anatomi Elemen Formulir Login & Logika Smart Redirect</div>
        <div class="toc-sub-row">2.3 Proteksi Formulir CSRF, Enkripsi Bcrypt & Proteksi Brute Force</div>
        <div class="toc-sub-row">2.4 Manajemen Profil Mandiri & Prosedur Pergantian Kata Sandi (/profile)</div>
        <div class="toc-sub-row">2.5 Prosedur Keluar Aman (Logout) & Keamanan Sesi Komputer Kasir</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 3: PUSAT KOMANDO & DASHBOARD ADMINISTRATOR</div>
        <div class="toc-sub-row">3.1 Antarmuka Panel Kontrol Administrator (/dashboard)</div>
        <div class="toc-sub-row">3.2 Banner Omset Hijau Gojek Pocket & Tombol Aksi Cepat (Quick Actions)</div>
        <div class="toc-sub-row">3.3 Analitik 4 Indikator Kinerja Utama (KPI) Finansial Real-Time</div>
        <div class="toc-sub-row">3.4 Visualisasi Grafik Tren Arus Kas & Analisis Penjualan 7 Hari Terakhir</div>
        <div class="toc-sub-row">3.5 Struktur Navigasi Sidebar Responsif & Copyright I Gusti Sultan</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 4: MANAJEMEN INVENTARIS PRODUK & PERGUDANGAN</div>
        <div class="toc-sub-row">4.1 Master Katalog Produk (/admin/products) & Indikator Ketersediaan</div>
        <div class="toc-sub-row">4.2 Fitur Pencarian Cepat Nama Barang & Filter Barcode</div>
        <div class="toc-sub-row">4.3 Formulir Tambah Produk Baru (/admin/products/create) & Barcode Auto Generator</div>
        <div class="toc-sub-row">4.4 Struktur Penetapan Harga Jual Kasir, Diskon Promo (%) & Satuan Kemasan</div>
        <div class="toc-sub-row">4.5 Tiga Tingkat Status Stok: Aman (Hijau), Menipis (Kuning), Habis (Merah)</div>
        <div class="toc-sub-row">4.6 Fitur Update Stok Cepat via Scanner Barcode di Katalog</div>
        <div class="toc-sub-row">4.7 Impor Data Produk Massal Melalui Spreadsheet Excel (.xlsx)</div>
        <div class="toc-sub-row">4.8 Pemeliharaan Data, Edit Barang, Snapshot Pricing & Penghapusan Aman</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 5: OPERASIONAL KASIR POINT OF SALE (POS) MODERN</div>
        <div class="toc-sub-row">5.1 Tata Letak Antarmuka Kasir Layar Sentuh & PC Split-Pane (/cashier/pos)</div>
        <div class="toc-sub-row">5.2 Navigasi Header Kasir & Perbedaan Tampilan Hak Akses Role</div>
        <div class="toc-sub-row">5.3 Metode Input Barang: Pemindai Barcode Laser vs Pencarian Teks</div>
        <div class="toc-sub-row">5.4 Manajemen Keranjang Belanja, Penyesuaian Kuantitas & Pembatalan Item</div>
        <div class="toc-sub-row">5.5 Penanganan Identitas Pelanggan (Pelanggan Umum vs Member Toko)</div>
        <div class="toc-sub-row">5.6 Alur Pembayaran Tunai (Cash) & Perhitungan Kembalian Otomatis</div>
        <div class="toc-sub-row">5.7 Alur Pembayaran Digital Dynamic QRIS DOKU & Webhook Real-Time</div>
        <div class="toc-sub-row">5.8 Asisten Suara (Audio Chime) Notifikasi Kasir Berhasil</div>
        <div class="toc-sub-row">5.9 Pintasan Keyboard Efisiensi Kasir (Hotkeys B, ESC, ENTER)</div>
        <div class="toc-sub-row">5.10 Cetak Struk Thermal Bluetooth (58mm/80mm) & Format Raw ESC/POS</div>
        <div class="toc-sub-row">5.11 Cetak Nota Faktur PDF Grayscale Monokrom (/receipt/{id}/print)</div>
        <div class="toc-sub-row">5.12 Pengiriman Bukti Pembayaran Digital via WhatsApp Pelanggan</div>
        <div class="toc-sub-row">5.13 Modul Ekspedisi: Pembuatan & Pencetakan Label Resi Paket Standar A6</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 6: PUSAT ANALITIK, 4 BUKU LAPORAN & REKONSILIASI KEUANGAN</div>
        <div class="toc-sub-row">6.1 Gambaran Umum Pusat Pelaporan Terpadu (/admin/reports)</div>
        <div class="toc-sub-row">6.2 Laporan Penjualan Transaksi (Filter Harian, Bulanan, Kuartal, Tahunan)</div>
        <div class="toc-sub-row">6.3 Laporan Keuangan & Arus Kas (Pemisahan Kas Tunai vs Netto QRIS)</div>
        <div class="toc-sub-row">6.4 Rekonsiliasi Biaya Potongan MDR DOKU Merchant 0.7% & Arus Kas Bersih</div>
        <div class="toc-sub-row">6.5 Laporan Monitoring & Audit Transaksi Digital QRIS (LQRS-Report)</div>
        <div class="toc-sub-row">6.6 Laporan Inventaris Gudang, Mutasi & Valuasi Total Aset Barang (LSTK-Report)</div>
        <div class="toc-sub-row">6.7 Pencetakan Struk Ringkasan Tutup Kasir Harian (Daily Closing Slip)</div>
        <div class="toc-sub-row">6.8 Standar Ekspor Laporan Formal ke Format PDF Landscape & Excel (.xlsx)</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 7: TANDA TANGAN ELEKTRONIK (TTE) & SERTIFIKASI DIGITAL</div>
        <div class="toc-sub-row">7.1 Landasan Hukum UU ITE No. 11/2008 & Integritas Dokumen Digital</div>
        <div class="toc-sub-row">7.2 Standar Kriptografi SHA-256 & Timestamp Keabsahan Dokumen</div>
        <div class="toc-sub-row">7.3 Struktur Format Penomoran Dokumen Laporan Dinamis (LKEU/LPK/LSTK)</div>
        <div class="toc-sub-row">7.4 Penandatangan Dokumen Otomatis Berdasarkan Akun & Gelar Alias TTE</div>
        <div class="toc-sub-row">7.5 Portal Publik Verifikasi Keaslian Dokumen Laporan (/verify/document)</div>
        <div class="toc-sub-row">7.6 Portal Publik Verifikasi Keaslian Faktur Pelanggan (/verify/tte/{trx})</div>
        <div class="toc-sub-row">7.7 Tautan Faktur Digital Sementara Bertanda Tangan (Expired 24 Jam)</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 8: PENGATURAN TOKO, KONFIGURASI & INTEGRASI SISTEM</div>
        <div class="toc-sub-row">8.1 Pusat Pengaturan Sistem Terpadu (/admin/settings)</div>
        <div class="toc-sub-row">8.2 Personalisasi Identitas Bisnis, Nama Usaha, Alamat & Logo Toko</div>
        <div class="toc-sub-row">8.3 Kustomisasi Branding Aplikasi, Prefix Invoice & Favicon Browser</div>
        <div class="toc-sub-row">8.4 Pengaturan & Pengunggahan Suara Bel Kasir (Audio Chime MP3/WAV)</div>
        <div class="toc-sub-row">8.5 Konfigurasi Gateway DOKU Merchant (Sandbox & Production)</div>
        <div class="toc-sub-row">8.6 Pengaturan Footer Struk Kasir & Kebijakan Toko</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 9: MANAJEMEN PENGGUNA & GELAR JABATAN (ALIAS TTE)</div>
        <div class="toc-sub-row">9.1 Daftar Akun Pengguna (/admin/users)</div>
        <div class="toc-sub-row">9.2 Pendaftaran Akun Kasir / Admin Baru & Hak Wewenang Role</div>
        <div class="toc-sub-row">9.3 Konfigurasi Kolom "Alias / Gelar Jabatan TTD" Penandatangan Dokumen</div>
        <div class="toc-sub-row">9.4 Pembaruan Data Pengguna & Prosedur Reset Kata Sandi</div>
    </div>

    <div class="toc-chapter-row">
        <div class="toc-chapter-title">BAB 10: PEMELIHARAAN SERVER EKSKLUSIF DIKELOLA OLEH I GUSTI SULTAN</div>
        <div class="toc-sub-row">10.1 Arsitektur Server Produksi & Manajemen Terpusat oleh I Gusti Sultan</div>
        <div class="toc-sub-row">10.2 Dedicated Media Streaming Engine (/media-file) Anti 404 Nginx</div>
        <div class="toc-sub-row">10.3 Prosedur Pemeliharaan Storage Symlink & File System aaPanel</div>
        <div class="toc-sub-row">10.4 Konfigurasi Web Server Nginx & URL Rewrite Engine Laravel</div>
        <div class="toc-sub-row">10.5 Pemantauan Log Kesalahan Sistem (laravel.log) & Error Handling</div>
        <div class="toc-sub-row">10.6 Prosedur Pencadangan (Backup) Harian & Disaster Recovery oleh I Gusti Sultan</div>
        <div class="toc-sub-row">10.7 Standar Operasional Prosedur (SOP) Pemeliharaan Server Rutin oleh I Gusti Sultan</div>
        <div class="toc-sub-row">10.8 Panduan Pemecahan Masalah Cepat (Troubleshooting FAQ)</div>
        <div class="toc-sub-row">10.9 Glosarium Lengkap Istilah POS, Perbankan, Kriptografi & E-Commerce</div>
        <div class="toc-sub-row">10.10 Lembar Kontak Dukungan Teknis Langsung I Gusti Sultan</div>
    </div>

    {{-- ========================================================================= --}}
    {{-- BAB 1: GAMBARAN UMUM & ARSITEKTUR TEKNOLOGI --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 01</div>
    <h1 class="chapter-title">GAMBARAN UMUM & ARSITEKTUR TEKNOLOGI SISTEM</h1>

    <h2 class="section-title">1.1 Latar Belakang & Filosofi Rancangan Sistem Kasir Modern</h2>
    <p>
        Sistem Point of Sale (POS) dan Administrasi Terpadu ini dirancang untuk menjawab kebutuhan operasional bisnis ritel kontemporer yang menuntut kecepatan tinggi dalam melayani pelanggan di meja kasir (*checkout speed*), akurasi pembukuan tanpa celah selisih, transparansi arus kas real-time, serta otomatisasi pelaporan manajerial yang akuntabel.
    </p>
    <p>
        Filosofi utama dari sistem ini adalah **Reaktivitas Satu Layar (*One-Screen POS Workflow*)**, di mana petugas kasir dapat memindai barcode barang, menyesuaikan kuantitas, memilih identitas pelanggan, memproses pembayaran tunai maupun non-tunai, mencetak struk thermal, hingga mengirim bukti transaksi elektronik melalui WhatsApp dalam satu tampilan antarmuka terpadu tanpa perlu berpindah-pindah tab peramban.
    </p>

    <h2 class="section-title">1.2 Tumpukan Teknologi (Technology Stack) & Arsitektur MVC</h2>
    <p>
        Sistem dibangun di atas fondasi arsitektur Model-View-Controller (MVC) yang kokoh, modular, dan teruji pada skala produksi tinggi. Setiap lapisan arsitektur dioptimalkan untuk menghasilkan performa komputasi terbaik:
    </p>
    <ul>
        <li><b>Backend Framework:</b> Laravel 11.x (PHP 8.2+) dengan arsitektur RESTful controller, Eloquent ORM untuk manajemen relasi basis data yang aman dari SQL Injection, serta sistem autentikasi session-based yang terlindungi token CSRF.</li>
        <li><b>Frontend & Reaktivitas:</b> Tailwind CSS 3.x untuk tata letak antarmuka modern bernuansa hijau Gojek yang responsif, dikombinasikan dengan Alpine.js 3.x untuk menangani kalkulasi keranjang belanja, kembalian tunai, dan modal QRIS secara reaktif di sisi peramban kasir tanpa membebani server.</li>
        <li><b>Basis Data:</b> MySQL 8.0 Enterprise Relational Database yang mengimplementasikan indeks integritas referensial dan transaksi ACID untuk mencegah data korup saat ribuan transaksi kasir terjadi secara simultan.</li>
        <li><b>Mesin Dokumen & PDF:</b> DomPDF Engine yang dikonfigurasi khusus dengan resolusi vektor tajam untuk menghasilkan cetakan struk thermal (58mm/80mm), nota faktur A4/A5, dan laporan keuangan manajerial bertanda tangan digital.</li>
        <li><b>Infrastruktur Server:</b> Dikelola secara terpusat pada sistem operasi Linux berbasis host aaPanel oleh <b>I Gusti Sultan</b> dengan konfigurasi Nginx reverse proxy dan PHP-FPM socket berkecepatan tinggi.</li>
    </ul>

    <h2 class="section-title">1.3 Matriks Hak Akses Pengguna (Admin vs Kasir)</h2>
    <p>
        Sistem menerapkan prinsip *Role-Based Access Control (RBAC)* yang membagi wewenang pengguna ke dalam dua peran berbeda untuk mencegah manipulasi data finansial:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 30%;">Fitur & Modul Sistem</th>
            <th style="width: 35%;">Role: Administrator</th>
            <th style="width: 35%;">Role: Petugas Kasir</th>
        </tr>
        <tr>
            <td><b>Akses Meja Kasir POS</b></td>
            <td>Akses Penuh (Termasuk Tombol Kembali)</td>
            <td>Akses Penuh (Hanya Transaksi & Logout)</td>
        </tr>
        <tr>
            <td><b>Dashboard Utama & Analitik</b></td>
            <td>Akses Penuh (Omset, Grafik, KPI)</td>
            <td>Akses Ditolak (HTTP 403 Forbidden)</td>
        </tr>
        <tr>
            <td><b>Katalog Produk & Stok</b></td>
            <td>Tambah, Edit, Hapus, Impor Excel, Barcode</td>
            <td>Hanya Melihat Stok Melalui Kasir</td>
        </tr>
        <tr>
            <td><b>Pusat Laporan Keuangan</b></td>
            <td>Akses 4 Buku Laporan & Ekspor PDF/Excel</td>
            <td>Akses Ditolak</td>
        </tr>
        <tr>
            <td><b>Pengaturan Toko & Gateway</b></td>
            <td>Akses Penuh (Identitas, Audio, DOKU, Bot)</td>
            <td>Akses Ditolak</td>
        </tr>
        <tr>
            <td><b>Manajemen Pengguna & Gelar TTE</b></td>
            <td>Akses Penuh (Tambah User, Reset Password)</td>
            <td>Hanya Edit Profil Sendiri (/profile)</td>
        </tr>
    </table>

    <h2 class="section-title">1.4 Integrasi Layanan Pembayaran Digital Dynamic QRIS DOKU Merchant</h2>
    <p>
        Sistem mengintegrasikan Application Programming Interface (API) resmi dari DOKU Payment Gateway untuk menerbitkan kode QRIS Dinamis standar Bank Indonesia (ASPCI). Setiap kali transaksi kasir memilih metode QRIS, sistem mengirimkan permintaan HTTP aman ke gateway DOKU dengan menyertakan *Client-ID*, *Secret Key*, dan *Nominal Tagihan Eksak*. DOKU mengembalikan string kode QRIS yang langsung dirender di layar kasir. Saat pembeli memindai dan membayar melalui GoPay, OVO, Dana, ShopeePay, BCA Mobile, atau perbankan manapun, server DOKU mengirimkan sinyal Webhook instan ke server kasir untuk menyelesaikan transaksi tanpa perlu kasir menekan tombol apapun.
    </p>

    <h2 class="section-title">1.5 Landasan Hukum & Validitas Tanda Tangan Elektronik (UU ITE No. 11/2008)</h2>
    <p>
        Seluruh dokumen laporan formal yang diterbitkan oleh sistem dilengkapi dengan stempel Tanda Tangan Elektronik (TTE) berbasis kriptografi SHA-256. Berdasarkan <b>Pasal 11 Undang-Undang Republik Indonesia Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik (UU ITE)</b>, Tanda Tangan Elektronik memiliki kekuatan hukum dan akibat hukum yang sah selama memenuhi persyaratan keaslian identitas penandatangan dan integritas data yang tidak dapat dimanipulasi pasca-penandatanganan.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 2: MANAJEMEN OTENTIKASI & KEAMANAN AKUN --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 02</div>
    <h1 class="chapter-title">MANAJEMEN OTENTIKASI & KEAMANAN AKUN</h1>

    <h2 class="section-title">2.1 Akses Masuk Sistem (Halaman Login /login) & Tata Letak Antarmuka</h2>
    <p>
        Halaman login (<code>/login</code>) merupakan pintu gerbang keamanan utama ke seluruh ekosistem kasir. Antarmuka login dirancang bersih, profesional, dan terpusat dengan logo toko dinamis, kolom alamat surel (*email*), kata sandi (*password*), kotak centang *Ingat Saya* (*Remember Me*), tombol *Masuk Sistem*, serta footer hak cipta resmi oleh <b>I Gusti Sultan</b>.
    </p>

    <h2 class="section-title">2.2 Anatomi Elemen Formulir Login & Logika Smart Redirect</h2>
    <p>
        Proses otentikasi dikendalikan oleh <code>AuthenticatedSessionController</code> yang dilengkapi mekanisme *Smart Role-Based Redirection*:
    </p>
    <ul>
        <li><b>Validasi Masukan:</b> Sistem memeriksa format email standar RFC dan memastikan panjang kata sandi memenuhi kriteria keamanan.</li>
        <li><b>Smart Redirection:</b> Jika pengguna yang berhasil masuk memiliki role <code>cashier</code>, sistem secara otomatis mengarahkan peramban ke rute kasir (<code>/cashier/pos</code>). Jika pengguna memiliki role <code>admin</code>, peramban langsung diarahkan ke panel komando (<code>/dashboard</code>).</li>
        <li><b>Pencegahan Duplikasi Login:</b> Jika pengguna yang sudah memiliki sesi aktif mencoba membuka halaman <code>/login</code>, sistem otomatis mengalihkannya ke halaman kerja sesuai role tanpa meminta kredensial ulang.</li>
    </ul>

    <h2 class="section-title">2.3 Proteksi Formulir CSRF, Enkripsi Bcrypt & Proteksi Brute Force</h2>
    <p>
        Keamanan akun dilindungi oleh tiga lapisan proteksi standar industri:
    </p>
    <div class="step-box">
        <div class="step-header"><span class="step-num">1</span> Proteksi Cross-Site Request Forgery (CSRF)</div>
        Setiap formulir masukan menyertakan directiva <code>@csrf</code> yang menghasilkan token acak terenkripsi 40 karakter. Token ini diverifikasi oleh middleware server pada setiap permintaan POST. Jika permintaan berasal dari pihak ketiga atau token kedaluwarsa, server mengembalikan respon HTTP 419 (*Page Expired*).
    </div>
    <div class="step-box">
        <div class="step-header"><span class="step-num">2</span> Enkripsi Kata Sandi Bcrypt Hashing (Cost 12)</div>
        Kata sandi pengguna tidak pernah disimpan dalam format teks biasa (*plaintext*). Sistem menggunakan algoritma adaptif *Bcrypt* dengan *salt* acak unik per pengguna dan faktor kerja *Cost 12*, memastikan kata sandi mustahil dibongkar bahkan jika basis data diekspor.
    </div>
    <div class="step-box">
        <div class="step-header"><span class="step-num">3</span> Pembatasan Percobaan Masuk (Rate Limiting)</div>
        Sistem membatasi percobaan masuk maksimal 5 kali berturut-turut dalam kurun waktu 1 menit per alamat IP. Jika ambang batas terlampaui, sistem mengunci akses selama 60 detik untuk memitigasi serangan brute force kamus kata sandi.
    </div>

    <h2 class="section-title">2.4 Manajemen Profil Mandiri & Prosedur Pergantian Kata Sandi (/profile)</h2>
    <p>
        Setiap pengguna, baik kasir maupun admin, dapat memperbarui identitas profil dan mengganti kata sandi secara mandiri pada rute <code>/profile</code>. Prosedur pergantian sandi mewajibkan input kata sandi lama sebagai verifikasi kepemilikan sah sebelum sistem mengizinkan penyimpanan kata sandi baru.
    </p>

    <h2 class="section-title">2.5 Prosedur Keluar Aman (Logout) & Keamanan Sesi Komputer Kasir</h2>
    <p>
        Saat pergantian shift kerja kasir atau penutupan operasional harian toko, petugas wajib menjalankan prosedur keluar aman dengan mengklik tombol **Logout (Keluar)** berwarna merah. Server akan menghancurkan cookie sesi pada peramban, membatalkan token autentikasi di tabel sesi, membersihkan memori keranjang lokal, dan mengunci peramban kembali ke tampilan login.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 3: PUSAT KOMANDO & DASHBOARD ADMINISTRATOR --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 03</div>
    <h1 class="chapter-title">PUSAT KOMANDO & DASHBOARD ADMINISTRATOR</h1>

    <h2 class="section-title">3.1 Antarmuka Panel Kontrol Administrator (/dashboard)</h2>
    <p>
        Panel kontrol Administrator (<code>/dashboard</code>) menyajikan ikhtisar performa bisnis toko secara komprehensif. Dirancang dengan tata letak modern yang intuitif, dashboard menampilkan ringkasan arus kas harian, volume penjualan, kartu indikator kinerja utama (*KPI*), grafik tren 7 hari, dan akses navigasi cepat ke seluruh modul administratif.
    </p>

    <h2 class="section-title">3.2 Banner Omset Hijau Gojek Pocket & Tombol Aksi Cepat (Quick Actions)</h2>
    <p>
        Elemen paling menonjol pada bagian atas dashboard adalah **Banner Omset Hijau Gojek Pocket (#00661A)**. Banner ini menampilkan:
    </p>
    <ul>
        <li><b>Total Omset Hari Ini (Rupiah):</b> Angka nominal pendapatan kotor yang terkumpul dari seluruh transaksi tunai dan non-tunai pada hari berjalan secara real-time.</li>
        <li><b>Volume Transaksi:</b> Jumlah transaksi yang berhasil diselesaikan dan total kuantitas produk yang terjual hari ini.</li>
        <li><b>Tombol Aksi Cepat (Quick Actions):</b> Pintasan langsung menuju *Buka Kasir POS*, *+ Tambah Produk Baru*, *Laporan Penjualan*, *Laporan Keuangan*, dan *Unduh Buku Panduan PDF*.</li>
    </ul>

    <h2 class="section-title">3.3 Analitik 4 Indikator Kinerja Utama (KPI) Finansial Real-Time</h2>
    <p>
        Di bawah banner utama, terdapat empat kartu indikator performa yang diperbarui secara otomatis setiap kali ada transaksi baru:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 25%;">Kartu KPI</th>
            <th style="width: 25%;">Sumber Data</th>
            <th>Fungsi Analitik & Nilai Informasi</th>
        </tr>
        <tr>
            <td><b>Omset Hari Ini</b></td>
            <td>Tabel <code>sales</code> (SUM total_price hari ini)</td>
            <td>Mengetahui likuiditas harian dan pencapaian target penjualan toko hari ini.</td>
        </tr>
        <tr>
            <td><b>Total Transaksi</b></td>
            <td>Tabel <code>sales</code> (COUNT id hari ini)</td>
            <td>Mengukur intensitas pelanggan dan kepadatan antrean meja kasir.</td>
        </tr>
        <tr>
            <td><b>Item Terjual</b></td>
            <td>Tabel <code>sale_items</code> (SUM quantity hari ini)</td>
            <td>Memantau perputaran barang fisik (*inventory turnover*) dari gudang.</td>
        </tr>
        <tr>
            <td><b>Status Operasional</b></td>
            <td>Database & Gateway Health Check</td>
            <td>Menunjukkan status online koneksi server, basis data, dan DOKU gateway.</td>
        </tr>
    </table>

    <h2 class="section-title">3.4 Visualisasi Grafik Tren Arus Kas & Analisis Penjualan 7 Hari Terakhir</h2>
    <p>
        Dashboard menyajikan visualisasi grafik batang komparatif selama 7 hari kalender terakhir. Grafik ini memecah pendapatan harian menjadi dua batang warna terpisah: **Warna Hijau (#00AA13)** untuk penerimaan uang kas tunai dan **Warna Biru (#00AED6)** untuk penerimaan uang digital Dynamic QRIS DOKU. Grafik ini memudahkan pimpinan toko dalam melihat tren belanja akhir pekan (*weekend peak*) dan pola adopsi pembayaran digital oleh pelanggan.
    </p>

    <h2 class="section-title">3.5 Struktur Navigasi Sidebar Responsif & Copyright I Gusti Sultan</h2>
    <p>
        Bilah navigasi samping (*Sidebar*) menggunakan skema warna hijau tua Gojek (#00360D) yang elegan dan terstruktur secara hierarkis:
    </p>
    <ul>
        <li><b>Header Sidebar:</b> Logo toko dan nama resmi aplikasi (<code>{{ $shop['app_name'] ?? 'SIKANDA' }} POS</code>).</li>
        <li><b>Menu Utama:</b> *Dashboard Ringkasan*, *Katalog Produk & Stok*, *Laporan Penjualan*, *Laporan Keuangan & Kas*, *Manajemen Pengguna*, dan *Pengaturan Toko*.</li>
        <li><b>Menu Panduan:</b> *Buku Panduan PDF* untuk membuka viewer dan mengekspor dokumen SOP resmi ini.</li>
        <li><b>Footer Sidebar:</b> Pernyataan lisensi resmi: <code>&copy; {{ date('Y') }} I Gusti Sultan. All rights reserved.</code></li>
    </ul>

    {{-- ========================================================================= --}}
    {{-- BAB 4: MANAJEMEN INVENTARIS PRODUK & PERGUDANGAN --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 04</div>
    <h1 class="chapter-title">MANAJEMEN INVENTARIS PRODUK & PERGUDANGAN</h1>

    <h2 class="section-title">4.1 Master Katalog Produk (/admin/products) & Indikator Ketersediaan</h2>
    <p>
        Modul Katalog Produk (<code>/admin/products</code>) mengelola seluruh master data barang dagangan toko. Tabel master menampilkan informasi lengkap mencakup kode barcode, nama produk, harga jual kasir, diskon promo, stok fisik di gudang, serta tombol tindakan operasional.
    </p>

    <h2 class="section-title">4.2 Fitur Pencarian Cepat Nama Barang & Filter Barcode</h2>
    <p>
        Tabel katalog produk dilengkapi kotak pencarian cerdas yang merespon input secara instan. Administrator dapat mengetikkan sebagian nama produk (misalnya: *Minyak*) atau langsung memindai barcode menggunakan scanner laser. Sistem menyaring daftar produk secara real-time tanpa memuat ulang seluruh halaman peramban.
    </p>

    <h2 class="section-title">4.3 Formulir Tambah Produk Baru (/admin/products/create) & Barcode Auto Generator</h2>
    <p>
        Penambahan produk baru dilakukan melalui formulir <code>/admin/products/create</code> dengan parameter berikut:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 25%;">Kolom Input</th>
            <th style="width: 25%;">Karakteristik Validasi</th>
            <th>Keterangan Operasional</th>
        </tr>
        <tr>
            <td><b>Nama Produk (*)</b></td>
            <td><code>required|string|max:255</code></td>
            <td>Nama lengkap komoditas barang dagangan (contoh: *Kopi Susu Aren 250ml*).</td>
        </tr>
        <tr>
            <td><b>Barcode / SKU</b></td>
            <td><code>nullable|unique:products</code></td>
            <td>Nomor barcode unik produk. Jika barang tidak memiliki barcode pabrik, tekan tombol <b>AUTO ⚡</b> untuk membuat kode EAN-13 acak unik secara otomatis.</td>
        </tr>
        <tr>
            <td><b>Harga Jual (Rp) (*)</b></td>
            <td><code>required|numeric|min:0</code></td>
            <td>Harga jual eceran di kasir sebelum dikenakan diskon promosi.</td>
        </tr>
        <tr>
            <td><b>Stok Fisik Awal (*)</b></td>
            <td><code>required|integer|min:0</code></td>
            <td>Jumlah unit fisik barang yang tersedia di gudang atau rak pajangan toko.</td>
        </tr>
        <tr>
            <td><b>Diskon Promo (%)</b></td>
            <td><code>nullable|numeric|min:0|max:100</code></td>
            <td>Persentase diskon promosi (0 - 100%). Kasir otomatis memotong harga saat ditransaksikan.</td>
        </tr>
        <tr>
            <td><b>Keterangan / Satuan</b></td>
            <td><code>nullable|string|max:1000</code></td>
            <td>Satuan kemasan barang (Pcs, Botol, Pack, Dus, Kg, Liter) atau deskripsi singkat.</td>
        </tr>
    </table>

    <h2 class="section-title">4.4 Struktur Penetapan Harga Jual Kasir, Diskon Promo (%) & Satuan Kemasan</h2>
    <p>
        Sistem menghitung harga kasir (*net sales price*) menggunakan formula:
    </p>
    <div style="background-color: #F7FAFC; border: 1px solid #E2E8F0; padding: 6px 10px; border-radius: 4px; font-family: monospace; font-size: 8pt; margin: 4px 0;">
        Harga_Final_Kasir = Harga_Jual - (Harga_Jual &times; (Diskon_Percent / 100))
    </div>
    <p>
        Harga final ini yang akan dicatat pada nota penjualan dan struk thermal pembeli.
    </p>

    <h2 class="section-title">4.5 Tiga Tingkat Status Stok: Aman (Hijau), Menipis (Kuning), Habis (Merah)</h2>
    <p>
        Untuk mempermudah manajemen logistik, sistem mengklasifikasikan status ketersediaan barang ke dalam tiga badge warna:
    </p>
    <ul>
        <li><b style="color: #00880F;">Badge Hijau (STOK AMAN):</b> Kuantitas stok di atas 5 unit (&gt; 5). Barang dalam kondisi aman untuk transaksi kasir normal.</li>
        <li><b style="color: #DD6B20;">Badge Kuning (STOK MENIPIS):</b> Kuantitas stok tersisa antara 1 hingga 5 unit (1 - 5). Memberikan peringatan kepada petugas gudang untuk segera melakukan pemesanan ulang (*re-order*).</li>
        <li><b style="color: #E53E3E;">Badge Merah (STOK HABIS):</b> Kuantitas stok bernilai 0 unit. Sistem kasir secara otomatis mengunci dan menolak penambahan barang ini ke keranjang belanja guna mencegah penjualan fiktif (*overselling*).</li>
    </ul>

    <h2 class="section-title">4.6 Fitur Update Stok Cepat via Scanner Barcode di Katalog</h2>
    <p>
        Petugas gudang dapat melakukan penambahan stok barang masuk (*stock in*) tanpa harus membuka formulir edit satu per satu. Pada bagian atas halaman katalog produk, tersedia kotak input cepat: cukup pindai barcode barang masuk, ketikkan jumlah kuantitas tambahan, dan tekan **ENTER**. Stok produk langsung bertambah secara instan melalui permintaan AJAX.
    </p>

    <h2 class="section-title">4.7 Impor Data Produk Massal Melalui Spreadsheet Excel (.xlsx)</h2>
    <p>
        Untuk pengisian awal ratusan produk toko baru, sistem menyediakan fitur impor massal via berkas Excel (<code>/admin/products/import</code>). Berkas Excel harus memiliki 4 kolom berurutan: **Kolom A (nama)**, **Kolom B (barcode)**, **Kolom C (harga)**, dan **Kolom D (stok)**. Mesin impor memproses data secara berurutan dan memberikan laporan jumlah produk yang berhasil didaftarkan.
    </p>

    <h2 class="section-title">4.8 Pemeliharaan Data, Edit Barang, Snapshot Pricing & Penghapusan Aman</h2>
    <p>
        Jika data harga atau nama produk diubah, transaksi kasir yang terjadi sebelum perubahan tetap mempertahankan nilai harga historis aslinya (*Price Snapshotting*). Selain itu, sistem menerapkan mekanisme penghapusan aman (*Safe Deletion*): produk yang sudah memiliki riwayat transaksi dilindungi integritasnya sehingga laporan pembukuan masa lalu tidak mengalami selisih atau error relasi basis data.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 5: OPERASIONAL KASIR POINT OF SALE (POS) MODERN --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 05</div>
    <h1 class="chapter-title">OPERASIONAL KASIR POINT OF SALE (POS) MODERN</h1>

    <h2 class="section-title">5.1 Tata Letak Antarmuka Kasir Layar Sentuh & PC Split-Pane (/cashier/pos)</h2>
    <p>
        Antarmuka kasir Point of Sale (<code>/cashier/pos</code>) dirancang dengan tata letak dua panel terpisah (*Split-Pane Layout*) yang dioptimalkan untuk monitor layar sentuh maupun keyboard desktop:
    </p>
    <ul>
        <li><b>Panel Kiri (Keranjang Belanja):</b> Memuat kotak pemindaian barcode, daftar item yang sedang dibeli, tombol pengubah kuantitas (+/-), tombol hapus item, rincian diskon, dan subtotal harga per baris.</li>
        <li><b>Panel Kanan (Dompet Pembayaran):</b> Bergaya kartu saku hijau tua Gojek (#00661A) yang memuat input nama pelanggan, ringkasan nominal total tagihan berukuran besar, pilihan metode pembayaran (Tunai vs QRIS), serta tombol utama **PROSES BAYAR (B)**.</li>
    </ul>

    <h2 class="section-title">5.2 Navigasi Header Kasir & Perbedaan Tampilan Hak Akses Role</h2>
    <p>
        Header kasir secara dinamis menyesuaikan role pengguna yang sedang login:
    </p>
    <ul>
        <li><b>Jika Login sebagai Administrator:</b> Header menampilkan tombol **"← Dashboard Admin"** berwarna gelap, memungkinkan pimpinan toko berpindah dari meja kasir ke panel manajemen tanpa harus logout.</li>
        <li><b>Jika Login sebagai Petugas Kasir:</b> Tombol dashboard disembunyikan. Header hanya menampilkan nama kasir bertugas (contoh: *Kasir: Siti Rahma*) dan tombol **"Logout"** merah untuk mengakhiri shift.</li>
    </ul>

    <h2 class="section-title">5.3 Metode Input Barang: Pemindai Barcode Laser vs Pencarian Teks</h2>
    <p>
        Kasir dapat memasukkan barang ke dalam keranjang belanja melalui dua cara:
    </p>
    <div class="step-box">
        <div class="step-header"><span class="step-num">A</span> Pemindaian Barcode Laser (Otomatis & Cepat)</div>
        Arahkan pemindai laser barcode ke label produk. Pemindai secara otomatis membaca kode, memasukkan nilai ke kolom pencarian, dan menekan simulasi 'ENTER'. Barang langsung masuk ke keranjang belanja dalam waktu kurang dari 0.2 detik. Pemindaian ulang pada barang yang sama akan otomatis menambah kuantitas (+1).
    </div>
    <div class="step-box">
        <div class="step-header"><span class="step-num">B</span> Pencarian Teks / Nama Barang Manual</div>
        Ketik minimal 2 huruf nama produk pada kotak input (contoh: *Bimoli*). Dropdown saran produk yang cocok akan muncul di bawah kotak pencarian. Kasir dapat mengklik produk yang diinginkan atau menekan tombol panah bawah keyboard lalu tekan 'ENTER'.
    </div>

    <h2 class="section-title">5.4 Manajemen Keranjang Belanja, Penyesuaian Kuantitas & Pembatalan Item</h2>
    <p>
        Pada setiap baris produk di dalam keranjang belanja, kasir dapat:
    </p>
    <ul>
        <li>Menekan tombol <b>[ + ]</b> untuk menambah kuantitas barang (+1 unit). Sistem otomatis memvalidasi sisa stok gudang agar tidak melebihi batas fisik.</li>
        <li>Menekan tombol <b>[ &minus; ]</b> untuk mengurangi kuantitas barang (&minus;1 unit). Jika kuantitas mencapai 0, item otomatis dihapus dari keranjang.</li>
        <li>Menekan tombol ikon tempat sampah merah <b>[ 🗑 ]</b> untuk membatalkan dan menghapus baris produk tersebut dari keranjang secara langsung.</li>
    </ul>

    <h2 class="section-title">5.5 Penanganan Identitas Pelanggan (Pelanggan Umum vs Member Toko)</h2>
    <p>
        Secara default, kolom nama pelanggan terisi otomatis dengan teks **"Pelanggan Umum"** untuk menghemat waktu saat antrean padat. Namun, jika pembeli adalah pelanggan langganan atau menginginkan pengiriman struk digital via WhatsApp, kasir dapat mengetikkan nama pelanggan (misalnya: *Ibu Dewi*) dan nomor teleponnya. Nama ini akan dicetak pada struk belanja dan disertifikasi dalam stempel digital TTE.
    </p>

    <h2 class="section-title">5.6 Alur Pembayaran Tunai (Cash) & Perhitungan Kembalian Otomatis</h2>
    <p>
        Jika pembeli memilih pembayaran tunai, kasir menekan tombol **Bayar Tunai** (atau tombol pintas **B**). Dialog popup muncul menampilkan total tagihan, kolom input uang yang diterima, tombol nominal uang pas, serta tombol pecahan cepat (Rp 50.000, Rp 100.000, Rp 200.000). Alpine.js secara otomatis menghitung nilai **Uang Kembalian** secara instan. Kasir menekan **ENTER** untuk menyimpan transaksi dan membuka laci kasir (*cash drawer*).
    </p>

    <h2 class="section-title">5.7 Alur Pembayaran Digital Dynamic QRIS DOKU & Webhook Real-Time</h2>
    <p>
        Jika pembeli memilih pembayaran non-tunai, kasir menekan tombol **Dynamic QRIS DOKU**. Sistem melakukan panggilan API ke DOKU dan menampilkan modal dialog dengan kode QRIS standar Bank Indonesia beserta nominal eksak dan hitung mundur batas waktu (5 menit). Begitu pembeli berhasil melakukan transfer scan QR melalui aplikasi perbankan atau dompet digital (GoPay, OVO, Dana, BCA), webhook DOKU secara otomatis memicu pelunasan di layar kasir, membunyikan bel audio sukses, dan mencetak struk belanja.
    </p>

    <h2 class="section-title">5.8 Asisten Suara (Audio Chime) Notifikasi Kasir Berhasil</h2>
    <p>
        Sistem dilengkapi dengan asisten audio lonceng bel (*Audio Chime Effect*). Setiap kali transaksi kasir berhasil diselesaikan—baik melalui pembayaran tunai maupun konfirmasi otomatis QRIS—sistem memutar efek suara lonceng yang jernih. Hal ini memberikan konfirmasi auditif langsung kepada kasir dan pembeli tanpa kasir harus menatap layar secara terus-menerus, serta mencegah penipuan screenshot struk palsu.
    </p>

    <h2 class="section-title">5.9 Pintasan Keyboard Efisiensi Kasir (Hotkeys B, ESC, ENTER)</h2>
    <p>
        Untuk meningkatkan kecepatan checkout kasir profesional, sistem mendukung hotkeys keyboard:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 25%;">Tombol Keyboard</th>
            <th style="width: 30%;">Aksi Sistem</th>
            <th>Fungsi Operasional</th>
        </tr>
        <tr>
            <td><span class="badge-shortcut">B</span></td>
            <td>Buka Modal Pembayaran</td>
            <td>Membuka dialog popup pembayaran kasir secara instan.</td>
        </tr>
        <tr>
            <td><span class="badge-shortcut">ESC</span></td>
            <td>Batal / Tutup Dialog</td>
            <td>Menutup dialog modal yang sedang terbuka atau membatalkan input.</td>
        </tr>
        <tr>
            <td><span class="badge-shortcut">ENTER</span></td>
            <td>Konfirmasi / Simpan Transaksi</td>
            <td>Memfinalisasi pembayaran, menyimpan ke database & cetak struk.</td>
        </tr>
    </table>

    <h2 class="section-title">5.10 Format Cetak Struk Thermal Bluetooth (58mm/80mm) & Format Raw ESC/POS</h2>
    <p>
        Sistem mendukung pencetakan langsung ke printer kasir thermal mini melalui koneksi Bluetooth maupun kabel USB menggunakan format perintah raw ESC/POS standar industri. Tata letak struk dirancang presisi dengan lebar 32 kolom karakter untuk kertas 58mm dan 48 kolom karakter untuk kertas 80mm. Struk memuat header nama toko, alamat, nomor invoice, nama kasir, daftar item dan kuantitas, total, bayar, kembalian, dan catatan kaki (*footer policy*).
    </p>

    <h2 class="section-title">5.11 Cetak Nota Faktur PDF Grayscale Monokrom (/receipt/{id}/print)</h2>
    <p>
        Untuk pelanggan perusahaan atau transaksi bernilai besar yang memerlukan bukti fisik formal, kasir dapat mencetak **Nota Faktur PDF Grayscale Monokrom** ukuran A4 atau A5. Dokumen ini menggunakan latar belakang putih murni yang bersih, garis batas formal, rincian produk bertabel rapi, serta stempel Tanda Tangan Elektronik (TTE) resmi kasir bertugas.
    </p>

    <h2 class="section-title">5.12 Pengiriman Bukti Pembayaran Digital via WhatsApp Pelanggan</h2>
    <p>
        Sistem menyediakan fitur pengiriman struk digital (*e-receipt*) nir-kertas (*paperless*). Setelah transaksi selesai, kasir dapat memasukkan nomor WhatsApp pembeli dan menekan tombol **Kirim Struk (WA)**. Sistem secara otomatis membuka tautan WhatsApp Web atau WhatsApp App dengan draf pesan sopan berisi salam, nama toko, ringkasan total belanja, dan tautan e-faktur digital resmi bertanda tangan elektronik.
    </p>

    <h2 class="section-title">5.13 Modul Ekspedisi: Pembuatan & Pencetakan Label Resi Paket Standar A6</h2>
    <p>
        Untuk toko yang melayani pengiriman barang jarak jauh atau pesanan online, sistem dilengkapi dengan generator label resi paket ukuran standar A6 (105mm &times; 148mm) pada rute <code>/shipping/label/{sale_id}</code>. Label ini memuat kotak data penerima (TO) lengkap dengan nomor telepon, kotak data pengirim (FROM) identitas toko, nomor resi pengiriman berformat visual barcode, logo ekspedisi (JNE, J&T, SiCepat), serta peringatan **FRAGILE (Mudah Pecah)** yang jelas.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 6: PUSAT ANALITIK & 4 BUKU LAPORAN KEUANGAN --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 06</div>
    <h1 class="chapter-title">PUSAT ANALITIK & 4 BUKU LAPORAN KEUANGAN</h1>

    <h2 class="section-title">6.1 Gambaran Umum Pusat Pelaporan Terpadu (/admin/reports)</h2>
    <p>
        Pusat Pelaporan Terpadu (<code>/admin/reports</code>) menyajikan empat buku laporan bisnis terpisah yang saling terintegrasi secara otomatis:
    </p>
    <ul>
        <li><b>Buku 1 - Laporan Penjualan (LPK):</b> Merekap volume transaksi, waktu belanja, kasir pelaksana, dan rincian omset kotor.</li>
        <li><b>Buku 2 - Laporan Keuangan & Arus Kas (LKEU):</b> Memisahkan arus kas fisik di laci dengan kas digital perbankan serta laba operasional.</li>
        <li><b>Buku 3 - Laporan Monitoring QRIS (LQRS):</b> Mengaudit transaksi gateway DOKU, potongan biaya MDR, dan pencairan (*settlement*).</li>
        <li><b>Buku 4 - Laporan Stok & Valuasi Aset (LSTK):</b> Mengkalkulasi nilai total kekayaan barang dagangan yang tersimpan di gudang toko.</li>
    </ul>

    <h2 class="section-title">6.2 Laporan Penjualan Transaksi (Filter Harian, Bulanan, Kuartal, Tahunan)</h2>
    <p>
        Administrator dapat memfilter laporan penjualan berdasarkan rentang tanggal fleksibel: harian, mingguan, bulanan, kuartal (tiga bulanan), maupun tahunan. Filter juga dapat dipersempit berdasarkan metode pembayaran tertentu (Hanya Tunai atau Hanya QRIS) dan kasir bertugas. Panel ringkasan menampilkan total nominal transaksi kotor, rata-rata nilai keranjang (*average basket size*), dan status pelunasan.
    </p>

    <h2 class="section-title">6.3 Laporan Keuangan & Arus Kas (Pemisahan Kas Tunai vs Netto QRIS)</h2>
    <p>
        Guna mencegah selisih pembukuan antara uang fisik dan saldo rekening bank, Laporan Keuangan secara ketat memisahkan:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 30%;">Pos Keuangan</th>
            <th style="width: 35%;">Karakteristik Aliran Dana</th>
            <th style="width: 35%;">Prosedur Rekonsiliasi</th>
        </tr>
        <tr>
            <td><b>Kas Fisik di Laci (Cash)</b></td>
            <td>Uang kertas dan koin riil di laci kasir. Bebas biaya potongan MDR (0%).</td>
            <td>Dihitung manual (*cash count*) setiap penutupan shift dan dicocokkan dengan struk closing.</td>
        </tr>
        <tr>
            <td><b>Kas Digital Bank (Net QRIS)</b></td>
            <td>Dana masuk ke rekening merchant bank setelah dipotong biaya MDR 0.7%.</td>
            <td>Dicocokkan dengan mutasi rekening koran (*bank statement*) pada tanggal pencairan H+1.</td>
        </tr>
    </table>

    <h2 class="section-title">6.4 Rekonsiliasi Biaya Potongan MDR DOKU Merchant 0.7% & Arus Kas Bersih</h2>
    <p>
        Berdasarkan regulasi Bank Indonesia, transaksi QRIS Merchant dikenakan biaya Merchant Discount Rate (MDR) sebesar 0.7%. Sistem kasir secara otomatis mengkalkulasi pemotongan ini pada setiap transaksi QRIS:
    </p>
    <div style="background-color: #F7FAFC; border: 1px solid #E2E8F0; padding: 6px 10px; border-radius: 4px; font-family: monospace; font-size: 8pt; margin: 4px 0;">
        Biaya_MDR = Nominal_Bruto &times; 0.007<br>
        Pendapatan_Netto_Bank = Nominal_Bruto - Biaya_MDR
    </div>
    <p>
        Dengan demikian, laporan keuangan toko selalu mencerminkan nilai uang bersih (*net cash flow*) yang sebenarnya diterima oleh pemilik usaha tanpa ada selisih tersembunyi.
    </p>

    <h2 class="section-title">6.5 Laporan Monitoring & Audit Transaksi Digital QRIS (LQRS-Report)</h2>
    <p>
        Buku laporan audit QRIS mencatat ID referensi transaksi perbankan, waktu presisi webhook diterima, nominal bruto, nilai potongan MDR, dan status settlement perbankan. Laporan ini menjadi bukti audit resmi saat melakukan rekonsiliasi dengan pihak DOKU Merchant.
    </p>

    <h2 class="section-title">6.6 Laporan Inventaris Gudang, Mutasi & Valuasi Total Aset Barang (LSTK-Report)</h2>
    <p>
        Laporan Valuasi Stok mengalikan sisa unit fisik setiap barang dengan harga jualnya untuk menghitung total nilai aset gudang toko yang sedang aktif. Laporan ini juga mengelompokkan barang berdasarkan status ketersediaannya sehingga bagian pengadaan dapat memprioritaskan pembelian barang yang berada pada kategori stok menipis.
    </p>

    <h2 class="section-title">6.7 Pencetakan Struk Ringkasan Tutup Kasir Harian (Daily Closing Slip)</h2>
    <p>
        Saat pergantian shift atau penutupan toko pada malam hari, supervisor kasir dapat mencetak **Struk Ringkasan Tutup Kasir Harian (*Daily Closing Slip*)** melalui printer thermal. Struk ini merangkum total uang tunai yang wajib diserahkan, total penerimaan QRIS, total potongan fee, dan ditandatangani oleh kasir dan supervisor sebagai bukti serah terima kas resmi.
    </p>

    <h2 class="section-title">6.8 Standar Ekspor Laporan Formal ke Format PDF Landscape & Excel (.xlsx)</h2>
    <p>
        Seluruh buku laporan dapat diekspor ke dalam dua format formal:
    </p>
    <ul>
        <li><b>Format PDF Landscape A4:</b> Tata letak horizontal yang lega dan rapi untuk presentasi manajerial, dilengkapi kop surat toko resmi, nomor dokumen dinamis, tabel audit bergaris rapi, dan stempel Tanda Tangan Elektronik (TTE).</li>
        <li><b>Format Spreadsheet Excel (.xlsx):</b> Berkas tabel spreadsheet data mentah yang dapat diolah lebih lanjut untuk keperluan akuntansi perpajakan atau audit internal.</li>
    </ul>

    {{-- ========================================================================= --}}
    {{-- BAB 7: TANDA TANGAN ELEKTRONIK (TTE) & SERTIFIKASI DIGITAL --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 07</div>
    <h1 class="chapter-title">TANDA TANGAN ELEKTRONIK (TTE) & SERTIFIKASI DIGITAL</h1>

    <h2 class="section-title">7.1 Landasan Hukum UU ITE No. 11/2008 & Integritas Dokumen Digital</h2>
    <p>
        Implementasi Tanda Tangan Elektronik (TTE) pada sistem ini berlandaskan pada <b>Pasal 11 Undang-Undang Republik Indonesia Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik (UU ITE)</b> serta Peraturan Pemerintah Nomor 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik (PSTE). TTE yang dihasilkan memiliki kekuatan hukum dan akibat hukum yang sah, mengikat para pihak yang menandatanganinya, dan diakui sebagai alat bukti hukum yang sah di pengadilan.
    </p>

    <h2 class="section-title">7.2 Standar Kriptografi SHA-256 & Timestamp Keabsahan Dokumen</h2>
    <p>
        Mekanisme pengamanan dokumen laporan dan struk digital menggunakan standar kriptografi asimetris:
    </p>
    <div class="step-box">
        <div class="step-header"><span class="step-num">1</span> Pembentukan Signature Hash SHA-256</div>
        Sistem menggabungkan metadata dokumen (Nomor Dokumen, Tanggal Periode, Total Nilai Finansial, ID Pengguna Penandatangan, dan Garam Rahasia <code>APP_KEY</code>) lalu meng-hash data tersebut menggunakan algoritma Secure Hash Algorithm 256-bit (SHA-256).
    </div>
    <div class="step-box">
        <div class="step-header"><span class="step-num">2</span> Penyematan Timestamp & QR Code Verifikasi</div>
        Hasil hash SHA-256 beserta cap waktu (*timestamp*) presisi detik disematkan ke dalam QR Code stempel dokumen dan dicetak di sudut kanan bawah berkas PDF laporan.
    </div>

    <h2 class="section-title">7.3 Struktur Format Penomoran Dokumen Laporan Dinamis (LKEU/LPK/LSTK)</h2>
    <p>
        Untuk memastikan konsistensi penomoran arsip, sistem menerapkan aturan penomoran dokumen dinamis berbasis **Tanggal Periode Laporan**:
    </p>
    <div style="background-color: #F7FAFC; border: 1.5px solid #00AA13; padding: 8px 12px; border-radius: 6px; font-family: monospace; font-size: 8.5pt; margin: 6px 0; text-align: center;">
        <b>[KODE_BUKU] / [BULAN_DATA] / [TANGGAL_DATA] / [TAHUN_DATA] / [NOMOR_URUT]</b><br>
        <span style="font-size: 7.5pt; color: #718096; font-family: sans-serif;">Contoh: LKEU / 08 / 05 / 2026 / 001 (Laporan Keuangan data tgl 5 Agustus 2026)</span>
    </div>
    <p>
        Sistem memastikan bahwa kapanpun dokumen tersebut dicetak ulang di masa depan, nomor dokumen yang tercetak tetap merujuk pada tanggal data laporan tersebut dihasilkan, menjaga validitas riwayat audit.
    </p>

    <h2 class="section-title">7.4 Penandatangan Dokumen Otomatis Berdasarkan Akun & Gelar Alias TTE</h2>
    <p>
        Saat laporan diekspor ke format PDF, sistem secara otomatis mengidentifikasi akun administrator yang sedang login dan mencetak nama lengkap serta **Gelar Jabatan Resmi TTD** (misalnya: *Budi Santoso, S.E. &bull; Kepala Bagian Keuangan*) di bawah stempel digital TTE.
    </p>

    <h2 class="section-title">7.5 Portal Publik Verifikasi Keaslian Dokumen Laporan (/verify/document)</h2>
    <p>
        Siapapun yang menerima berkas cetak PDF laporan formal dapat memverifikasi keaslian dokumen tersebut dengan memindai QR Code menggunakan kamera ponsel pintar. Peramban akan otomatis membuka **Portal Publik Verifikasi Dokumen Resmi (<code>/verify/document</code>)** yang menampilkan Sertifikat Hijau Keabsahan Dokumen dengan status valid, tanggal pengesahan, dan ringkasan data resmi.
    </p>

    <h2 class="section-title">7.6 Portal Publik Verifikasi Keaslian Faktur Pelanggan (/verify/tte/{trx})</h2>
    <p>
        Pelanggan yang menerima struk belanja dapat memindai kode QR pada nota untuk membuka **Portal Verifikasi Faktur Belanja (<code>/verify/tte/{trx}</code>)** guna memastikan bahwa transaksi belanja mereka benar-benar tercatat secara sah di server toko.
    </p>

    <h2 class="section-title">7.7 Tautan Faktur Digital Sementara Bertanda Tangan (Expired 24 Jam)</h2>
    <p>
        Untuk menjaga kerahasiaan dan privasi data transaksi pelanggan yang dikirim melalui pesan WhatsApp, sistem menerapkan pengamanan URL bertanda tangan kriptografis (*Signed URL*) dengan masa kedaluwarsa **24 Jam**. Setelah lewat 24 jam, tautan e-faktur WhatsApp tersebut otomatis terkunci dan tidak dapat diakses lagi oleh publik, sementara data transaksi di server toko tetap tersimpan abadi secara aman.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 8: PENGATURAN TOKO, KONFIGURASI & INTEGRASI --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 08</div>
    <h1 class="chapter-title">PENGATURAN TOKO, KONFIGURASI & INTEGRASI SISTEM</h1>

    <h2 class="section-title">8.1 Pusat Pengaturan Sistem Terpadu (/admin/settings)</h2>
    <p>
        Menu Pengaturan Sistem (<code>/admin/settings</code>) merupakan pusat kendali untuk mengonfigurasi seluruh aspek identitas bisnis toko, branding aplikasi, parameter printer struk, pengunggahan audio bel, dan integrasi gateway DOKU.
    </p>

    <h2 class="section-title">8.2 Personalisasi Identitas Bisnis, Nama Usaha, Alamat & Logo Toko</h2>
    <p>
        Seluruh teks identitas toko bersifat dinamis dan dapat dipersonalisasi tanpa menyentuh kode program:
    </p>
    <ul>
        <li><b>Nama Toko:</b> Nama resmi badan usaha yang akan tercetak pada header struk kasir thermal, nota PDF, cover buku panduan, dan portal sertifikat digital.</li>
        <li><b>Alamat Lengkap Toko:</b> Alamat fisik gerai atau toko ritel beserta kota dan kode pos.</li>
        <li><b>Nomor Telepon / WhatsApp Bantuan:</b> Saluran kontak layanan pelanggan toko.</li>
        <li><b>Logo Usaha:</b> Berkas gambar logo toko (.png, .jpg, .svg). Berkas logo yang diunggah disimpan di direktori aman dan dialirkan melalui rute khusus <code>/media-file</code>.</li>
    </ul>

    <h2 class="section-title">8.3 Kustomisasi Branding Aplikasi, Prefix Invoice & Favicon Browser</h2>
    <p>
        Administrator dapat mengubah nama branding aplikasi (contoh: <code>SIKANDA POS</code>) yang tampil pada tab browser dan bilah sidebar. Selain itu, administrator dapat menentukan **Prefix Nomor Faktur** (misalnya: *INV*, *TRX*, *SLS*) untuk membedakan identitas transaksi antar cabang toko.
    </p>

    <h2 class="section-title">8.4 Pengaturan & Pengunggahan Suara Bel Kasir (Audio Chime MP3/WAV)</h2>
    <p>
        Melalui tab Audio Bel Kasir, administrator dapat mengunggah berkas efek suara lonceng kasir khusus berformat <code>.mp3</code> atau <code>.wav</code> (ukuran maksimal 2 MB) dan mengujinya secara langsung menggunakan tombol **Tes Putar Audio**. Berkas audio ini akan diputar otomatis pada peramban meja kasir setiap kali pembayaran berhasil.
    </p>

    <h2 class="section-title">8.5 Konfigurasi Gateway DOKU Merchant (Sandbox & Production)</h2>
    <p>
        Untuk menghubungkan sistem dengan layanan Dynamic QRIS Bank Indonesia, administrator memasukkan parameter kredensial resmi dari dashboard DOKU Merchant:
    </p>
    <table class="doc-table">
        <tr>
            <th style="width: 30%;">Parameter DOKU</th>
            <th style="width: 35%;">Format Nilai</th>
            <th>Keterangan Integrasi</th>
        </tr>
        <tr>
            <td><b>DOKU Client ID</b></td>
            <td>String numerik (contoh: <code>MALL-ID-829103...</code>)</td>
            <td>Identitas merchant unik yang diterbitkan oleh DOKU.</td>
        </tr>
        <tr>
            <td><b>DOKU Secret Key</b></td>
            <td>String acak (contoh: <code>SK-live-892837498...</code>)</td>
            <td>Kunci enkripsi rahasia untuk menghasilkan tanda tangan HMAC-SHA256 pada header permintaan API.</td>
        </tr>
        <tr>
            <td><b>Mode Lingkungan</b></td>
            <td><code>sandbox</code> atau <code>production</code></td>
            <td>Pilihan mode uji coba (*Sandbox*) untuk pelatihan staf atau mode langsung (*Production*) untuk transaksi riil.</td>
        </tr>
    </table>

    <h2 class="section-title">8.6 Pengaturan Footer Struk Kasir & Kebijakan Toko</h2>
    <p>
        Administrator dapat menyesuaikan 2 baris pesan catatan kaki (*footer*) pada struk belanja thermal, seperti ucapan terima kasih (*"Terima Kasih Atas Kunjungan Anda"*) dan kebijakan penukaran barang (*"Barang yang sudah dibeli tidak dapat ditukar/dikembalikan"*).
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 9: MANAJEMEN PENGGUNA & GELAR JABATAN (ALIAS TTE) --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 09</div>
    <h1 class="chapter-title">MANAJEMEN PENGGUNA & GELAR JABATAN (ALIAS TTE)</h1>

    <h2 class="section-title">9.1 Daftar Akun Pengguna (/admin/users)</h2>
    <p>
        Modul Manajemen Pengguna (<code>/admin/users</code>) memuat tabel seluruh akun personil kasir dan administrator toko. Tabel menampilkan nama lengkap, alamat email, role wewenang, gelar jabatan alias penandatangan dokumen, serta opsi tindakan edit dan hapus akun.
    </p>

    <h2 class="section-title">9.2 Pendaftaran Akun Kasir / Admin Baru & Hak Wewenang Role</h2>
    <p>
        Penambahan staf baru dilakukan dengan mengklik tombol **+ Tambah Pengguna Baru** dan melengkapi data:
    </p>
    <ul>
        <li><b>Nama Lengkap:</b> Nama personil staf toko (contoh: *Dewi Lestari*).</li>
        <li><b>Alamat Email Resmi:</b> Surel unik yang digunakan untuk login (contoh: *dewi@toko.site*).</li>
        <li><b>Peran (Role):</b> Pilihan role **Administrator** (akses penuh ke seluruh menu) atau **Kasir** (akses terbatas hanya untuk melayani meja kasir).</li>
        <li><b>Kata Sandi Awal:</b> Sandi default minimal 8 karakter yang dapat diganti mandiri oleh staf saat login pertama kali.</li>
    </ul>

    <h2 class="section-title">9.3 Konfigurasi Kolom "Alias / Gelar Jabatan TTD" Penandatangan Dokumen</h2>
    <p>
        Kolom **"Alias / Gelar Jabatan TTD"** merupakan fitur khusus yang menentukan teks jabatan formal yang dicetak di bawah nama pada stempel digital TTE berkas PDF laporan keuangan. Contoh pengisian:
    </p>
    <ul>
        <li><code>Kepala Bagian Keuangan</code> &bull; Untuk administrator divisi perbendaharaan.</li>
        <li><code>Store Manager / Pimpinan Usaha</code> &bull; Untuk pemilik atau pengelola gerai toko.</li>
        <li><code>Supervisor Meja Kasir</code> &bull; Untuk kepala regu operasional kasir harian.</li>
    </ul>
    <p>
        Jika kolom ini dibiarkan kosong, sistem secara otomatis menggunakan fallback nama role akun pengguna tersebut.
    </p>

    <h2 class="section-title">9.4 Pembaruan Data Pengguna & Prosedur Reset Kata Sandi</h2>
    <p>
        Jika seorang kasir lupa kata sandi akunnya, Administrator dapat melakukan reset kata sandi dengan membuka menu edit pengguna pada rute <code>/admin/users/{id}/edit</code>, mengetikkan kata sandi baru, dan menyimpannya. Sistem akan langsung mengenkripsi sandi baru dengan hash Bcrypt dan membatalkan seluruh sesi aktif lama pengguna tersebut.
    </p>

    {{-- ========================================================================= --}}
    {{-- BAB 10: PEMELIHARAAN SERVER EKSKLUSIF DIKELOLA OLEH I GUSTI SULTAN --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="chapter-num">BAB 10</div>
    <h1 class="chapter-title">PEMELIHARAAN SERVER EKSKLUSIF OLEH I GUSTI SULTAN</h1>

    <h2 class="section-title">10.1 Arsitektur Server Produksi & Manajemen Terpusat oleh I Gusti Sultan</h2>
    <p>
        Seluruh infrastruktur server, instalasi lingkungan produksi, konfigurasi web server, tuning basis data, pengamanan firewall, dan pemeliharaan berkesinambungan **dikelola dan dipelihara secara eksklusif oleh I Gusti Sultan**.
    </p>
    <p>
        Arsitektur server mengadopsi tumpukan Linux enterprise berbasis host aaPanel pada direktori root <code>/www/wwwroot/kasir.site</code> dengan web server Nginx berkecepatan tinggi, PHP-FPM 8.2 socket, MySQL 8.0, dan integrasi Git Repository terpusat.
    </p>

    <h2 class="section-title">10.2 Dedicated Media Streaming Engine (/media-file) Anti-404 Nginx</h2>
    <p>
        Pada arsitektur hosting aaPanel standar, tautan symlink statis <code>public/storage</code> kerap mengalami kendala *404 Not Found* saat peramban memuat gambar logo atau audio bel kasir akibat restriksi hak akses Nginx.
    </p>
    <p>
        Untuk mengatasi masalah ini secara permanen, **I Gusti Sultan** merancang dan mengimplementasikan **Dedicated Media Streaming Engine** melalui rute khusus <code>/media-file/{path}</code>. Mesin ini membaca berkas biner langsung dari storage internal, memvalidasi MIME-type secara akurat, dan mengalirkan data biner dengan header *HTTP Cache-Control (30 Hari)*. Hal ini menjamin bahwa logo toko dan audio bel kasir **100% selalu tampil dan berbunyi normal tanpa pernah mengalami broken image atau error 404**.
    </p>

    <h2 class="section-title">10.3 Prosedur Pemeliharaan Storage Symlink & File System aaPanel</h2>
    <p>
        Struktur pohon direktori penyimpanan berkas pada server dikonfigurasi sebagai berikut:
    </p>
    <div style="background-color: #F7FAFC; border: 1px solid #CBD5E0; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 7.8pt; line-height: 1.5; margin: 6px 0;">
        /www/wwwroot/kasir.site/<br>
        ├── public/storage &rarr; ../storage/app/public (Symlink Linux)<br>
        ├── storage/app/public/logos/ (Direktori Berkas Logo Usaha)<br>
        ├── storage/app/public/audio/ (Direktori Berkas Suara Bel Kasir)<br>
        └── storage/logs/laravel.log (Berkas Log Monitoring Sistem)
    </div>
    <p>
        Hak akses direktori diatur ketat pada <code>CHMOD 755</code> untuk folder dan <code>CHMOD 644</code> untuk berkas dengan kepemilikan user web server <code>chown -R www:www /www/wwwroot/kasir.site</code>.
    </p>

    <h2 class="section-title">10.4 Konfigurasi Web Server Nginx & URL Rewrite Engine Laravel 11</h2>
    <p>
        Blok konfigurasi Nginx pada berkas virtual host server dikonfigurasi dengan URL rewrite engine standar Laravel:
    </p>
    <div style="background-color: #2D3748; color: #E2E8F0; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 7.5pt; line-height: 1.4; margin: 6px 0;">
        server {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;listen 80; listen 443 ssl http2;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;server_name botnomor.my.id kasir.site;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;root /www/wwwroot/kasir.site/public;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;index index.php index.html;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;location / {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;try_files $uri $uri/ /index.php?$query_string;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
        &nbsp;&nbsp;&nbsp;&nbsp;location ~ \.php$ {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fastcgi_pass unix:/tmp/php-cgi-82.sock;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fastcgi_index index.php;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;include fastcgi.conf;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
        &nbsp;&nbsp;&nbsp;&nbsp;client_max_body_size 50M;<br>
        }
    </div>

    <h2 class="section-title">10.5 Pemantauan Log Kesalahan Sistem (laravel.log) & Error Handling</h2>
    <p>
        Sistem mencatat setiap aktivitas kritis dan potensi error ke dalam berkas log terpusat di <code>storage/logs/laravel.log</code>. **I Gusti Sultan** melakukan pemantauan log secara rutin untuk mendeteksi anomali koneksi webhook DOKU, kegagalan pencetakan printer thermal, atau beban memori tinggi saat ekspor laporan PDF.
    </p>

    <h2 class="section-title">10.6 Prosedur Pencadangan (Backup) Harian & Disaster Recovery oleh I Gusti Sultan</h2>
    <p>
        Untuk menjamin keamanan aset data transaksi toko dari segala risiko kerusakan perangkat keras, **I Gusti Sultan** mengonfigurasi skema pencadangan otomatis:
    </p>
    <ul>
        <li><b>Pencadangan Database Otomatis Harian:</b> Cron job mengeksekusi perintah <code>mysqldump</code> setiap pukul 00:00 tengah malam untuk mengompresi dan menyimpan seluruh tabel basis data.</li>
        <li><b>Penyimpanan Cadangan Terisolasi:</b> Berkas cadangan disimpan pada partisi khusus dengan retensi rotasi selama 30 hari.</li>
        <li><b>Pemulihan Bencana (*Disaster Recovery*):</b> Jika terjadi kegagalan server fisik, waktu pemulihan (*Recovery Time Objective / RTO*) dijamin di bawah 15 menit dengan nol kehilangan data transaksi (*Zero Data Loss*).</li>
    </ul>

    <h2 class="section-title">10.7 Standar Operasional Prosedur (SOP) Deployment Pembaruan Sistem</h2>
    <p>
        Setiap pembaruan fitur atau perbaikan kode sistem dilakukan melalui alur deployment Git resmi:
    </p>
    <div style="background-color: #00360D; color: #FFFFFF; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 8pt; line-height: 1.5; margin: 6px 0;">
        <span style="color: #A8DAB5;"># Masuk ke direktori aplikasi</span><br>
        cd /www/wwwroot/kasir.site<br><br>
        <span style="color: #A8DAB5;"># Tarik pemutakhiran kode sumber terbaru</span><br>
        git pull origin main<br><br>
        <span style="color: #A8DAB5;"># Bersihkan cache tampilan dan routing</span><br>
        php artisan view:clear && php artisan route:clear
    </div>

    <h2 class="section-title">10.8 Panduan Pemecahan Masalah Cepat (FAQ Troubleshooting Operasional)</h2>
    <table class="doc-table">
        <tr>
            <th style="width: 30%;">Gejala Kendala</th>
            <th style="width: 32%;">Kemungkinan Penyebab</th>
            <th>Langkah Solusi Cepat</th>
        </tr>
        <tr>
            <td><b>Struk Thermal Tidak Keluar</b></td>
            <td>Koneksi Bluetooth printer terputus atau kertas habis.</td>
            <td>Periksa rol kertas struk, matikan dan nyalakan ulang printer Bluetooth, lalu pasangkan ulang (*re-pair*).</td>
        </tr>
        <tr>
            <td><b>QRIS Tidak Muncul di Kasir</b></td>
            <td>Koneksi internet toko terganggu atau kredensial DOKU salah.</td>
            <td>Alihkan sementara ke pembayaran TUNAI. Periksa koneksi internet modem toko dan cek kredensial DOKU di menu Pengaturan.</td>
        </tr>
        <tr>
            <td><b>Halaman Menampilkan Error 419</b></td>
            <td>Sesi peramban kasir telah kedaluwarsa.</td>
            <td>Muat ulang halaman (*Refresh*) peramban dan lakukan login ulang dengan email & kata sandi.</td>
        </tr>
        <tr>
            <td><b>Logo Toko Tidak Muncul</b></td>
            <td>Tautan symlink storage terputus.</td>
            <td>Hubungi I Gusti Sultan untuk memastikan rute <code>/media-file</code> aktif dan mengeksekusi <code>php artisan storage:link</code>.</td>
        </tr>
    </table>

    <h2 class="section-title">10.9 Glosarium Istilah POS, Perbankan, Kriptografi & E-Commerce</h2>
    <table class="doc-table">
        <tr>
            <th style="width: 25%;">Istilah Teknis</th>
            <th>Definisi & Penjelasan Kontekstual</th>
        </tr>
        <tr>
            <td><b>POS (Point of Sale)</b></td>
            <td>Sistem perangkat lunak meja kasir untuk memproses dan mencatat transaksi penjualan ritel secara langsung.</td>
        </tr>
        <tr>
            <td><b>Dynamic QRIS</b></td>
            <td>Kode QR pembayaran digital nasional Bank Indonesia yang diterbitkan secara dinamis dengan nominal tagihan presisi.</td>
        </tr>
        <tr>
            <td><b>MDR (0.7%)</b></td>
            <td>Merchant Discount Rate, yaitu biaya resmi pemrosesan transaksi pembayaran QRIS yang ditetapkan regulator.</td>
        </tr>
        <tr>
            <td><b>TTE</b></td>
            <td>Tanda Tangan Elektronik yang bersertifikat dan berkekuatan hukum sah berdasarkan UU ITE No. 11/2008 Pasal 11.</td>
        </tr>
        <tr>
            <td><b>SHA-256</b></td>
            <td>Algoritma fungsi hash kriptografi satu arah berukuran 256-bit untuk mengunci keabsahan data laporan dari manipulasi.</td>
        </tr>
        <tr>
            <td><b>Webhook</b></td>
            <td>Mekanisme pengiriman notifikasi HTTP otomatis dari server perbankan ke server kasir saat pembayaran sukses.</td>
        </tr>
    </table>

    <h2 class="section-title">10.10 Lembar Kontak Dukungan Teknis & Komitmen Garansi I Gusti Sultan</h2>
    <p>
        Untuk eskalasi kendala teknis darurat, konsultasi integrasi perangkat keras kasir, atau pemeliharaan infrastruktur server, silakan menghubungi saluran resmi berikut:
    </p>
    <table class="doc-table">
        <tr>
            <td style="width: 38%;"><b>Lead Software Architect & Server Maintainer</b></td>
            <td><b>I Gusti Sultan</b></td>
        </tr>
        <tr>
            <td><b>Layanan Pemeliharaan Server</b></td>
            <td>Pemantauan Server 24/7, Pencadangan Database Harian & Pembaruan Sistem</td>
        </tr>
        <tr>
            <td><b>Repositori Kode Sumber Resmi</b></td>
            <td><code>https://github.com/Igustisultanh12/toko.git</code> (Branch: <code>main</code>)</td>
        </tr>
        <tr>
            <td><b>Status Jaminan Layanan</b></td>
            <td><b style="color: #00880F;">● AKTIF & TERPELIHARA PENUH</b></td>
        </tr>
    </table>

    <div style="margin-top: 30px; text-align: center;" class="no-break">
        <div style="background-color: #E6F4EA; border: 1.5px solid #A8DAB5; border-radius: 8px; padding: 15px; display: inline-block; width: 85%;">
            <div style="font-weight: 900; font-size: 9pt; color: #00661A; text-transform: uppercase;">
                KOMITMEN KUALITAS & KESTABILAN SISTEM KASIR POS
            </div>
            <div style="font-size: 7.8pt; color: #2D3748; margin-top: 4px;">
                Hak Cipta Perangkat Lunak, Arsitektur Basis Data & Seluruh Pemeliharaan Server Produksi Dikelola Penuh oleh:
            </div>
            <div style="font-size: 11pt; font-weight: 900; color: #00880F; margin-top: 4px;">
                I GUSTI SULTAN
            </div>
        </div>
        <p style="font-size: 7.5pt; color: #718096; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; margin-top: 20px;">
            &copy; {{ date('Y') }} {{ strtoupper($shop['app_name'] ?? 'SIKANDA POS') }} &bull; Dikelola oleh I Gusti Sultan. All rights reserved.
        </p>
    </div>

</body>
</html>
