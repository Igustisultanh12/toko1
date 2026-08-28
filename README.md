<p align="center">
  <img src="https://img.icons8.com/isometric/512/pos-terminal.png" width="120" alt="Logo SIKANDA POS">
</p>

<h1 align="center">🏪 SIKANDA POS & ONLINE STORE SYSTEM</h1>

<p align="center">
  <b>Sistem Manajemen Kasir Point of Sales (POS) Modern, Toko Online Publik (Guest Checkout), Pembayaran Otomatis QRIS DOKU, Pelacakan Resi Ekspedisi, dan Notifikasi Suara Full Real-Time.</b>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 10"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1+"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://alpinejs.dev"><img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js"></a>
  <a href="https://doku.com"><img src="https://img.shields.io/badge/DOKU-QRIS_Jokul-EE2737?style=for-the-badge&logo=google-pay&logoColor=white" alt="DOKU QRIS"></a>
  <a href="https://sweetalert2.github.io"><img src="https://img.shields.io/badge/SweetAlert-2-8774e1?style=for-the-badge&logo=javascript&logoColor=white" alt="SweetAlert2"></a>
</p>

---

## 🌟 Tentang Aplikasi

**SIKANDA POS & Online Store** adalah platform kasir toko dan *e-commerce storefront* terpadu yang dirancang untuk kecepatan transaksi fisik di toko maupun pesanan pelanggan secara daring tanpa mewajibkan login (*Guest Checkout*). Sistem dilengkapi arsitektur **Full Real-Time**, pembayaran **Dynamic QRIS DOKU** dengan verifikasi webhook instan, bot notifikasi **Telegram Radar**, cetak label pengiriman format A6/Thermal, dan **Web Audio Synthesizer** untuk feedback audio notifikasi tanpa jeda jaringan.

Sistem dikelola dan dipelihara secara profesional oleh **I Gusti Sultan**.

---

## ✨ Fitur-Fitur Utama

### 🛒 1. Kasir Point of Sales (POS) Modern
- **2 Tampilan Responsif**: Mode Mobile Kasir & Mode Desktop PC Full-Screen (`/cashier/pos` & `/cashier/pos-pc`).
- **Pencarian Cepat & Barcode Scanner**: Dukungan pemindai barcode USB/Bluetooth instan.
- **Multimetode Pembayaran**: Tunai (*Cash*) dengan kalkulasi kembalian otomatis & Non-Tunai (**Dynamic QRIS DOKU**).
- **Text-to-Speech & Sound Engine**: Pengumuman suara nominal pembayaran dan efek suara lonceng kasir.
- **Cetak Struk Thermal**: Format 58mm & 80mm yang kompatibel dengan printer bluetooth/USB.

### 🌐 2. Toko Online Publik (Guest Checkout Tanpa Login)
- **Katalog Produk Dinamis (`/order`)**: Tampilan grid modern bertema hijau Gojek (*#00AA13*), badge diskon, sisa stok, dan foto produk hingga 4MB.
- **Floating Cart Drawer**: Keranjang belanja interaktif berbasis Alpine.js dengan penyesuaian kuantitas langsung.
- **Checkout Cepat**: Pelanggan cukup mengisi nama, no HP/WhatsApp, alamat pengiriman, opsi kurir, dan catatan pesanan.
- **Pembayaran QRIS Otomatis (`/order/pay/{order_number}`)**: Generate dynamic QRIS resmi via DOKU API dengan hitung mundur (*countdown timer*) 15 menit dan verifikasi lunas otomatis tanpa refresh.
- **Struk Digital Online (`/order/receipt/{order_number}`)**: Bukti pembelian digital yang siap diunduh atau disimpan.

### 🚚 3. Portal Lacak Pengiriman Pesanan (`/lacak/{order_number}`)
- **5 Stepper Visual Real-Time**: *Menunggu Bayar ➔ Sudah Dibayar ➔ Sedang Disiapkan ➔ Sedang Dikirim ➔ Selesai*.
- **Auto Reveal Resi Ekspedisi**: Kotak nomor resi pengiriman resmi dan tombol *Salin Resi* langsung tampil di layar pembeli saat kasir menginput resi.
- **Bantuan WhatsApp**: Tombol interaktif langsung ke admin toko dengan pesan yang sudah terformat rapi.

### 🔊 4. Sistem Full Real-Time & Web Audio Synthesizer
- **Zero-Refresh UI**: Dashboard admin, antrean pesanan kasir, dan pelacakan pembeli ter-update otomatis dalam 3-4 detik.
- **Synthesizer Native Browser**: Efek suara instan bebas CORS dan tanpa delay:
  - 🔔 *Double Ding-Dong*: Pesanan online baru masuk ke toko.
  - 💰 *Victory Arpeggio*: Pembayaran QRIS / transaksi kasir sukses diterima.
  - 🚚 *Status Melodi*: Perubahan status pengiriman atau resi diterbitkan.

### 📊 5. Panel Manajemen & Laporan Admin
- **Live Statistik Dashboard (`/dashboard`)**: Pemantauan omzet harian, transaksi sukses, dan item keluar secara *real-time*.
- **Manajemen Produk & Foto (`/admin/products`)**: CRUD produk dengan dukungan upload foto maksimal **4MB**, barcode generator otomatis, dan proteksi stok menipis.
- **Manajemen Pesanan Online (`/admin/orders`)**: Konfirmasi pesanan, input ekspedisi & nomor resi, serta pembatalan/pengembalian stok otomatis.
- **Cetak Label Pengiriman A6 (`/admin/orders/{id}/shipping-label`)**: Desain label paket siap tempel ukuran A6 (105 x 148 mm) dengan Barcode Code128.
- **Laporan Penjualan & Ekspor Excel/PDF**: Rekap omzet harian, mingguan, bulanan, dan per kasir.
- **Notifikasi Telegram Radar**: Notifikasi instan ke grup/channel Telegram pemilik toko setiap kali ada transaksi QRIS lunas atau pesanan online baru.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend Framework**: [Laravel 10.x](https://laravel.com) (PHP 8.1 / 8.2)
- **Frontend Layer**: [Blade Templating](https://laravel.com/docs/blade), [Alpine.js 3.x](https://alpinejs.dev), [Tailwind CSS](https://tailwindcss.com)
- **UI Components & Alerts**: [SweetAlert2](https://sweetalert2.github.io)
- **Payment Gateway**: [DOKU Jokul API (QRIS Dynamic)](https://doku.com)
- **PDF Engine**: [Barryvdh Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Excel Engine**: [Maatwebsite Laravel Excel](https://laravel-excel.com)
- **Barcode Generator**: [Picqer PHP Barcode Generator](https://github.com/picqer/php-barcode-generator)
- **Audio Engine**: Web Audio API (`AudioContext` Oscillators)

---

## 📋 Struktur Rute Utama

| Rute URL | Akses | Deskripsi |
| :--- | :--- | :--- |
| `/` | Publik | Beranda / Redirect ke Login atau Toko Online |
| `/order` | Publik | Katalog Belanja Toko Online Pelanggan |
| `/order/checkout` | Publik | Proses Pemesanan Online (POST) |
| `/order/pay/{order_number}` | Publik | Halaman Bayar Dynamic QRIS DOKU |
| `/order/receipt/{order_number}` | Publik | Struk Digital Pesanan Online |
| `/lacak` & `/lacak/{order_number}` | Publik | Portal Pelacakan Status & Nomor Resi |
| `/dashboard` | Admin | Live Dashboard Statistik Real-time |
| `/admin/orders` | Admin / Kasir | Manajemen Konfirmasi & Resi Pesanan Online |
| `/admin/products` | Admin | Manajemen Produk, Harga, & Foto (Max 4MB) |
| `/admin/reports` | Admin | Laporan Transaksi & Cetak Rekap |
| `/cashier/pos` | Kasir | Layar Transaksi Kasir POS |
| `/cashier/pos-pc` | Kasir | Layar POS Khusus Desktop Monitor Lebar |

---

## 🚀 Panduan Instalasi Lokal (Local Setup)

### 1. Clone Repositori
```bash
git clone https://github.com/Igustisultanh12/toko.git
cd toko
```

### 2. Install Dependensi PHP & Asset
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database dan kredensial DOKU / Telegram di file `.env`:
```env
APP_NAME="SIKANDA POS"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kasir
DB_USERNAME=root
DB_PASSWORD=

# DOKU QRIS Payment Gateway
DOKU_CLIENT_ID=your_doku_client_id
DOKU_SECRET_KEY=your_doku_secret_key
DOKU_IS_PRODUCTION=false

# Notifikasi Telegram Radar (Opsional)
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

### 4. Jalankan Migrasi & Database Seeder
```bash
php artisan migrate --seed
```

### 5. Jalankan Web Server
```bash
php artisan serve
```
Buka peramban di `http://127.0.0.1:8000`.

---

## 🌐 Panduan Deployment di Server Produksi (aaPanel)

Untuk menerapkan pembaruan terbaru di server Linux aaPanel:

```bash
# Masuk ke direktori web root
cd /www/wwwroot/kasir.site

# Ambil pembaruan dari repository
git pull origin main

# Jalankan migrasi tabel terbaru
php artisan migrate --force

# Bersihkan cache Laravel
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Optimasi performa
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 👨‍💻 Perawatan & Manajemen Sistem

Sistem ini dikelola, dirawat, dan dioptimalkan oleh:
* **System Administrator / Developer**: **I Gusti Sultan**
* **Repository**: [https://github.com/Igustisultanh12/toko.git](https://github.com/Igustisultanh12/toko.git)
* **Domain Produksi**: `https://botnomor.my.id`

---

<p align="center">
  Made with ❤️ for Seamless Retail & E-Commerce Operations.
</p>
