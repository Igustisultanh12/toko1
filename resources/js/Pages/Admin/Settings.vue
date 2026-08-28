<template>
  <AdminLayout>
    <template #header>Pengaturan Sistem & Toko</template>
    <Head title="Pengaturan Toko" />

    <div class="max-w-4xl mx-auto space-y-6">
      
      <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm space-y-8">
        <div>
          <h3 class="text-base font-black text-slate-900 uppercase">Pengaturan Toko & Gateway Pembayaran</h3>
          <p class="text-xs text-slate-400 font-medium">Konfigurasi nama toko, alamat, DOKU Payment Gateway, dan Cloudflare Turnstile.</p>
        </div>

        <form @submit.prevent="saveSettings" class="space-y-8">
          
          <!-- INFORMASI TOKO -->
          <div class="space-y-4">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
              <span>🏪</span>
              <span>Informasi Toko</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Toko / Usaha</label>
                <input 
                  v-model="form.shop_name" 
                  type="text" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                >
              </div>

              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Telepon / WhatsApp</label>
                <input 
                  v-model="form.shop_phone" 
                  type="text" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat Toko</label>
                <textarea 
                  v-model="form.shop_address" 
                  rows="2" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 outline-none focus:border-[#00AA13]"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- DOKU PAYMENT GATEWAY CONFIGURATION -->
          <div class="space-y-4">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
              <span>⚡</span>
              <span>DOKU Jokul Payment Gateway (QRIS)</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">DOKU Client ID</label>
                <input 
                  v-model="form.doku_client_id" 
                  type="text" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                >
              </div>

              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">DOKU Secret Key</label>
                <input 
                  v-model="form.doku_secret_key" 
                  type="password" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Mode Lingkungan (Environment)</label>
                <select 
                  v-model="form.doku_environment" 
                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                >
                  <option value="sandbox">Sandbox (Testing / Uji Coba)</option>
                  <option value="production">Production (Live Transaksi Nyata)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- CLOUDFLARE TURNSTILE CAPTCHA -->
          <div class="space-y-4">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
              <span>🛡️</span>
              <span>Cloudflare Turnstile CAPTCHA</span>
            </h4>

            <div class="space-y-3">
              <label class="flex items-center space-x-3 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="form.turnstile_enabled" 
                  class="w-5 h-5 text-[#00AA13] rounded-lg focus:ring-[#00AA13]"
                >
                <span class="text-xs font-black text-slate-800 uppercase">Aktifkan Proteksi Cloudflare Turnstile</span>
              </label>

              <div v-if="form.turnstile_enabled" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Site Key</label>
                  <input 
                    v-model="form.turnstile_site_key" 
                    type="text" 
                    class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                  >
                </div>
                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Secret Key</label>
                  <input 
                    v-model="form.turnstile_secret_key" 
                    type="password" 
                    class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="px-8 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition"
            >
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Seluruh Pengaturan' }}
            </button>
          </div>

        </form>
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
  settings: Object,
});

const form = ref({
  shop_name: props.settings?.shop_name || 'SIBALOG STORE',
  shop_phone: props.settings?.shop_phone || '',
  shop_address: props.settings?.shop_address || '',
  doku_client_id: props.settings?.doku_client_id || '',
  doku_secret_key: props.settings?.doku_secret_key || '',
  doku_environment: props.settings?.doku_environment || 'sandbox',
  turnstile_enabled: props.settings?.turnstile_enabled === '1' || props.settings?.turnstile_enabled === true,
  turnstile_site_key: props.settings?.turnstile_site_key || '',
  turnstile_secret_key: props.settings?.turnstile_secret_key || '',
});

const isSubmitting = ref(false);

const saveSettings = () => {
  isSubmitting.value = true;
  router.post('/admin/settings', {
    ...form.value,
    turnstile_enabled: form.value.turnstile_enabled ? '1' : '0',
  }, {
    onSuccess: () => {
      isSubmitting.value = false;
      Swal.fire('Sukses', 'Pengaturan berhasil disimpan.', 'success');
    },
    onError: (errs) => {
      isSubmitting.value = false;
      Swal.fire('Error', 'Gagal menyimpan pengaturan.', 'error');
    }
  });
};
</script>
