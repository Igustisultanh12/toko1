<template>
  <AdminLayout>
    <template #header>Tambah Produk Baru</template>
    <Head title="Tambah Produk" />

    <div class="max-w-4xl mx-auto space-y-6">
      
      <!-- HEADER BAR -->
      <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div>
          <h3 class="text-base font-black text-slate-900 uppercase">Formulir Data Produk</h3>
          <p class="text-xs text-slate-400 font-medium">Lengkapi detail informasi barang, harga jual kasir, foto utama, dan galeri foto produk.</p>
        </div>
        <Link href="/admin/products" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
          &larr; Kembali
        </Link>
      </div>

      <!-- FORM CARD -->
      <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- NAMA BARANG -->
            <div class="md:col-span-2">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama Produk / Barang <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.name" 
                type="text" 
                required 
                placeholder="Contoh: Kemeja PDL Tactical Ripstop"
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-slate-800 text-sm transition"
              >
            </div>

            <!-- FOTO UTAMA PRODUK -->
            <div class="md:col-span-2 bg-slate-50 p-6 rounded-3xl border-2 border-dashed border-slate-200">
              <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-2">Foto Utama / Sampul Produk (Maks. 4MB)</label>
              <div class="flex flex-col sm:flex-row items-center gap-5">
                <img v-if="mainImagePreview" :src="mainImagePreview" class="w-24 h-24 object-cover rounded-2xl border-2 border-[#00AA13] shadow-md">
                <div v-else class="w-24 h-24 bg-slate-200 rounded-2xl flex items-center justify-center text-slate-400 text-3xl border border-slate-300">
                  🖼️
                </div>
                <div class="flex-1 space-y-1">
                  <input 
                    type="file" 
                    accept="image/*" 
                    @change="handleMainImage"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#00AA13] file:text-white hover:file:bg-[#00880F] file:cursor-pointer file:transition"
                  >
                  <p class="text-[10px] text-slate-400 font-bold">Foto sampul yang akan muncul pertama kali di etalase dan kasir.</p>
                </div>
              </div>
            </div>

            <!-- FOTO GALERI TAMBAHAN (MULTI-UPLOAD) -->
            <div class="md:col-span-2 bg-emerald-50/40 p-6 rounded-3xl border-2 border-dashed border-emerald-200 space-y-3">
              <div class="flex justify-between items-center">
                <label class="block text-[10px] font-black text-[#00661A] uppercase tracking-widest">
                  📸 Galeri Foto Tambahan (Bisa Pilih Lebih Dari 1 Foto)
                </label>
                <span class="text-[10px] font-bold text-slate-400">Multi-upload</span>
              </div>

              <input 
                type="file" 
                multiple 
                accept="image/*" 
                @change="handleGalleryImages"
                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#00AA13] file:text-white hover:file:bg-[#00880F] file:cursor-pointer file:transition"
              >

              <div v-if="galleryPreviews.length > 0" class="grid grid-cols-3 sm:grid-cols-6 gap-3 pt-2">
                <div v-for="(img, idx) in galleryPreviews" :key="idx" class="relative aspect-square rounded-2xl overflow-hidden border-2 border-emerald-300 shadow-sm">
                  <img :src="img" class="w-full h-full object-cover">
                  <span class="absolute bottom-1 right-1 px-1.5 py-0.5 bg-black/60 text-white rounded text-[8px] font-mono font-black">#{{ idx + 1 }}</span>
                </div>
              </div>
            </div>

            <!-- KODE BARCODE -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kode Barcode / SKU</label>
              <div class="relative">
                <input 
                  v-model="form.barcode" 
                  type="text" 
                  placeholder="899..."
                  class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-mono font-bold text-slate-800 text-xs transition uppercase"
                >
                <button type="button" @click="generateBarcode" class="absolute right-3 top-3 px-3 py-1.5 bg-emerald-100 text-[#00880F] rounded-xl text-[10px] font-black uppercase hover:bg-[#00AA13] hover:text-white transition">
                  Auto
                </button>
              </div>
            </div>

            <!-- HARGA JUAL -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.price" 
                type="number" 
                required 
                min="0"
                placeholder="10000"
                class="w-full p-4 bg-emerald-50/50 border-2 border-emerald-200 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-[#00880F] text-base transition"
              >
            </div>

            <!-- STOK AWAL -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Stok Awal Barang <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.stock" 
                type="number" 
                required 
                min="0"
                placeholder="10"
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-slate-800 text-xs transition"
              >
            </div>

            <!-- DISKON -->
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Diskon (%)</label>
              <input 
                v-model="form.discount_percent" 
                type="number" 
                min="0" 
                max="100" 
                step="0.1"
                placeholder="0"
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-black text-slate-800 text-xs transition"
              >
            </div>

            <!-- DESKRIPSI LENGKAP -->
            <div class="md:col-span-2">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Deskripsi Lengkap & Spesifikasi Barang</label>
              <textarea 
                v-model="form.description" 
                rows="4" 
                placeholder="Tuliskan deskripsi lengkap produk, rincian bahan, ukuran/size chart, petunjuk pemakaian, dll..."
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-medium text-slate-800 text-xs transition leading-relaxed"
              ></textarea>
            </div>

          </div>

          <!-- TOMBOL AKSI -->
          <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
            <Link href="/admin/products" class="px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-wider transition">
              Batal
            </Link>
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
            >
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Produk & Foto' }}
            </button>
          </div>
        </form>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const form = ref({
  name: '',
  barcode: '',
  price: 0,
  stock: 10,
  discount_percent: 0,
  description: '',
  image: null,
  gallery: [],
});

const mainImagePreview = ref(null);
const galleryPreviews = ref([]);
const isSubmitting = ref(false);

const generateBarcode = () => {
  form.value.barcode = '899' + Math.floor(100000000 + Math.random() * 900000000);
};

const handleMainImage = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 4 * 1024 * 1024) {
      Swal.fire('Ukuran Melebihi Batas', 'Maksimal ukuran foto adalah 4MB.', 'warning');
      e.target.value = '';
      return;
    }
    form.value.image = file;
    const reader = new FileReader();
    reader.onload = (ev) => mainImagePreview.value = ev.target.result;
    reader.readAsDataURL(file);
  }
};

const handleGalleryImages = (e) => {
  galleryPreviews.value = [];
  const files = Array.from(e.target.files);
  form.value.gallery = files;

  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (ev) => galleryPreviews.value.push(ev.target.result);
    reader.readAsDataURL(file);
  });
};

const submitForm = () => {
  isSubmitting.value = true;
  const formData = new FormData();
  formData.append('name', form.value.name);
  formData.append('barcode', form.value.barcode || '');
  formData.append('price', form.value.price);
  formData.append('stock', form.value.stock);
  formData.append('discount_percent', form.value.discount_percent || 0);
  formData.append('description', form.value.description || '');

  if (form.value.image) {
    formData.append('image', form.value.image);
  }

  if (form.value.gallery && form.value.gallery.length > 0) {
    form.value.gallery.forEach((file, idx) => {
      formData.append(`gallery[${idx}]`, file);
    });
  }

  router.post('/admin/products', formData, {
    forceFormData: true,
    onSuccess: () => {
      isSubmitting.value = false;
      Swal.fire('Sukses', 'Produk dan foto galeri berhasil ditambahkan.', 'success');
    },
    onError: (errs) => {
      isSubmitting.value = false;
      const first = Object.values(errs)[0] || 'Gagal menyimpan data.';
      Swal.fire('Error', first, 'error');
    }
  });
};
</script>
