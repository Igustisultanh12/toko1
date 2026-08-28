<template>
  <OnlineLayout>
    <Head title="Katalog Belanja Online" />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-8">
      
      <!-- HERO BANNER -->
      <div class="bg-gradient-to-r from-[#00360D] via-[#004D13] to-[#00661A] rounded-[2.5rem] p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-3">
          <span class="px-3.5 py-1.5 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-wider text-emerald-200 border border-white/10 inline-block">
            ⚡ Belanja Instan &bull; Produk Original
          </span>
          <h2 class="text-2xl sm:text-4xl font-black uppercase tracking-tight leading-tight">
            {{ $page.props.shop?.shop_name || 'Selamat Datang di Toko Kami' }}
          </h2>
          <p class="text-xs sm:text-sm text-emerald-100/90 font-medium leading-relaxed">
            Klik produk untuk melihat foto galeri, membaca rincian deskripsi, stok fisik, dan bayar instan via QRIS DOKU.
          </p>
        </div>
        <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-x-10 translate-y-10">
          <svg class="w-80 h-80 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
        </div>
      </div>

      <!-- SEARCH BAR & KERANJANG TRIGGER -->
      <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="w-full sm:w-96 relative">
          <input 
            v-model="searchQuery" 
            @input="handleSearch" 
            type="text" 
            placeholder="Cari nama barang atau barcode..."
            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13] focus:bg-white transition"
          >
          <div class="absolute left-4 top-3.5 text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
          </div>
        </div>

        <button 
          @click="isCartOpen = true" 
          class="w-full sm:w-auto px-6 py-3.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center justify-center space-x-2.5"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2.5"/></svg>
          <span>Keranjang Belanja</span>
          <span class="bg-white text-[#00880F] px-2.5 py-0.5 rounded-full text-[11px] font-black">{{ totalItems }}</span>
        </button>
      </div>

      <!-- GRID KATALOG PRODUK -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        <div 
          v-for="product in products.data" 
          :key="product.id"
          @click="openProductModal(product)"
          class="bg-white rounded-[2rem] border border-slate-100 p-4 sm:p-5 shadow-sm hover:shadow-xl hover:border-emerald-200 transition-all duration-300 flex flex-col justify-between group cursor-pointer relative"
        >
          <div class="space-y-2.5">
            <!-- FOTO PRODUK DENGAN BADGE -->
            <div class="relative w-full h-40 bg-slate-50 rounded-2xl overflow-hidden mb-3 flex items-center justify-center border border-slate-100 group-hover:border-emerald-300 transition">
              <img 
                v-if="product.image_url" 
                :src="product.image_url" 
                :alt="product.name" 
                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
              >
              <div v-else class="text-4xl text-slate-300">🛍️</div>

              <!-- BADGE DISKON -->
              <span 
                v-if="product.discount_percent > 0" 
                class="absolute top-2 right-2 text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-rose-500 text-white shadow-md"
              >
                -{{ product.discount_percent }}%
              </span>

              <!-- BADGE MULTI FOTO -->
              <span 
                v-if="product.gallery_urls && product.gallery_urls.length > 1" 
                class="absolute top-2 left-2 text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-md text-white flex items-center space-x-1 shadow-sm"
              >
                <span>📸</span>
                <span>{{ product.gallery_urls.length }} Foto</span>
              </span>

              <!-- BADGE STOK -->
              <span 
                class="absolute bottom-2 left-2 text-[9px] font-black uppercase px-2 py-0.5 rounded-md backdrop-blur-sm shadow-sm"
                :class="product.stock > 5 ? 'bg-black/60 text-white' : 'bg-amber-600 text-white'"
              >
                Stok: {{ product.stock }}
              </span>
            </div>

            <!-- NAMA PRODUK -->
            <div>
              <h3 class="font-black text-slate-900 text-xs sm:text-sm uppercase tracking-tight line-clamp-2 group-hover:text-[#00AA13] transition">
                {{ product.name }}
              </h3>
              <p v-if="product.barcode" class="font-mono text-[9px] text-slate-400 font-bold mt-0.5">{{ product.barcode }}</p>
            </div>
          </div>

          <!-- HARGA & TOMBOL -->
          <div class="mt-4 pt-3 border-t border-slate-50 space-y-2.5">
            <div>
              <span v-if="product.discount_percent > 0" class="text-[10px] text-slate-400 line-through font-bold block">
                {{ formatRupiah(product.price) }}
              </span>
              <span class="text-sm sm:text-base font-black text-[#00880F]">
                {{ formatRupiah(calculateFinalPrice(product)) }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-1.5">
              <button 
                type="button" 
                @click.stop="openProductModal(product)"
                class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-black text-[10px] uppercase tracking-wider transition text-center"
              >
                Detail
              </button>
              <button 
                type="button" 
                @click.stop="addToCart(product)"
                class="w-full py-2 bg-emerald-50 hover:bg-[#00AA13] text-[#00880F] hover:text-white rounded-xl font-black text-[10px] uppercase tracking-wider transition-all text-center"
              >
                + Keranjang
              </button>
            </div>
          </div>
        </div>

        <div v-if="products.data.length === 0" class="col-span-full py-16 text-center text-slate-400 font-bold uppercase text-xs italic bg-white rounded-[2rem] border border-slate-100">
          Tidak ada produk yang cocok dengan pencarian.
        </div>
      </div>

      <!-- PAGINASI -->
      <div v-if="products.links && products.links.length > 3" class="p-6 bg-white rounded-[2rem] border border-slate-100 flex justify-center items-center gap-2">
        <template v-for="(link, key) in products.links" :key="key">
          <Link 
            v-if="link.url" 
            :href="link.url" 
            v-html="link.label"
            :class="link.active ? 'bg-[#00AA13] text-white font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold'"
            class="px-3.5 py-2 rounded-xl text-xs uppercase transition border border-slate-200"
          />
          <span v-else v-html="link.label" class="px-3.5 py-2 text-xs text-slate-300"></span>
        </template>
      </div>

      <!-- FLOATING QUICK CHECKOUT BAR (KUNCI KEMUDAHAN USER) -->
      <div 
        v-if="totalItems > 0 && !isCartOpen" 
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-[92%] max-w-lg bg-slate-900/95 backdrop-blur-md text-white p-3.5 sm:p-4 rounded-3xl shadow-2xl border border-white/10 flex items-center justify-between transition-all duration-300 animate-bounce-subtle"
      >
        <div class="flex items-center space-x-3 pl-2">
          <div class="w-10 h-10 rounded-2xl bg-[#00AA13] flex items-center justify-center text-white text-lg font-black shadow-md">
            🛒
          </div>
          <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ totalItems }} Barang Dipilih</p>
            <p class="text-sm sm:text-base font-black text-emerald-300">{{ formatRupiah(totalPrice) }}</p>
          </div>
        </div>

        <button 
          @click="isCartOpen = true" 
          class="px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center space-x-1.5"
        >
          <span>Buka Keranjang</span>
          <span>→</span>
        </button>
      </div>

      <!-- ========================================================== -->
      <!-- MODAL DETAIL PRODUK (TELEPORT TO BODY DENGAN TRANSISI) -->
      <!-- ========================================================== -->
      <Teleport to="body">
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div 
            v-if="isDetailModalOpen && selectedProduct" 
            class="fixed inset-0 z-50 overflow-y-auto"
          >
            <!-- BACKDROP GELAP -->
            <div 
              class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
              @click="closeProductModal"
            ></div>

            <!-- MODAL CONTAINER -->
            <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center">
              <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div class="relative w-full max-w-3xl transform overflow-hidden rounded-[2.5rem] sm:rounded-[3rem] bg-white text-left shadow-2xl border border-slate-100 transition-all my-auto max-h-[92vh] flex flex-col z-10">
                  
                  <!-- HEADER MODAL -->
                  <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                    <div class="flex items-center space-x-2">
                      <span class="px-3 py-1 bg-emerald-50 text-[#00661A] text-[10px] font-black uppercase rounded-full tracking-wider border border-emerald-200">
                        🛍️ Rincian Produk
                      </span>
                      <span class="text-xs text-slate-400 font-bold">&bull; Toko Online Resmi</span>
                    </div>
                    <button 
                      @click="closeProductModal" 
                      class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-900 rounded-full hover:bg-slate-100 text-sm font-black transition"
                    >
                      ✕
                    </button>
                  </div>

                  <!-- BODY MODAL -->
                  <div class="flex-1 overflow-y-auto p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8">
                      
                      <!-- KOLOM KIRI: GALERI FOTO (5 SPAN) -->
                      <div class="md:col-span-5 space-y-3">
                        <div class="relative aspect-square w-full bg-slate-50 rounded-3xl overflow-hidden border-2 border-slate-100 shadow-inner flex items-center justify-center group">
                          <img 
                            v-if="activeGalleryUrls.length > 0" 
                            :src="activeGalleryUrls[selectedImageIndex] || selectedProduct.image_url" 
                            :alt="selectedProduct.name" 
                            class="w-full h-full object-contain p-2 bg-white transition-all duration-300"
                          >
                          <div v-else class="text-6xl text-slate-300">🛍️</div>

                          <!-- BADGE DISKON -->
                          <span 
                            v-if="selectedProduct.discount_percent > 0" 
                            class="absolute top-3 right-3 text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-rose-500 text-white shadow-lg"
                          >
                            -{{ selectedProduct.discount_percent }}%
                          </span>

                          <!-- TOMBOL PREV/NEXT -->
                          <template v-if="activeGalleryUrls.length > 1">
                            <button 
                              type="button" 
                              @click="prevImage" 
                              class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-800 font-black shadow-md flex items-center justify-center transition border border-slate-200"
                            >
                              ‹
                            </button>
                            <button 
                              type="button" 
                              @click="nextImage" 
                              class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-800 font-black shadow-md flex items-center justify-center transition border border-slate-200"
                            >
                              ›
                            </button>
                            <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/60 backdrop-blur-sm text-white text-[9px] font-mono font-black rounded-lg">
                              {{ selectedImageIndex + 1 }} / {{ activeGalleryUrls.length }} Foto
                            </span>
                          </template>
                        </div>

                        <!-- THUMBNAILS STRIP -->
                        <div v-if="activeGalleryUrls.length > 1" class="flex items-center gap-2 overflow-x-auto pb-1">
                          <button 
                            v-for="(imgUrl, idx) in activeGalleryUrls" 
                            :key="idx"
                            @click="selectedImageIndex = idx"
                            :class="selectedImageIndex === idx ? 'border-2 border-[#00AA13] shadow-md ring-2 ring-emerald-300' : 'border border-slate-200 opacity-60 hover:opacity-100'"
                            class="w-14 h-14 rounded-xl overflow-hidden shrink-0 transition-all bg-white"
                          >
                            <img :src="imgUrl" class="w-full h-full object-cover">
                          </button>
                        </div>
                      </div>

                      <!-- KOLOM KANAN: DETAIL INFO (7 SPAN) -->
                      <div class="md:col-span-7 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                          <!-- BARCODE & STOK -->
                          <div class="flex flex-wrap items-center gap-2">
                            <span v-if="selectedProduct.barcode" class="font-mono text-[10px] font-black text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                              SKU: {{ selectedProduct.barcode }}
                            </span>

                            <span 
                              :class="selectedProduct.stock > 5 ? 'bg-emerald-50 text-[#00661A] border-emerald-200' : (selectedProduct.stock > 0 ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-800 border-rose-200')"
                              class="text-[10px] font-black uppercase px-2.5 py-1 rounded-lg border inline-flex items-center space-x-1"
                            >
                              <span v-if="selectedProduct.stock > 5">🟢 Stok Tersedia ({{ selectedProduct.stock }} pcs)</span>
                              <span v-else-if="selectedProduct.stock > 0">🟠 Sisa Sedikit ({{ selectedProduct.stock }} pcs)</span>
                              <span v-else>🔴 Stok Habis</span>
                            </span>
                          </div>

                          <!-- NAMA BARANG -->
                          <h2 class="font-black text-slate-900 text-lg sm:text-2xl uppercase tracking-tight leading-snug">
                            {{ selectedProduct.name }}
                          </h2>

                          <!-- HARGA -->
                          <div class="bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-100 space-y-0.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Harga Satuan</span>
                            <div class="flex items-baseline space-x-2">
                              <span v-if="selectedProduct.discount_percent > 0" class="text-xs text-slate-400 line-through font-bold">
                                {{ formatRupiah(selectedProduct.price) }}
                              </span>
                              <span class="text-2xl font-black text-[#00880F]">
                                {{ formatRupiah(calculateFinalPrice(selectedProduct)) }}
                              </span>
                            </div>
                          </div>

                          <!-- DESKRIPSI LENGKAP -->
                          <div class="space-y-1.5 pt-1">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi & Keterangan Barang:</h4>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 max-h-44 overflow-y-auto text-xs font-medium text-slate-700 leading-relaxed whitespace-pre-line">
                              <p v-if="selectedProduct.description && selectedProduct.description.trim() !== ''">
                                {{ selectedProduct.description }}
                              </p>
                              <p v-else class="text-slate-400 italic font-bold">
                                Produk original berkualitas tinggi dari toko kami. Barang dicek dan dikemas rapi sebelum dikirimkan ke alamat Anda.
                              </p>
                            </div>
                          </div>
                        </div>

                        <!-- KUANTITAS & AKSI -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                          <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">Jumlah Beli:</span>
                            
                            <div class="flex items-center space-x-2 bg-slate-100 p-1 rounded-2xl">
                              <button 
                                type="button" 
                                @click="detailQuantity > 1 ? detailQuantity-- : null" 
                                class="w-8 h-8 rounded-xl bg-white hover:bg-slate-200 text-slate-900 font-black text-sm shadow-sm transition"
                              >
                                -
                              </button>
                              <span class="w-8 text-center font-black text-sm text-slate-900">{{ detailQuantity }}</span>
                              <button 
                                type="button" 
                                @click="detailQuantity < selectedProduct.stock ? detailQuantity++ : null" 
                                class="w-8 h-8 rounded-xl bg-white hover:bg-slate-200 text-slate-900 font-black text-sm shadow-sm transition"
                              >
                                +
                              </button>
                            </div>
                          </div>

                          <div class="grid grid-cols-2 gap-2.5">
                            <button 
                              type="button" 
                              @click="addDetailToCart(false)"
                              class="py-3.5 bg-emerald-50 hover:bg-emerald-100 text-[#00661A] font-black text-xs uppercase tracking-wider rounded-2xl border border-emerald-200 transition shadow-sm flex items-center justify-center space-x-1.5"
                            >
                              <span>+ Keranjang</span>
                            </button>
                            
                            <button 
                              type="button" 
                              @click="addDetailToCart(true)"
                              class="py-3.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition flex items-center justify-center space-x-1.5"
                            >
                              <span>⚡ Beli Sekarang</span>
                            </button>
                          </div>
                        </div>

                      </div>

                    </div>
                  </div>

                </div>
              </Transition>
            </div>
          </div>
        </Transition>
      </Teleport>

      <!-- ========================================================== -->
      <!-- DRAWER KERANJANG & CHECKOUT (TELEPORT TO BODY SLIDE-OVER) -->
      <!-- ========================================================== -->
      <Teleport to="body">
        <div v-if="isCartOpen" class="fixed inset-0 z-50 overflow-hidden">
          <!-- BACKDROP ANIMASI -->
          <Transition
            appear
            enter-active-class="transition-opacity duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div 
              class="fixed inset-0 bg-black/60 backdrop-blur-sm"
              @click="isCartOpen = false"
            ></div>
          </Transition>

          <!-- DRAWER PANEL SLIDE-IN DARI KANAN -->
          <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <Transition
              appear
              enter-active-class="transform transition ease-out duration-300"
              enter-from-class="translate-x-full"
              enter-to-class="translate-x-0"
              leave-active-class="transform transition ease-in duration-200"
              leave-from-class="translate-x-0"
              leave-to-class="translate-x-full"
            >
              <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col z-10 border-l border-slate-100">
                
                <!-- DRAWER HEADER -->
                <div class="flex justify-between items-center p-5 sm:p-6 border-b border-slate-100 bg-white shrink-0">
                  <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#00880F] flex items-center justify-center text-lg font-black">
                      🛒
                    </div>
                    <div>
                      <h3 class="font-black text-slate-900 uppercase text-sm leading-none">Keranjang Belanja</h3>
                      <p class="text-[10px] text-slate-400 font-bold mt-1">{{ totalItems }} Produk Dipilih</p>
                    </div>
                  </div>
                  <button 
                    @click="isCartOpen = false" 
                    class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-800 rounded-full hover:bg-slate-100 text-sm font-black transition"
                  >
                    ✕
                  </button>
                </div>

                <!-- DRAWER BODY -->
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
                  
                  <!-- LIST ITEM -->
                  <div class="space-y-3">
                    <div 
                      v-for="(item, index) in cart" 
                      :key="item.id"
                      class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 flex justify-between items-center hover:border-emerald-200 transition"
                    >
                      <div class="flex items-center space-x-2.5 flex-1 pr-2">
                        <img v-if="item.image_url" :src="item.image_url" class="w-11 h-11 rounded-xl object-cover border border-slate-200 shrink-0 bg-white">
                        <div v-else class="w-11 h-11 rounded-xl bg-slate-200 flex items-center justify-center text-base shrink-0">🛍️</div>
                        <div class="min-w-0">
                          <h4 class="font-black text-slate-800 text-xs leading-tight line-clamp-1">{{ item.name }}</h4>
                          <span class="text-[11px] font-bold text-[#00880F] block mt-0.5">{{ formatRupiah(item.price) }}</span>
                        </div>
                      </div>

                      <div class="flex items-center space-x-1.5 shrink-0">
                        <button @click="decreaseQty(index)" class="w-7 h-7 bg-white border border-slate-200 rounded-lg font-bold text-xs hover:bg-slate-100 flex items-center justify-center transition">-</button>
                        <span class="font-black text-xs text-slate-800 w-5 text-center">{{ item.quantity }}</span>
                        <button @click="increaseQty(index)" class="w-7 h-7 bg-white border border-slate-200 rounded-lg font-bold text-xs hover:bg-slate-100 flex items-center justify-center transition">+</button>
                        <button @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 p-1.5 text-xs font-bold transition">🗑️</button>
                      </div>
                    </div>

                    <!-- EMPTY STATE -->
                    <div v-if="cart.length === 0" class="text-center py-16 px-4 space-y-4">
                      <div class="w-16 h-16 bg-emerald-50 text-[#00AA13] rounded-3xl flex items-center justify-center text-3xl mx-auto border border-emerald-100">
                        🛒
                      </div>
                      <div class="space-y-1">
                        <h4 class="font-black text-slate-800 text-sm">Keranjang Anda Masih Kosong</h4>
                        <p class="text-xs text-slate-400 font-medium max-w-xs mx-auto">Silakan klik produk di etalase untuk memasukkan barang ke keranjang.</p>
                      </div>
                      <button 
                        @click="isCartOpen = false" 
                        class="px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-[#00880F] rounded-xl font-black text-xs uppercase tracking-wider transition inline-flex items-center space-x-1.5"
                      >
                        <span>🛍️ Mulai Belanja</span>
                      </button>
                    </div>
                  </div>

                  <!-- FORM INFORMASI PENGIRIMAN -->
                  <div v-if="cart.length > 0" class="pt-4 border-t border-slate-100 space-y-4">
                    <form @submit.prevent="submitCheckout" class="space-y-3">
                      <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Pengiriman Pembeli</h4>
                      
                      <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1">Nama Lengkap Penerima <span class="text-rose-500">*</span></label>
                        <input 
                          v-model="checkoutForm.customer_name" 
                          type="text" 
                          required 
                          placeholder="Contoh: Ibu Rina Hartati"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                        >
                      </div>

                      <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <input 
                          v-model="checkoutForm.customer_phone" 
                          type="tel" 
                          required 
                          placeholder="08xxxxxxxxxx"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                        >
                      </div>

                      <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1">Alamat Lengkap Pengiriman <span class="text-rose-500">*</span></label>
                        <textarea 
                          v-model="checkoutForm.customer_address" 
                          required 
                          rows="2" 
                          placeholder="Jl. Raya No..., RT/RW, Kelurahan, Kecamatan, Kota"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                        ></textarea>
                      </div>

                      <div class="grid grid-cols-2 gap-2">
                        <div>
                          <label class="block text-[10px] font-bold text-slate-600 mb-1">Pilihan Ekspedisi <span class="text-rose-500">*</span></label>
                          <select 
                            v-model="checkoutForm.courier" 
                            required 
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                          >
                            <option value="J&T Express">J&T Express</option>
                            <option value="JNE Reguler">JNE Reguler</option>
                            <option value="SiCepat Express">SiCepat Express</option>
                            <option value="Kurir Toko / Ambil Sendiri">Kurir Toko / Ambil Sendiri</option>
                          </select>
                        </div>
                        <div>
                          <label class="block text-[10px] font-bold text-slate-600 mb-1">Catatan (Opsional)</label>
                          <input 
                            v-model="checkoutForm.customer_notes" 
                            type="text" 
                            placeholder="Titip di pos..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-[#00AA13]"
                          >
                        </div>
                      </div>

                      <!-- CLOUDFLARE TURNSTILE CAPTCHA -->
                      <div v-if="$page.props.turnstile?.enabled" class="pt-3 flex justify-center">
                        <div class="cf-turnstile" :data-sitekey="$page.props.turnstile.site_key" data-theme="light"></div>
                      </div>
                    </form>
                  </div>

                </div>

                <!-- DRAWER FOOTER -->
                <div v-if="cart.length > 0" class="p-5 sm:p-6 border-t border-slate-100 bg-white shrink-0 shadow-lg space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Tagihan:</span>
                    <span class="text-lg font-black text-[#00AA13]">{{ formatRupiah(totalPrice) }}</span>
                  </div>

                  <button 
                    @click="submitCheckout" 
                    :disabled="isSubmitting"
                    class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 disabled:opacity-50 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/25 transition flex items-center justify-center space-x-2"
                  >
                    <span>{{ isSubmitting ? 'Memproses Pesanan...' : '💳 Buat Pesanan & Bayar QRIS' }}</span>
                  </button>
                </div>

              </div>
            </Transition>
          </div>
        </div>
      </Teleport>

    </div>
  </OnlineLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import OnlineLayout from '@/Layouts/OnlineLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  products: Object,
  search: String,
});

const searchQuery = ref(props.search || '');
const cart = ref([]);
const isCartOpen = ref(false);
const isDetailModalOpen = ref(false);
const selectedProduct = ref(null);
const selectedImageIndex = ref(0);
const detailQuantity = ref(1);
const isSubmitting = ref(false);

const checkoutForm = ref({
  customer_name: '',
  customer_phone: '',
  customer_address: '',
  customer_notes: '',
  courier: 'J&T Express',
});

const activeGalleryUrls = computed(() => {
  if (!selectedProduct.value) return [];
  if (selectedProduct.value.gallery_urls && selectedProduct.value.gallery_urls.length > 0) {
    return selectedProduct.value.gallery_urls;
  }
  return selectedProduct.value.image_url ? [selectedProduct.value.image_url] : [];
});

const totalItems = computed(() => cart.value.reduce((sum, item) => sum + item.quantity, 0));
const totalPrice = computed(() => cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0));

const formatRupiah = (val) => {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
};

const calculateFinalPrice = (product) => {
  if (!product) return 0;
  if (product.discount_percent > 0) {
    return product.price - (product.price * (product.discount_percent / 100));
  }
  return product.price;
};

let searchTimeout = null;
const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/order', { search: searchQuery.value }, { preserveState: true, replace: true });
  }, 400);
};

const openProductModal = (product) => {
  selectedProduct.value = product;
  selectedImageIndex.value = 0;
  detailQuantity.value = 1;
  isDetailModalOpen.value = true;
};

const closeProductModal = () => {
  isDetailModalOpen.value = false;
  selectedProduct.value = null;
};

const prevImage = () => {
  const total = activeGalleryUrls.value.length;
  if (total > 0) {
    selectedImageIndex.value = (selectedImageIndex.value - 1 + total) % total;
  }
};

const nextImage = () => {
  const total = activeGalleryUrls.value.length;
  if (total > 0) {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % total;
  }
};

const addToCart = (product) => {
  const finalPrice = calculateFinalPrice(product);
  const existing = cart.value.find(item => item.id === product.id);
  if (existing) {
    if (existing.quantity >= product.stock) {
      Swal.fire({
        icon: 'warning',
        title: 'Stok Terbatas',
        text: `Maksimal kuantitas adalah ${product.stock} pcs.`,
        confirmButtonColor: '#00AA13'
      });
      return;
    }
    existing.quantity++;
  } else {
    cart.value.push({
      id: product.id,
      name: product.name,
      price: finalPrice,
      max_stock: product.stock,
      quantity: 1,
      image_url: product.image_url
    });
  }

  Swal.fire({
    icon: 'success',
    title: 'Masuk Keranjang',
    text: `${product.name} berhasil ditambahkan!`,
    toast: true,
    position: 'top-end',
    timer: 1500,
    showConfirmButton: false
  });
};

const addDetailToCart = (andCheckout = false) => {
  if (!selectedProduct.value) return;
  const qty = parseInt(detailQuantity.value) || 1;
  const prod = selectedProduct.value;
  const finalPrice = calculateFinalPrice(prod);
  
  const existing = cart.value.find(item => item.id === prod.id);
  const currentQty = existing ? existing.quantity : 0;
  
  if (currentQty + qty > prod.stock) {
    Swal.fire({
      icon: 'warning',
      title: 'Stok Terbatas',
      text: `Maksimal pembelian adalah ${prod.stock} pcs (di keranjang sudah ada ${currentQty} pcs).`,
      confirmButtonColor: '#00AA13'
    });
    return;
  }

  if (existing) {
    existing.quantity += qty;
  } else {
    cart.value.push({
      id: prod.id,
      name: prod.name,
      price: finalPrice,
      max_stock: prod.stock,
      quantity: qty,
      image_url: prod.image_url
    });
  }

  closeProductModal();

  if (andCheckout) {
    isCartOpen.value = true;
  } else {
    Swal.fire({
      icon: 'success',
      title: 'Masuk Keranjang',
      text: `${qty}x ${prod.name} berhasil ditambahkan!`,
      toast: true,
      position: 'top-end',
      timer: 1800,
      showConfirmButton: false
    });
  }
};

const increaseQty = (idx) => {
  if (cart.value[idx].quantity < cart.value[idx].max_stock) {
    cart.value[idx].quantity++;
  } else {
    Swal.fire({
      icon: 'warning',
      title: 'Stok Terbatas',
      text: `Maksimal kuantitas adalah ${cart.value[idx].max_stock} pcs.`,
      confirmButtonColor: '#00AA13'
    });
  }
};

const decreaseQty = (idx) => {
  if (cart.value[idx].quantity > 1) {
    cart.value[idx].quantity--;
  } else {
    removeItem(idx);
  }
};

const removeItem = (idx) => {
  cart.value.splice(idx, 1);
};

const submitCheckout = () => {
  if (cart.value.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Keranjang Kosong',
      text: 'Pilih minimal satu barang untuk checkout.',
      confirmButtonColor: '#00AA13'
    });
    return;
  }

  if (!checkoutForm.value.customer_name || !checkoutForm.value.customer_phone || !checkoutForm.value.customer_address) {
    Swal.fire({
      icon: 'warning',
      title: 'Data Belum Lengkap',
      text: 'Mohon isi nama lengkap, nomor WhatsApp, dan alamat pengiriman Anda.',
      confirmButtonColor: '#00AA13'
    });
    return;
  }

  isSubmitting.value = true;

  // Ambil token Turnstile jika aktif
  const turnstileInput = document.querySelector('[name="cf-turnstile-response"]');
  const turnstileToken = turnstileInput ? turnstileInput.value : null;

  const payload = {
    ...checkoutForm.value,
    items: cart.value.map(item => ({ id: item.id, quantity: item.quantity })),
    items_json: JSON.stringify(cart.value),
    'cf-turnstile-response': turnstileToken,
  };

  router.post('/order/checkout', payload, {
    onError: (errors) => {
      isSubmitting.value = false;
      const firstErr = Object.values(errors)[0] || 'Gagal memproses pesanan.';
      Swal.fire({
        icon: 'error',
        title: 'Gagal Checkout',
        text: firstErr,
        confirmButtonColor: '#00AA13'
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};
</script>
