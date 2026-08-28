<template>
  <div class="min-h-screen bg-gradient-to-br from-[#001A06] via-[#002B0B] to-[#011405] flex items-center justify-center p-4 relative overflow-hidden text-white">
    <Head title="Staff Login" />

    <!-- GLOWING BACKGROUND MESH ORBS -->
    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-[#00AA13]/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- GLASSMORPHISM CARD (TRANSPARAN FROSTED GLASS) -->
    <div class="relative w-full max-w-md bg-white/[0.08] backdrop-blur-2xl rounded-[2.5rem] p-8 sm:p-10 shadow-2xl border border-white/15 space-y-6 z-10">
      
      <!-- LOGO & TITLE -->
      <div class="text-center space-y-2.5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#00AA13] to-[#00661A] flex items-center justify-center text-white text-2xl font-black mx-auto shadow-xl shadow-emerald-600/40 border border-white/20">
          ⚡
        </div>
        <h2 class="text-2xl font-black text-white uppercase tracking-tight">Staff Login</h2>
        <p class="text-xs text-emerald-200/70 font-medium">Masuk untuk mengelola Kasir POS dan Panel Admin.</p>
      </div>

      <!-- FORM LOGIN -->
      <form @submit.prevent="submitLogin" class="space-y-4 text-xs">
        <div>
          <label class="block text-[10px] font-black text-emerald-200/80 uppercase tracking-widest mb-1.5 ml-1">Email Petugas</label>
          <input 
            v-model="form.email" 
            type="email" 
            required 
            placeholder="admin@sultanweb.id"
            class="w-full p-4 bg-white/10 border border-white/15 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white/15 font-bold text-white placeholder-white/30 text-xs transition backdrop-blur-md"
          >
        </div>

        <div>
          <label class="block text-[10px] font-black text-emerald-200/80 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi</label>
          <input 
            v-model="form.password" 
            type="password" 
            required 
            placeholder="••••••••"
            class="w-full p-4 bg-white/10 border border-white/15 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white/15 font-bold text-white placeholder-white/30 text-xs transition backdrop-blur-md"
          >
        </div>

        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center space-x-2 cursor-pointer group">
            <input 
              type="checkbox" 
              v-model="form.remember" 
              class="w-4 h-4 rounded bg-white/10 border-white/20 text-[#00AA13] focus:ring-0 focus:ring-offset-0"
            >
            <span class="text-emerald-100/80 font-bold group-hover:text-white transition">Ingat Saya</span>
          </label>
        </div>

        <!-- TURNSTILE CAPTCHA -->
        <div v-if="$page.props.turnstile?.enabled" class="pt-2 flex justify-center">
          <div class="cf-turnstile" :data-sitekey="$page.props.turnstile.site_key" data-theme="dark"></div>
        </div>

        <button 
          type="submit" 
          :disabled="isSubmitting"
          class="w-full py-4 bg-gradient-to-r from-[#00AA13] to-[#00880F] hover:from-[#00880F] hover:to-[#00661A] active:scale-95 disabled:opacity-50 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-emerald-600/30 border border-emerald-400/30 transition mt-4"
        >
          {{ isSubmitting ? 'Memverifikasi...' : 'Masuk ke Sistem' }}
        </button>
      </form>

      <div class="text-center pt-2">
        <Link href="/order" class="text-xs font-black text-emerald-300/70 hover:text-emerald-200 transition inline-flex items-center space-x-1">
          <span>&larr; Kembali ke Toko Online</span>
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
