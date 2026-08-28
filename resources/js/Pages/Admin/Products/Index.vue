<template>
  <AdminLayout>
    <template #header>Manajemen Produk & Stok</template>
    <Head title="Produk & Stok" />

    <div class="space-y-6">
      
      <!-- TOOLBAR ATAS: SEARCH & TOMBOL TAMBAH -->
      <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="w-full sm:w-80 relative">
          <input 
            v-model="searchQuery" 
            @input="handleSearch"
            type="text" 
            placeholder="Cari produk atau barcode..."
            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
          >
          <span class="absolute left-3.5 top-3 text-slate-400 text-xs">🔍</span>
        </div>

        <div class="flex items-center space-x-3 w-full sm:w-auto">
          <Link 
            href="/admin/products/create" 
            class="w-full sm:w-auto px-6 py-3 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center justify-center space-x-1.5"
          >
            <span>+ Tambah Produk</span>
          </Link>
        </div>
      </div>

      <!-- TABEL PRODUK -->
      <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
              <tr>
                <th class="p-4 pl-6">Produk</th>
                <th class="p-4">Barcode</th>
                <th class="p-4">Harga Jual</th>
                <th class="p-4">Stok Fisik</th>
                <th class="p-4">Diskon</th>
                <th class="p-4 pr-6 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-slate-50/80 transition">
                <td class="p-4 pl-6 flex items-center space-x-3">
                  <img v-if="product.image_url" :src="product.image_url" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 shrink-0">
                  <div v-else class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl shrink-0">📦</div>
                  <div>
                    <h4 class="font-black text-slate-900 text-xs uppercase">{{ product.name }}</h4>
                    <span v-if="product.gallery_urls && product.gallery_urls.length > 1" class="text-[9px] text-[#00AA13] font-bold">
                      📸 {{ product.gallery_urls.length }} Foto Galeri
                    </span>
                  </div>
                </td>

                <td class="p-4 font-mono font-bold text-slate-600">
                  {{ product.barcode || '-' }}
                </td>

                <td class="p-4 font-black text-[#00880F]">
                  {{ formatRupiah(product.price) }}
                </td>

                <td class="p-4">
                  <span 
                    :class="product.stock > 5 ? 'bg-emerald-50 text-[#00661A] border-emerald-200' : (product.stock > 0 ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-800 border-rose-200')"
                    class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase border"
                  >
                    {{ product.stock }} pcs
                  </span>
                </td>

                <td class="p-4 font-bold text-slate-500">
                  <span v-if="product.discount_percent > 0" class="text-rose-600 font-black">-{{ product.discount_percent }}%</span>
                  <span v-else>-</span>
                </td>

                <td class="p-4 pr-6 text-right space-x-2">
                  <Link 
                    :href="'/admin/products/' + product.id + '/edit'" 
                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase transition"
                  >
                    Edit
                  </Link>

                  <button 
                    @click="deleteProduct(product)"
                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase transition"
                  >
                    Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="products.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400 font-bold italic">
                  Tidak ada data produk yang ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINASI -->
        <div v-if="products.links && products.links.length > 3" class="p-4 border-t border-slate-100 flex justify-center items-center gap-1.5">
          <template v-for="(link, key) in products.links" :key="key">
            <Link 
              v-if="link.url" 
              :href="link.url" 
              v-html="link.label"
              :class="link.active ? 'bg-[#00AA13] text-white font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold'"
              class="px-3 py-1.5 rounded-xl text-xs uppercase transition border border-slate-200"
            />
            <span v-else v-html="link.label" class="px-3 py-1.5 text-xs text-slate-300"></span>
          </template>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  products: Object,
  search: String,
});

const searchQuery = ref(props.search || '');

const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);

let searchTimer = null;
const handleSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    router.get('/admin/products', { search: searchQuery.value }, { preserveState: true, replace: true });
  }, 400);
};

const deleteProduct = (product) => {
  Swal.fire({
    title: 'Hapus Produk?',
    text: `Anda yakin ingin menghapus produk "${product.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e11d48',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((res) => {
    if (res.isConfirmed) {
      router.delete(`/admin/products/${product.id}`, {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Produk telah berhasil dihapus.', 'success');
        }
      });
    }
  });
};
</script>
