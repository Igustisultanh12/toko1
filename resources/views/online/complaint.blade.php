@extends('online.layout')

@section('title', 'Pusat Komplain & Layanan Pelanggan - ' . $order->order_number)

@section('content')
<div x-data="complaintFormHandler()" class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    {{-- KARTU UTAMA FORM KOMPLAIN --}}
    <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-gray-100 shadow-xl space-y-6">
        
        {{-- HEADER FORM --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-5">
            <div>
                <span class="px-3.5 py-1 bg-emerald-50 text-[#00880F] rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200/60 inline-block mb-1.5 shadow-sm">
                    🛡️ Layanan Purna Jual & Garansi Toko
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 uppercase tracking-tight">Form Pengaduan & Komplain</h2>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Sampaikan kendala pesanan Anda dengan melampirkan bukti foto dan video unboxing.</p>
            </div>
            <div class="text-left sm:text-right bg-gray-50 sm:bg-transparent p-3 sm:p-0 rounded-2xl border sm:border-0 border-gray-100 w-full sm:w-auto">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">No. Pesanan:</span>
                <span class="font-mono font-black text-indigo-700 text-sm">{{ $order->order_number }}</span>
            </div>
        </div>

        {{-- RINGKASAN DATA PESANAN --}}
        <div class="p-5 bg-gray-50 rounded-3xl border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-gray-400 font-black uppercase text-[10px] tracking-wider block">Penerima Paket:</span>
                <span class="font-black text-gray-900 text-sm uppercase block mt-0.5">{{ $order->customer_name }}</span>
                <span class="text-gray-500 font-bold font-mono">{{ $order->customer_phone }}</span>
                <p class="text-gray-400 text-[10px] mt-1 line-clamp-2 leading-tight">{{ $order->customer_address }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-black uppercase text-[10px] tracking-wider block">Daftar Barang Belanjaan:</span>
                <ul class="text-[11px] font-bold text-gray-800 space-y-1 mt-1">
                    @foreach($order->items as $item)
                        <li class="flex justify-between items-center">
                            <span>• {{ $item->product_name }}</span>
                            <span class="text-gray-400 font-black">{{ $item->quantity }}x</span>
                        </li>
                    @endforeach
                </ul>
                <div class="pt-2 mt-2 border-t border-gray-200/60 flex justify-between items-center font-black text-xs text-gray-900">
                    <span>Total Bayar:</span>
                    <span class="text-[#00880F]">{{ $order->formatted_total }} (Lunas)</span>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- JIKA SUDAH ADA KOMPLAIN YANG DIAJUKAN SEBELUMNYA --}}
        {{-- ======================================================== --}}
        @if($latestComplaint)
            <div class="bg-gradient-to-r from-emerald-50/80 to-teal-50/80 rounded-3xl p-6 border-2 border-emerald-500/30 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <span class="text-xl">📋</span>
                        <h4 class="font-black text-gray-900 uppercase text-xs tracking-wider">Status Komplain Terkini:</h4>
                    </div>
                    <div>
                        {!! $latestComplaint->status_badge !!}
                    </div>
                </div>

                <div class="text-xs space-y-2 text-gray-700 bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm">
                    <p><b>Alasan Komplain:</b> {{ $latestComplaint->reason }}</p>
                    <p><b>Solusi yang Diharapkan:</b> {{ $latestComplaint->expected_solution }}</p>
                    <p><b>Keterangan Masalah:</b> <i class="text-gray-600">"{{ $latestComplaint->description }}"</i></p>
                    <p class="text-[10px] text-gray-400 font-medium">Diajukan pada: {{ $latestComplaint->created_at->format('d/m/Y H:i') }} WIB</p>
                    
                    @if($latestComplaint->admin_notes)
                        <div class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 text-xs">
                            <span class="font-black uppercase text-[10px] tracking-wider block text-[#00661A]">💬 Tanggapan Kasir / Admin Toko:</span>
                            <p class="font-medium mt-1">{{ $latestComplaint->admin_notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- GALERI BUKTI FOTO & VIDEO YANG TELAH DIUNGGAH --}}
                <div class="space-y-2">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Bukti yang Telah Dilampirkan:</span>
                    
                    @if(is_array($latestComplaint->photos) && count($latestComplaint->photos) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                            @foreach($latestComplaint->photos as $photo)
                                <a href="{{ route('media.file', ['path' => $photo]) }}" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border-2 border-emerald-200 shadow-sm hover:opacity-90 transition">
                                    <img src="{{ route('media.file', ['path' => $photo]) }}" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($latestComplaint->video)
                        <div class="pt-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block mb-1">🎥 Video Bukti Unboxing:</span>
                            <video controls class="w-full max-h-64 rounded-2xl bg-black shadow-md">
                                <source src="{{ route('media.file', ['path' => $latestComplaint->video]) }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutar video.
                            </video>
                        </div>
                    @endif
                </div>

                <div class="pt-2 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs">
                    <span class="text-gray-500">Ada pertanyaan seputar pengaduan ini?</span>
                    @if(!empty($shop['shop_phone']))
                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $shop['shop_phone']) }}&text={{ urlencode('Halo Admin ' . ($shop['shop_name'] ?? 'Toko') . ', saya ingin konfirmasi terkait komplain pesanan ' . $order->order_number) }}" 
                           target="_blank" class="px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl font-black uppercase text-xs tracking-wider transition shadow-md shadow-emerald-500/20 flex items-center space-x-1.5">
                            <span>💬 Chat WhatsApp Toko</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- FORM PENGAJUAN KOMPLAIN BARU --}}
        {{-- ======================================================== --}}
        <form action="{{ route('order.complaint.store', $order->order_number) }}" method="POST" enctype="multipart/form-data" 
              @submit="handleSubmit($event)" class="space-y-5 pt-2">
            @csrf

            <div class="flex items-center space-x-2">
                <span class="text-base">📝</span>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">
                    {{ $latestComplaint ? 'Ajukan Pengaduan Tambahan' : 'Isi Rincian Pengaduan Barang' }}
                </h3>
            </div>

            {{-- 1. PILIH ALASAN KOMPLAIN --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wide mb-1.5 ml-1">
                    Alasan Komplain <span class="text-rose-500">*</span>
                </label>
                <select name="reason" required 
                        class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-bold text-xs text-gray-900 outline-none focus:border-[#00AA13] focus:bg-white transition">
                    <option value="" disabled selected>-- Pilih Alasan Kendala --</option>
                    <option value="Barang Rusak / Cacat Fisik">Barang Rusak / Cacat Fisik</option>
                    <option value="Barang Kurang / Tidak Lengkap">Barang Kurang / Kuantitas Tidak Lengkap</option>
                    <option value="Barang Tidak Sesuai Pesanan">Barang Tidak Sesuai Pesanan / Salah Varian</option>
                    <option value="Salah Kirim Produk">Salah Kirim Produk Berbeda</option>
                    <option value="Kemasan Rusak / Bocor Saat Pengiriman">Kemasan Rusak / Bocor Saat Pengiriman</option>
                    <option value="Lainnya">Kendala Lainnya</option>
                </select>
            </div>

            {{-- 2. PILIH SOLUSI YANG DIHARAPKAN --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wide mb-1.5 ml-1">
                    Solusi yang Anda Harapkan <span class="text-rose-500">*</span>
                </label>
                <select name="expected_solution" required 
                        class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-bold text-xs text-gray-900 outline-none focus:border-[#00AA13] focus:bg-white transition">
                    <option value="" disabled selected>-- Pilih Solusi yang Diharapkan --</option>
                    <option value="Kirim Barang Pengganti (Retur Baru)">Kirim Barang Pengganti (Retur Barang Baru)</option>
                    <option value="Pengembalian Dana (Refund Total/Sebagian)">Pengembalian Dana (Refund Total / Sebagian)</option>
                    <option value="Kirim Kekurangan Barang">Kirim Kekurangan Barang yang Belum Terkirim</option>
                    <option value="Diskusi Solusi Terbaik via WhatsApp">Diskusi Solusi Terbaik via WhatsApp dengan Toko</option>
                </select>
            </div>

            {{-- 3. KETERANGAN MASALAH --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wide mb-1.5 ml-1">
                    Penjelasan Detail Kendala <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="4" required placeholder="Jelaskan secara detail bagian barang yang rusak, tidak sesuai, atau kurang..."
                          class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-medium text-xs text-gray-900 outline-none focus:border-[#00AA13] focus:bg-white transition"></textarea>
            </div>

            {{-- 4. UPLOAD FOTO BUKTI (BISA BANYAK GAMBAR, MAX TOTAL 25MB) --}}
            <div class="space-y-2 p-5 sm:p-6 bg-emerald-50/40 rounded-3xl border-2 border-dashed border-emerald-300/80">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wide">
                        📸 Upload Foto Bukti <span class="text-[#00880F] font-bold">* (Bisa Banyak Gambar, Max 25MB)</span>
                    </label>
                    <span class="text-[10px] font-bold text-gray-500 font-mono" x-text="photoSizeText"></span>
                </div>
                <p class="text-[11px] text-gray-500">Unggah foto jelas bagian barang yang rusak, label kemasan paket, atau nota barang.</p>
                
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" required
                       @change="handlePhotoSelect($event)" id="photoInput" class="hidden">
                
                <label for="photoInput" 
                       class="w-full py-4 bg-white hover:bg-emerald-50 border-2 border-[#00AA13] text-[#00880F] rounded-2xl font-black text-xs uppercase tracking-wider transition cursor-pointer flex items-center justify-center space-x-2 shadow-sm">
                    <svg class="w-5 h-5 text-[#00AA13]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>+ Pilih Foto Bukti (Bisa Sekaligus Banyak)</span>
                </label>

                {{-- PREVIEW FOTO-FOTO SEBELUM UPLOAD --}}
                <template x-if="photoPreviews.length > 0">
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5 pt-2">
                        <template x-for="(preview, idx) in photoPreviews" :key="idx">
                            <div class="relative aspect-square rounded-2xl overflow-hidden border-2 border-emerald-300 shadow-sm group">
                                <img :src="preview.url" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[8px] font-mono px-1 rounded" x-text="preview.sizeText"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- 5. UPLOAD VIDEO BUKTI (MAX 15MB) --}}
            <div class="space-y-2 p-5 sm:p-6 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wide">
                        🎥 Upload Video Bukti Unboxing <span class="text-gray-500 font-bold">(Opsional, Max 15MB)</span>
                    </label>
                    <span class="text-[10px] font-bold text-gray-500 font-mono" x-text="videoSizeText"></span>
                </div>
                <p class="text-[11px] text-gray-500">Video saat membuka paket (*unboxing*) akan mempercepat proses verifikasi oleh toko.</p>
                
                <input type="file" name="video" accept="video/mp4,video/mov,video/avi,video/webm,video/mkv,video/3gp"
                       @change="handleVideoSelect($event)" id="videoInput" class="hidden">
                
                <label for="videoInput" 
                       class="w-full py-4 bg-white hover:bg-gray-100 border-2 border-gray-300 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-wider transition cursor-pointer flex items-center justify-center space-x-2 shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-text="videoFileName ? 'Ganti Video: ' + videoFileName : '+ Pilih Video Unboxing (Max 15MB)'"></span>
                </label>

                {{-- PREVIEW VIDEO SEBELUM UPLOAD --}}
                <template x-if="videoPreviewUrl">
                    <div class="pt-2">
                        <video :src="videoPreviewUrl" controls class="w-full max-h-56 rounded-2xl bg-black shadow-md"></video>
                    </div>
                </template>
            </div>

            {{-- TOMBOL SUBMIT PENGADUAN --}}
            <div class="pt-3">
                <button type="submit" :disabled="isUploading"
                        class="w-full py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/25 transition flex items-center justify-center space-x-2 disabled:opacity-50">
                    <template x-if="!isUploading">
                        <span class="flex items-center space-x-2">
                            <span>🚀 Kirim Pengaduan Komplain Sekarang</span>
                        </span>
                    </template>
                    <template x-if="isUploading">
                        <span class="flex items-center space-x-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengunggah Berkas Bukti... Mohon Tunggu</span>
                        </span>
                    </template>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function complaintFormHandler() {
    return {
        photoPreviews: [],
        photoSizeText: '',
        totalPhotoBytes: 0,
        videoPreviewUrl: null,
        videoFileName: '',
        videoSizeText: '',
        isUploading: false,

        handlePhotoSelect(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            this.photoPreviews = [];
            let totalBytes = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                totalBytes += file.size;

                const url = URL.createObjectURL(file);
                const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
                this.photoPreviews.push({
                    url: url,
                    sizeText: `${sizeMB} MB`
                });
            }

            this.totalPhotoBytes = totalBytes;
            const totalMB = (totalBytes / (1024 * 1024)).toFixed(1);
            this.photoSizeText = `${this.photoPreviews.length} Foto (${totalMB} MB / Maks 25 MB)`;

            // Validasi client-side: Max 25MB
            if (totalBytes > 25 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Foto Terlalu Besar',
                    text: `Total ukuran foto yang Anda pilih (${totalMB} MB) melebihi batas maksimal 25 MB. Silakan kurangi jumlah foto atau kecilkan resolusinya.`,
                    confirmButtonColor: '#00AA13'
                });
                event.target.value = '';
                this.photoPreviews = [];
                this.photoSizeText = '';
            }
        },

        handleVideoSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
            this.videoFileName = file.name;
            this.videoSizeText = `${sizeMB} MB / Maks 15 MB`;

            // Validasi client-side: Max 15MB
            if (file.size > 15 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Video Terlalu Besar',
                    text: `Ukuran video yang Anda pilih (${sizeMB} MB) melebihi batas maksimal 15 MB. Silakan potong durasi video atau kecilkan kompresinya.`,
                    confirmButtonColor: '#00AA13'
                });
                event.target.value = '';
                this.videoPreviewUrl = null;
                this.videoFileName = '';
                this.videoSizeText = '';
                return;
            }

            this.videoPreviewUrl = URL.createObjectURL(file);
        },

        handleSubmit(event) {
            if (this.totalPhotoBytes > 25 * 1024 * 1024) {
                event.preventDefault();
                Swal.fire('Error', 'Total ukuran foto melebihi 25 MB.', 'error');
                return;
            }
            this.isUploading = true;
        }
    }
}
</script>
@endsection
