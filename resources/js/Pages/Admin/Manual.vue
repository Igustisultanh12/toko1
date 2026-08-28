<template>
  <AdminLayout>
    <template #header>Buku Panduan Penggunaan Sistem</template>
    <Head title="Buku Panduan" />

    <div class="max-w-4xl mx-auto space-y-6">
      
      <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm space-y-6">
        <div>
          <h3 class="text-lg font-black text-slate-900 uppercase">📖 Petunjuk & Panduan Operasional</h3>
          <p class="text-xs text-slate-400 font-medium">Panduan lengkap cara penggunaan Kasir POS, Toko Online, dan Panel Administrasi.</p>
        </div>

        <div class="space-y-6 text-xs text-slate-700 leading-relaxed font-medium">
          <!-- BAB 1 -->
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <h4 class="font-black text-slate-900 uppercase text-xs">1. Operasional Kasir POS Modern</h4>
            <ul class="list-disc list-inside space-y-1 text-slate-600">
              <li><b>Scan Barcode:</b> Cukup arahkan barcode scanner ke produk, sistem otomatis memasukkan item ke keranjang kasir.</li>
              <li><b>Pembayaran Tunai:</b> Klik tombol "💵 Tunai", masukkan nominal uang pembeli, sistem otomatis menghitung uang kembalian.</li>
              <li><b>Pembayaran QRIS:</b> Klik tombol "⚡ QRIS DOKU", tunjukkan QRIS kepada pelanggan. Sistem otomatis mendeteksi status lunas seketika.</li>
            </ul>
          </div>

          <!-- BAB 2 -->
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <h4 class="font-black text-slate-900 uppercase text-xs">2. Katalog Belanja Online & Pelanggan</h4>
            <ul class="list-disc list-inside space-y-1 text-slate-600">
              <li><b>Detail Produk & Galeri:</b> Pelanggan dapat mengklik foto barang di katalog untuk membuka slider banyak foto, membaca spesifikasi, dan cek stok.</li>
              <li><b>Lacak Pesanan:</b> Pembeli dapat memasukkan nomor pesanan di menu "Lacak Pesanan" untuk memantau pengiriman barang.</li>
              <li><b>Pusat Pengaduan:</b> Pelanggan yang telah membayar lunas dapat mengunggah bukti foto dan video unboxing jika barang mengalami kendala.</li>
            </ul>
          </div>

          <!-- BAB 3 -->
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <h4 class="font-black text-slate-900 uppercase text-xs">3. Kelola Pesanan & Ekspedisi Admin</h4>
            <ul class="list-disc list-inside space-y-1 text-slate-600">
              <li>Buka menu <b>Kelola Pesanan</b> lalu klik tombol hijau <b>"Kelola Pesanan"</b> pada invoice yang dituju.</li>
              <li>Pilih status pesanan dengan mengklik kartu status (Dibayar, Disiapkan, Dikirim, Selesai).</li>
              <li>Pilih kurir (J&T, JNE, SiCepat) dan masukkan nomor resi AWB, lalu klik Simpan. Status di layar pembeli akan langsung terupdate realtime.</li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
