<template>
  <AdminLayout>
    <template #header>Manajemen Akun Petugas</template>
    <Head title="Akun Petugas" />

    <div class="space-y-6">
      
      <!-- TOOLBAR -->
      <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex justify-between items-center">
        <div>
          <h3 class="text-base font-black text-slate-900 uppercase">Daftar Akun Kasir & Admin</h3>
          <p class="text-xs text-slate-400 font-medium">Kelola hak akses dan kata sandi petugas kasir.</p>
        </div>

        <button 
          @click="openCreateModal"
          class="px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center space-x-1.5"
        >
          <span>+ Tambah Petugas</span>
        </button>
      </div>

      <!-- TABEL USERS -->
      <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
            <tr>
              <th class="p-4 pl-6">Nama Petugas</th>
              <th class="p-4">Email Login</th>
              <th class="p-4">Peran (Role)</th>
              <th class="p-4 pr-6 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            <tr v-for="u in users" :key="u.id" class="hover:bg-slate-50/80 transition">
              <td class="p-4 pl-6 flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-[#00661A] font-black flex items-center justify-center text-xs uppercase">
                  {{ u.name.charAt(0) }}
                </div>
                <span class="font-black text-slate-900">{{ u.name }}</span>
              </td>

              <td class="p-4 font-mono font-bold text-slate-600">
                {{ u.email }}
              </td>

              <td class="p-4">
                <span 
                  :class="u.role === 'admin' ? 'bg-purple-100 text-purple-800 border-purple-200' : 'bg-emerald-100 text-[#00661A] border-emerald-200'"
                  class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase border"
                >
                  {{ u.role }}
                </span>
              </td>

              <td class="p-4 pr-6 text-right space-x-2">
                <button 
                  @click="openEditModal(u)"
                  class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase transition"
                >
                  Edit
                </button>
                <button 
                  v-if="u.id !== $page.props.auth?.user?.id"
                  @click="deleteUser(u)"
                  class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- MODAL TAMBAH / EDIT USER (TELEPORT TO BODY) -->
      <Teleport to="body">
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isModalOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center">
              <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div class="relative w-full max-w-md bg-white rounded-3xl p-6 space-y-6 shadow-2xl border border-slate-100 text-left z-10">
                  <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="font-black text-base uppercase text-slate-900">
                      {{ editingUser ? 'Edit Akun Petugas' : 'Tambah Akun Petugas' }}
                    </h3>
                    <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-700 font-black">✕</button>
                  </div>

                  <form @submit.prevent="submitUser" class="space-y-4 text-xs">
                    <div>
                      <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap</label>
                      <input v-model="userForm.name" type="text" required class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:border-[#00AA13]">
                    </div>

                    <div>
                      <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email Login</label>
                      <input v-model="userForm.email" type="email" required class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:border-[#00AA13]">
                    </div>

                    <div>
                      <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Peran (Role)</label>
                      <select v-model="userForm.role" required class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:border-[#00AA13]">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                      </select>
                    </div>

                    <div>
                      <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                        {{ editingUser ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password' }}
                      </label>
                      <input v-model="userForm.password" :required="!editingUser" type="password" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:border-[#00AA13]">
                    </div>

                    <button 
                      type="submit" 
                      class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition mt-2"
                    >
                      Simpan Akun
                    </button>
                  </form>
                </div>
              </Transition>
            </div>
          </div>
        </Transition>
      </Teleport>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  users: Array,
});

const isModalOpen = ref(false);
const editingUser = ref(null);

const userForm = ref({
  name: '',
  email: '',
  role: 'kasir',
  password: '',
});

const openCreateModal = () => {
  editingUser.value = null;
  userForm.value = { name: '', email: '', role: 'kasir', password: '' };
  isModalOpen.value = true;
};

const openEditModal = (u) => {
  editingUser.value = u;
  userForm.value = { name: u.name, email: u.email, role: u.role, password: '' };
  isModalOpen.value = true;
};

const submitUser = () => {
  if (editingUser.value) {
    router.put(`/admin/users/${editingUser.value.id}`, userForm.value, {
      onSuccess: () => {
        isModalOpen.value = false;
        Swal.fire('Sukses', 'Akun berhasil diperbarui.', 'success');
      }
    });
  } else {
    router.post('/admin/users', userForm.value, {
      onSuccess: () => {
        isModalOpen.value = false;
        Swal.fire('Sukses', 'Akun petugas berhasil ditambahkan.', 'success');
      }
    });
  }
};

const deleteUser = (u) => {
  Swal.fire({
    title: 'Hapus Akun?',
    text: `Hapus akun "${u.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e11d48',
    confirmButtonText: 'Ya, Hapus'
  }).then((res) => {
    if (res.isConfirmed) {
      router.delete(`/admin/users/${u.id}`, {
        onSuccess: () => Swal.fire('Terhapus', 'Akun berhasil dihapus.', 'success')
      });
    }
  });
};
</script>
