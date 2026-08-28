<template>
  <AdminLayout>
    <template #header>Pusat Backup & Migrasi Data</template>
    <Head title="Backup & Migrasi Data" />

    <div class="max-w-6xl mx-auto space-y-8 pb-12">
      
      <!-- HERO BANNER -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-[#00360D] via-[#004D13] to-[#00661A] text-white p-6 sm:p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#00AA13]/20 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="space-y-1 relative z-10">
          <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-wider text-emerald-200 border border-white/10 mb-2">
            <span>🔄</span>
            <span>Pusat Cadangan & Pemindahan Data</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">Backup & Migrasi Data</h1>
          <p class="text-xs text-emerald-100/80 font-medium max-w-xl">
            Ekspor seluruh data barang, foto galeri, riwayat penjualan kasir, pesanan online, akun petugas, dan pengaturan sistem ke dalam 1 paket arsip lengkap.
          </p>
        </div>

        <div class="relative z-10 shrink-0">
          <a 
            href="/admin/backup/export-zip" 
            class="px-6 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-950/40 transition flex items-center space-x-2 border border-emerald-400/30"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span>Unduh Paket Lengkap (.ZIP)</span>
          </a>
        </div>
      </div>

      <!-- STATISTIK RINGKASAN DATA -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">📦 Produk & Stok</span>
          <p class="text-2xl font-black text-slate-800">{{ stats.total_products }} <span class="text-xs text-slate-400 font-bold">Item</span></p>
          <p class="text-[9px] text-emerald-600 font-bold">Termasuk foto & galeri</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">🧾 Transaksi Kasir</span>
          <p class="text-2xl font-black text-[#00880F]">{{ stats.total_sales }} <span class="text-xs text-slate-400 font-bold">Faktur</span></p>
          <p class="text-[9px] text-slate-400 font-bold">{{ stats.total_sale_items }} item detail terjual</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">🚚 Pesanan Online</span>
          <p class="text-2xl font-black text-purple-700">{{ stats.total_orders }} <span class="text-xs text-slate-400 font-bold">Pesanan</span></p>
          <p class="text-[9px] text-slate-400 font-bold">{{ stats.total_complaints }} data komplain</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-1">
          <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">💾 Total Media Storage</span>
          <p class="text-2xl font-black text-blue-600">{{ stats.storage_size }}</p>
          <p class="text-[9px] text-slate-400 font-bold">{{ stats.total_users }} akun &bull; {{ stats.total_settings }} setting</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: FITUR EKSPOR DATA & MIGRASI CEPAT (6 SPAN) -->
        <div class="lg:col-span-6 space-y-6">
          
          <!-- CARD EKSPOR DATA -->
          <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
              <span class="text-[10px] font-black text-[#00AA13] uppercase tracking-widest block mb-1">FITUR 1 &bull; EKSPOR DATA</span>
              <h3 class="text-lg font-black text-slate-900 uppercase">Ekspor Semua Data Sistem</h3>
              <p class="text-xs text-slate-400 font-medium mt-0.5">Unduh data untuk arsip berkala atau pemindahan ke server baru.</p>
            </div>

            <!-- OPSI 1: PAKET ZIP LENGKAP -->
            <div class="p-5 bg-emerald-50/60 rounded-3xl border-2 border-emerald-200/80 space-y-3">
              <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-[#00AA13] text-white flex items-center justify-center text-lg shrink-0 shadow-md shadow-emerald-600/30">
                  📦
                </div>
                <div class="flex-1">
                  <h4 class="font-black text-xs uppercase text-slate-900 leading-tight">Paket Lengkap Migrasi (.ZIP)</h4>
                  <p class="text-[11px] text-slate-600 font-medium mt-1">
                    Sangat direkomendasikan untuk pindah server! Mencakup seluruh database JSON, Dump SQL, serta seluruh file foto barang, galeri, dan logo.
                  </p>
                </div>
              </div>

              <a 
                href="/admin/backup/export-zip" 
                class="w-full py-3.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition flex items-center justify-center space-x-2"
              >
                <span>⚡ Unduh Paket Migrasi Lengkap (.ZIP)</span>
              </a>
            </div>

            <!-- OPSI 2 & 3: EKSPOR KHUSUS JSON & SQL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
              <a 
                href="/admin/backup/export-json" 
                class="p-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-left transition flex flex-col justify-between space-y-2 group"
              >
                <div class="flex justify-between items-center">
                  <span class="text-base">📄</span>
                  <span class="text-[9px] font-black uppercase text-slate-400 group-hover:text-[#00AA13]">JSON</span>
                </div>
                <div>
                  <h5 class="font-black text-xs text-slate-900 uppercase">Ekspor Data JSON</h5>
                  <p class="text-[10px] text-slate-400 font-medium mt-0.5">Database terstruktur</p>
                </div>
              </a>

              <a 
                href="/admin/backup/export-sql" 
                class="p-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-left transition flex flex-col justify-between space-y-2 group"
              >
                <div class="flex justify-between items-center">
                  <span class="text-base">💾</span>
                  <span class="text-[9px] font-black uppercase text-slate-400 group-hover:text-[#00AA13]">SQL Dump</span>
                </div>
                <div>
                  <h5 class="font-black text-xs text-slate-900 uppercase">Ekspor SQL Dump</h5>
                  <p class="text-[10px] text-slate-400 font-medium mt-0.5">Siap import phpMyAdmin</p>
                </div>
              </a>
            </div>

          </div>

          <!-- CARD MIGRASI LANGSUNG 1-KLIK -->
          <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
              <div>
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest block">FITUR CEPAT &bull; SETUP INSTAN</span>
                <h4 class="text-sm font-black text-slate-900 uppercase">Link Migrasi Sistem Langsung</h4>
              </div>
              <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase border border-blue-200">
                /migrasibaru
              </span>
            </div>

            <p class="text-xs text-slate-500 font-medium leading-relaxed">
              Jalur satu-klik untuk menjalankan seluruh migrasi database, membuat symlink storage, akun default, dan membersihkan cache secara otomatis.
            </p>

            <div class="flex flex-col sm:flex-row gap-2 pt-1">
              <a 
                href="/migrasibaru" 
                target="_blank"
                class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider text-center transition flex items-center justify-center space-x-1.5 shadow-md shadow-blue-500/20"
              >
                <span>🚀 Buka Link Migrasi Langsung</span>
                <span>↗</span>
              </a>

              <button 
                type="button" 
                @click="copyMigrationLink"
                class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider transition"
              >
                📋 Salin Link
              </button>
            </div>
          </div>

        </div>

        <!-- KOLOM KANAN: FITUR IMPOR / PULIHKAN DATA (6 SPAN) -->
        <div class="lg:col-span-6 space-y-6">
          <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
              <span class="text-[10px] font-black text-purple-600 uppercase tracking-widest block mb-1">FITUR 2 &bull; IMPOR & PULIHKAN</span>
              <h3 class="text-lg font-black text-slate-900 uppercase">Impor Data ke Aplikasi Ini</h3>
              <p class="text-xs text-slate-400 font-medium mt-0.5">Pulihkan atau masukkan data hasil backup dari server/aplikasi lama.</p>
            </div>

            <form @submit.prevent="confirmAndSubmitImport" class="space-y-5">
              
              <!-- PILIHAN FILE BACKUP -->
              <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">
                  Pilih File Backup (.ZIP atau .JSON) <span class="text-rose-500">*</span>
                </label>
                <div class="p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 hover:border-[#00AA13] transition">
                  <input 
                    type="file" 
                    ref="fileInput"
                    accept=".zip,.json" 
                    required
                    @change="handleFileChange"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-800 file:text-white hover:file:bg-black file:cursor-pointer transition"
                  >
                  <p class="text-[9px] text-slate-400 font-bold mt-2">Maksimal ukuran file: 200MB. Format didukung: .zip atau .json.</p>
                </div>
              </div>

              <!-- PILIHAN MODE IMPOR -->
              <div class="space-y-2 pt-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Metode Pemulihan:</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <label 
                    :class="importForm.mode === 'replace' ? 'border-[#00AA13] bg-emerald-50/50 ring-2 ring-emerald-300' : 'border-slate-200 bg-slate-50'"
                    class="p-4 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between space-y-2 text-left"
                  >
                    <input type="radio" v-model="importForm.mode" value="replace" class="sr-only">
                    <div class="flex justify-between items-center">
                      <span class="text-base">🧹</span>
                      <span v-if="importForm.mode === 'replace'" class="w-2.5 h-2.5 rounded-full bg-[#00AA13]"></span>
                    </div>
                    <div>
                      <h5 class="font-black text-xs text-slate-900 uppercase">Timpa Bersih (Fresh)</h5>
                      <p class="text-[10px] text-slate-500 font-medium mt-0.5">Mengganti seluruh data lama dengan isi backup.</p>
                    </div>
                  </label>

                  <label 
                    :class="importForm.mode === 'merge' ? 'border-purple-600 bg-purple-50/50 ring-2 ring-purple-300' : 'border-slate-200 bg-slate-50'"
                    class="p-4 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between space-y-2 text-left"
                  >
                    <input type="radio" v-model="importForm.mode" value="merge" class="sr-only">
                    <div class="flex justify-between items-center">
                      <span class="text-base">🔀</span>
                      <span v-if="importForm.mode === 'merge'" class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    </div>
                    <div>
                      <h5 class="font-black text-xs text-slate-900 uppercase">Gabungkan (Merge)</h5>
                      <p class="text-[10px] text-slate-500 font-medium mt-0.5">Mempertahankan data lama dan menambahkan data baru.</p>
                    </div>
                  </label>
                </div>
              </div>

              <!-- TOMBOL PROSES IMPOR -->
              <button 
                type="submit" 
                :disabled="isImporting || !importForm.backup_file"
                class="w-full py-4 bg-purple-700 hover:bg-purple-800 active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-purple-600/25 transition flex items-center justify-center space-x-2 mt-4"
              >
                <span v-if="isImporting">⏳ Sedang Memulihkan Data...</span>
                <span v-else>📥 Mulai Proses Impor Data</span>
              </button>

            </form>

            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs space-y-1">
              <div class="flex items-center space-x-1.5 font-black text-[11px]">
                <span>⚠️</span>
                <span>Peringatan Keamanan Data:</span>
              </div>
              <p class="text-[10px] text-amber-800 leading-relaxed font-medium">
                Pastikan Anda telah mengunduh backup terkini sebelum melakukan impor data dengan mode <b>Timpa Bersih</b> agar tidak kehilangan catatan penjualan yang sedang berjalan.
              </p>
            </div>

          </div>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  shop: Object,
  stats: Object,
});

const fileInput = ref(null);
const isImporting = ref(false);

const importForm = ref({
  backup_file: null,
  mode: 'replace',
});

const handleFileChange = (e) => {
  const files = e.target.files;
  if (files && files.length > 0) {
    importForm.value.backup_file = files[0];
  }
};

const copyMigrationLink = () => {
  const url = window.location.origin + '/migrasibaru';
  navigator.clipboard.writeText(url).then(() => {
    Swal.fire({
      icon: 'success',
      title: 'Link Disalin!',
      text: `Link migrasi langsung: ${url}`,
      toast: true,
      position: 'top-end',
      timer: 2000,
      showConfirmButton: false,
    });
  });
};

const confirmAndSubmitImport = () => {
  if (!importForm.value.backup_file) {
    Swal.fire({
      icon: 'warning',
      title: 'File Belum Dipilih',
      text: 'Silakan pilih file backup (.zip atau .json) terlebih dahulu.',
      confirmButtonColor: '#00AA13',
    });
    return;
  }

  const modeText = importForm.value.mode === 'replace'
    ? 'Perhatian: Mode "Timpa Bersih" akan menggantikan data lama dengan isi file backup ini. Lanjutkan?'
    : 'Mode "Gabungkan" akan memasukkan data backup tanpa menghapus data lama. Lanjutkan?';

  Swal.fire({
    title: 'Konfirmasi Impor Data',
    text: modeText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: importForm.value.mode === 'replace' ? '#9333ea' : '#00AA13',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Jalankan Impor!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      executeImport();
    }
  });
};

const executeImport = () => {
  isImporting.value = true;

  const formData = new FormData();
  formData.append('backup_file', importForm.value.backup_file);
  formData.append('mode', importForm.value.mode);

  router.post('/admin/backup/import', formData, {
    forceFormData: true,
    onSuccess: () => {
      isImporting.value = false;
      if (fileInput.value) fileInput.value.value = '';
      importForm.value.backup_file = null;
      Swal.fire({
        icon: 'success',
        title: 'Impor Berhasil!',
        text: 'Data berhasil dipulihkan ke sistem.',
        confirmButtonColor: '#00AA13',
      });
    },
    onError: (errors) => {
      isImporting.value = false;
      const firstErr = Object.values(errors)[0] || 'Gagal memulihkan data backup.';
      Swal.fire({
        icon: 'error',
        title: 'Impor Gagal',
        text: firstErr,
        confirmButtonColor: '#00AA13',
      });
    },
    onFinish: () => {
      isImporting.value = false;
    },
  });
};
</script>
