<template>
  <OnlineLayout>
    <Head title="Pusat Pengaduan & Komplain Pelanggan" />

    <div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
      
      <!-- FORM HEADER -->
      <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-slate-100 space-y-6">
        <div class="space-y-2">
          <span class="px-3.5 py-1.5 bg-rose-50 text-rose-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-rose-200 inline-block">
            ⚠️ Pusat Pengaduan Layanan
          </span>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
            Formulir Komplain Pesanan
          </h2>
          <p class="text-xs text-slate-500 font-medium">
            Jika barang yang Anda terima rusak, kurang, atau salah kirim, silakan kirimkan bukti foto dan video unboxing di bawah ini.
          </p>
        </div>

        <form @submit.prevent="submitComplaint" class="space-y-4">
          <!-- NOMOR PESANAN -->
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nomor Pesanan (Wajib Lunas) <span class="text-rose-500">*</span></label>
            <input 
              v-model="form.order_number" 
              type="text" 
              required 
              placeholder="Contoh: ORD-20260828-XXXXX"
              class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-mono font-bold text-slate-800 text-xs transition uppercase"
            >
          </div>

          <!-- NAMA & HP -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama Pelapor <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.customer_name" 
                type="text" 
                required 
                placeholder="Nama Anda"
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-bold text-slate-800 text-xs transition"
              >
            </div>
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">No. WhatsApp <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.customer_phone" 
                type="tel" 
                required 
                placeholder="08xxxxxxxxxx"
                class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-bold text-slate-800 text-xs transition"
              >
            </div>
          </div>

          <!-- KETERANGAN MASALAH -->
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Rincian Masalah / Komplain <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="form.complaint_text" 
              required 
              rows="4"
              placeholder="Jelaskan secara detail kendala yang dialami, bagian barang yang rusak/kurang, dll..."
              class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-medium text-slate-800 text-xs transition leading-relaxed"
            ></textarea>
          </div>

          <!-- UPLOAD FOTO BUKTI (MAX 25MB) -->
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider">📸 Upload Foto Bukti Barang (Maks. 25MB)</label>
            <input 
              type="file" 
              multiple 
              accept="image/*" 
              @change="handlePhotos"
              class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300"
            >
          </div>

          <!-- UPLOAD VIDEO UNBOXING (MAX 15MB) -->
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider">🎥 Upload Video Unboxing (Maks. 15MB)</label>
            <input 
              type="file" 
              accept="video/*" 
              @change="handleVideo"
              class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300"
            >
          </div>

          <!-- TOMBOL SUBMIT -->
          <div class="pt-4">
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="w-full py-4 bg-rose-600 hover:bg-rose-700 active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-600/25 transition"
            >
              {{ isSubmitting ? 'Mengirimkan Pengaduan...' : '🚨 Kirimkan Komplain Sekarang' }}
            </button>
          </div>
        </form>

      </div>

    </div>
  </OnlineLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import OnlineLayout from '@/Layouts/OnlineLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  order_number: String,
});

const form = ref({
  order_number: props.order_number || '',
  customer_name: '',
  customer_phone: '',
  complaint_text: '',
  photos: [],
  video: null,
});

const isSubmitting = ref(false);

const handlePhotos = (e) => {
  form.value.photos = Array.from(e.target.files);
};

const handleVideo = (e) => {
  form.value.video = e.target.files[0] || null;
};

const submitComplaint = () => {
  isSubmitting.value = true;
  const formData = new FormData();
  formData.append('order_number', form.value.order_number);
  formData.append('customer_name', form.value.customer_name);
  formData.append('customer_phone', form.value.customer_phone);
  formData.append('complaint_text', form.value.complaint_text);

  if (form.value.photos && form.value.photos.length > 0) {
    form.value.photos.forEach((file, idx) => {
      formData.append(`photos[${idx}]`, file);
    });
  }

  if (form.value.video) {
    formData.append('video', form.value.video);
  }

  router.post('/order/complaint', formData, {
    forceFormData: true,
    onSuccess: () => {
      isSubmitting.value = false;
      Swal.fire({
        icon: 'success',
        title: 'Komplain Terkirim',
        text: 'Laporan Anda telah diterima dan akan segera ditinjau oleh tim kami.',
        confirmButtonColor: '#00AA13'
      }).then(() => {
        router.get(`/order/track?order_number=${form.value.order_number}`);
      });
    },
    onError: (errs) => {
      isSubmitting.value = false;
      const first = Object.values(errs)[0] || 'Gagal mengirim pengaduan.';
      Swal.fire('Gagal', first, 'error');
    }
  });
};
</script>
