<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4 relative overflow-hidden text-slate-800">
    <Head title="Staff Login" />

    <!-- SUBTLE BACKGROUND AMBIENT DECORATION -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-100/60 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <!-- CONTAINER LOGIN PUTIH BERSIH & ELEGAN -->
    <div class="relative w-full max-w-md bg-white rounded-[2.5rem] p-8 sm:p-10 shadow-xl border border-slate-100 space-y-6 z-10">
      
      <!-- LOGO & TITLE -->
      <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-2xl bg-[#00AA13] flex items-center justify-center text-white text-2xl font-black mx-auto shadow-lg shadow-emerald-600/30">
          ⚡
        </div>
        <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Staff Login</h2>
        <p class="text-xs text-slate-500 font-medium">Masuk untuk mengelola Kasir POS dan Panel Admin.</p>
      </div>

      <!-- FORM LOGIN -->
      <form @submit.prevent="submitLogin" class="space-y-4 text-xs">
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email Petugas</label>
          <input 
            v-model="form.email" 
            type="email" 
            required 
            placeholder="admin@sultanweb.id"
            class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-bold text-slate-800 text-xs transition"
          >
        </div>

        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi</label>
          <input 
            v-model="form.password" 
            type="password" 
            required 
            placeholder="••••••••"
            class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white font-bold text-slate-800 text-xs transition"
          >
        </div>

        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center space-x-2 cursor-pointer">
            <input 
              type="checkbox" 
              v-model="form.remember" 
              class="w-4 h-4 text-[#00AA13] rounded border-slate-300 focus:ring-[#00AA13]"
            >
            <span class="text-slate-600 font-bold">Ingat Saya</span>
          </label>
        </div>

        <!-- TURNSTILE CAPTCHA -->
        <div v-if="$page.props.turnstile?.enabled" class="pt-2 flex justify-center">
          <div class="cf-turnstile" :data-sitekey="$page.props.turnstile.site_key" data-theme="light"></div>
        </div>

        <button 
          type="submit" 
          :disabled="isSubmitting"
          class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition mt-4"
        >
          {{ isSubmitting ? 'Memverifikasi...' : 'Masuk ke Sistem' }}
        </button>
      </form>

      <div class="text-center pt-2">
        <Link href="/order" class="text-xs font-black text-slate-400 hover:text-slate-700 transition">
          &larr; Kembali ke Toko Online
        </Link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isSubmitting = ref(false);

const submitLogin = () => {
  isSubmitting.value = true;
  
  const turnstileInput = document.querySelector('[name="cf-turnstile-response"]');
  const turnstileToken = turnstileInput ? turnstileInput.value : null;

  router.post('/login', {
    ...form.value,
    'cf-turnstile-response': turnstileToken,
  }, {
    onError: (errs) => {
      isSubmitting.value = false;
      const first = Object.values(errs)[0] || 'Email atau password salah.';
      Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: first,
        confirmButtonColor: '#00AA13'
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};
</script>
