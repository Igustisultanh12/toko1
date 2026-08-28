@extends('layouts.admin')

@section('title', 'Pusat Pengaturan Sistem')
@section('header_title', 'Pengaturan Toko & Konfigurasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10">
    
    {{-- NOTIFIKASI SUKSES --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-[#00880F] font-bold rounded-2xl shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-[#00AA13] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span class="text-xs">{{ session('success') }}</span>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 font-bold rounded-2xl shadow-sm">
            <p class="uppercase text-xs font-black mb-1.5">Terdeteksi Kesalahan Input:</p>
            <ul class="list-disc ml-5 text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- SEKSI 1: BRANDING & IDENTITAS APLIKASI --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 space-y-6">
                <div class="flex items-center space-x-4 border-b border-gray-100 pb-4">
                    <div class="bg-[#00AA13] p-3.5 rounded-2xl text-white shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Branding & Nama Sistem</h3>
                        <p class="text-xs text-gray-400 font-medium">Ubah nama aplikasi POS dan favicon browser tab.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Aplikasi / Sistem</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'SIKANDA' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-black text-gray-800 text-sm" placeholder="SIKANDA">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Nama yang tampil di pojok kiri atas sidebar, tab browser, dan halaman login.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tagline / Subtitle Aplikasi</label>
                        <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] ?? 'Sultan Web Engine' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-bold text-gray-700 text-xs" placeholder="Sultan Web Engine">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Logo Favicon (Tab Browser)</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center bg-gray-50 hover:border-emerald-400 transition-all">
                            @if(!empty($settings['app_favicon']))
                                <div class="inline-block mb-3 p-3 bg-white rounded-2xl shadow border border-gray-100">
                                    <img src="{{ route('media.file', ['path' => $settings['app_favicon']]) }}" class="h-12 w-12 object-contain mx-auto">
                                    <span class="text-[9px] font-bold text-gray-400 block mt-1">Favicon Saat Ini</span>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="app_favicon" accept=".ico,.png,.jpg,.jpeg,.svg" class="text-xs font-bold text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-[#00AA13] file:text-white file:font-black file:text-[10px] file:uppercase">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 font-medium">Format: .ico, .png, .svg, .jpg (Maksimal 1MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEKSI 2: IDENTITAS TOKO & LOGO --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 space-y-6">
                <div class="flex items-center space-x-4 border-b border-gray-100 pb-4">
                    <div class="bg-[#00AA13] p-3.5 rounded-2xl text-white shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Identitas Toko</h3>
                        <p class="text-xs text-gray-400 font-medium">Informasi toko yang tercetak pada nota & faktur PDF.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Toko / Usaha <span class="text-rose-500">*</span></label>
                        <input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? '' }}" required
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-black text-gray-800 text-sm" placeholder="Contoh: BAGIAN LOGISTIK / TOKO KITA">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nomor WhatsApp / Telepon Toko</label>
                        <input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-bold text-gray-700 text-xs" placeholder="08123456789">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Alamat Lengkap Toko</label>
                        <textarea name="shop_address" rows="2" 
                                  class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-bold text-gray-700 text-xs">{{ $settings['shop_address'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Logo Toko (Header / Struk)</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center bg-gray-50 hover:border-emerald-400 transition-all">
                            @if(!empty($settings['shop_logo']))
                                <div class="inline-block mb-3 p-2 bg-white rounded-2xl shadow border border-gray-100">
                                    <img src="{{ route('media.file', ['path' => $settings['shop_logo']]) }}" class="h-16 mx-auto object-contain rounded-xl">
                                    <span class="text-[9px] font-bold text-gray-400 block mt-1">Logo Toko Saat Ini</span>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="shop_logo" accept="image/*" class="text-xs font-bold text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-[#00AA13] file:text-white file:font-black file:text-[10px] file:uppercase">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 font-medium">Format: PNG, JPG, JPEG, SVG (Maksimal 2MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEKSI 3: GATEWAY QRIS DOKU --}}
            <div class="bg-gradient-to-br from-[#002B0A] to-[#004D13] rounded-[2.5rem] shadow-xl p-8 sm:p-10 space-y-6 text-white">
                <div class="flex items-center space-x-4 border-b border-emerald-800/80 pb-4">
                    <div class="bg-[#00AED6] p-3.5 rounded-2xl text-white shadow-lg shadow-cyan-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black uppercase tracking-tight">Gateway QRIS (DOKU API)</h3>
                        <p class="text-xs text-emerald-200 font-medium">Kredensial API Merchant DOKU.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1.5 ml-1">DOKU Client ID (Mall ID / Client ID)</label>
                        <input type="text" name="doku_client_id" value="{{ $settings['doku_client_id'] ?? '' }}" 
                               class="w-full p-4 bg-emerald-950/60 border-2 border-emerald-800 rounded-2xl outline-none focus:border-[#00AED6] transition-all font-mono text-xs text-emerald-100" placeholder="Contoh: MCH-123456789">
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-1.5 ml-1">
                            <label class="block text-[10px] font-black text-emerald-200 uppercase tracking-widest">DOKU Secret Key (API Key)</label>
                            <button type="button" onclick="toggleSecretKeyVisibility()" class="text-[10px] text-cyan-300 hover:text-white font-bold underline">
                                <span id="toggleText">👁️ Tampilkan Key</span>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="password" id="dokuSecretKeyInput" name="doku_secret_key" value="{{ $settings['doku_secret_key'] ?? '' }}" 
                                   class="w-full p-4 bg-emerald-950/60 border-2 border-emerald-800 rounded-2xl outline-none focus:border-[#00AED6] transition-all font-mono text-xs text-emerald-100 pr-12" placeholder="Contoh: SK-XXXXXXXXXXXXXXXX">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1.5 ml-1">Environment Server</label>
                        <select name="doku_base_url" class="w-full p-4 bg-emerald-950/60 border-2 border-emerald-800 rounded-2xl outline-none focus:border-[#00AED6] transition-all font-bold text-xs text-emerald-100 appearance-none">
                            <option value="https://api-sandbox.doku.com" {{ ($settings['doku_base_url'] ?? '') == 'https://api-sandbox.doku.com' ? 'selected' : '' }}>🟡 SANDBOX (Uji Coba Testing)</option>
                            <option value="https://api.doku.com" {{ ($settings['doku_base_url'] ?? '') == 'https://api.doku.com' ? 'selected' : '' }}>🟢 PRODUCTION (Live / Transaksi Asli)</option>
                        </select>
                    </div>

                    {{-- KOTAK LINK WEBHOOK --}}
                    <div class="p-4 bg-emerald-950/80 border border-emerald-700/60 rounded-2xl space-y-1.5">
                        <label class="block text-[10px] font-black text-emerald-300 uppercase tracking-wider">URL Webhook DOKU (Notification URL):</label>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ url('/doku/notification') }}" id="webhookUrlInput"
                                   class="w-full bg-black/40 border border-emerald-800 text-emerald-200 text-[11px] font-mono px-3 py-2 rounded-xl outline-none">
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('webhookUrlInput').value); Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'URL Webhook berhasil disalin ke clipboard.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });"
                                    class="px-3 py-2 bg-[#00AA13] hover:bg-[#00880F] text-white text-[10px] font-black rounded-xl uppercase shrink-0">
                                Salin
                            </button>
                        </div>
                        <p class="text-[9px] text-emerald-300">Tempel link di atas pada menu <i>Notification URL</i> di Dashboard DOKU Merchant Anda.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1.5 ml-1">Catatan Kaki Struk (Receipt Footer)</label>
                        <input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? 'Terima Kasih Telah Berbelanja!' }}" 
                               class="w-full p-4 bg-emerald-950/60 border-2 border-emerald-800 rounded-2xl outline-none focus:border-[#00AED6] transition-all font-bold text-xs text-emerald-100" placeholder="Terima Kasih!">
                    </div>
                </div>
            </div>

            {{-- SEKSI 4: FITUR NOTIFIKASI SUARA --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-[#FFB800] p-3.5 rounded-2xl text-white shadow-lg shadow-amber-500/25">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" stroke-width="2.5"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Notifikasi Suara Kasir</h3>
                            <p class="text-xs text-gray-400 font-medium">Pengumuman audio saat pembayaran sukses.</p>
                        </div>
                    </div>
                    
                    {{-- TOGGLE ON/OFF --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_voice_enabled" value="1" class="sr-only peer" {{ ($settings['is_voice_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                        <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00AA13] shadow-inner"></div>
                    </label>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">File Audio Notifikasi (MP3 / WAV / OGG)</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center bg-gray-50 hover:border-emerald-400 transition-all space-y-3">
                            @if(!empty($settings['payment_success_sound']))
                                <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 max-w-sm mx-auto">
                                    <p class="text-[10px] font-bold text-gray-500 mb-1">Audio Notifikasi Aktif:</p>
                                    <audio controls class="w-full h-8">
                                        <source src="{{ route('media.file', ['path' => $settings['payment_success_sound']]) }}">
                                    </audio>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="payment_success_sound" accept="audio/*" class="text-xs font-bold text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-[#00AA13] file:text-white file:font-black file:text-[10px] file:uppercase">
                            </div>
                            <p class="text-[10px] text-gray-400 font-medium">Upload sound chime/bel toko. Sistem juga akan otomatis menyebutkan nominal pembayaran.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEKSI 5: TANDA TANGAN ELEKTRONIK (TTE) & PENANGGUNG JAWAB --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 space-y-6 lg:col-span-2">
                <div class="flex items-center space-x-4 border-b border-gray-100 pb-4">
                    <div class="bg-[#00AA13] p-3.5 rounded-2xl text-white shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2.5"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Tanda Tangan Elektronik (TTE) & Penanggung Jawab</h3>
                        <p class="text-xs text-gray-400 font-medium">Pengaturan penandatangan digital pada faktur dan verifikasi QR Code resmi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Jabatan / Sebutan Petugas (TTE)</label>
                        <input type="text" name="cashier_officer_title" value="{{ $settings['cashier_officer_title'] ?? 'Petugas Kasir' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-bold text-gray-800 text-xs" placeholder="Contoh: Petugas Kasir / Kepala Toko">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Jabatan yang tercantum di atas QR TTE pada dokumen Faktur PDF.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama Penanggung Jawab Default (Opsional)</label>
                        <input type="text" name="cashier_officer_name" value="{{ $settings['cashier_officer_name'] ?? '' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-bold text-gray-800 text-xs" placeholder="Kosongkan untuk otomatis nama kasir login">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Jika dikosongkan, sistem akan otomatis menggunakan nama akun kasir yang melakukan transaksi.</p>
                    </div>
                </div>

                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200/60 flex items-center space-x-3 text-xs text-[#00880F] font-bold">
                    <span class="text-lg">🛡️</span>
                    <span>Setiap faktur PDF akan dilengkapi <b>QR Code Tanda Tangan Elektronik (TTE)</b>. Ketika di-scan oleh pembeli, sistem akan membuka halaman verifikasi keabsahan dokumen dan identitas penanggung jawab transaksi.</span>
                </div>
            </div>

            {{-- SEKSI 6: CLOUDFLARE TURNSTILE CAPTCHA (KEAMANAN ANTI-BOT) --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 space-y-6 lg:col-span-2">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-tr from-amber-500 to-orange-500 p-3.5 rounded-2xl text-white shadow-lg shadow-orange-500/25">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2.5"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Cloudflare Turnstile CAPTCHA</h3>
                            <p class="text-xs text-gray-400 font-medium">Proteksi anti-bot cerdas pada Form Checkout Order, Portal Lacak Pesanan, dan Staff Login.</p>
                        </div>
                    </div>
                    
                    {{-- TOGGLE ON/OFF --}}
                    <div class="flex items-center space-x-3 bg-gray-50 px-4 py-2 rounded-2xl border border-gray-100">
                        <span class="text-xs font-black uppercase text-gray-700">Status Turnstile:</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="turnstile_enabled" value="1" class="sr-only peer" {{ ($settings['turnstile_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00AA13] shadow-inner"></div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Turnstile Site Key (Publik)</label>
                        <input type="text" name="turnstile_site_key" value="{{ $settings['turnstile_site_key'] ?? '' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-mono font-bold text-gray-800 text-xs" placeholder="0x4AAAAAAAXxxxxxxx">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Didapatkan dari dashboard Cloudflare ➔ Turnstile ➔ Site Key.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Turnstile Secret Key (Rahasia)</label>
                        <input type="password" name="turnstile_secret_key" value="{{ $settings['turnstile_secret_key'] ?? '' }}" 
                               class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl outline-none focus:border-[#00AA13] focus:bg-white transition-all font-mono font-bold text-gray-800 text-xs" placeholder="0x4AAAAAAAXxxxxxxx_SecretKey">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Kunci rahasia untuk verifikasi backend token dari Cloudflare.</p>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 flex items-center space-x-3 text-xs text-amber-900 font-medium">
                    <span class="text-xl">🛡️</span>
                    <span>Ketika diaktifkan, widget <b>Cloudflare Turnstile</b> akan otomatis muncul dan memvalidasi setiap pengunjung saat <b>Checkout Pesanan</b>, <b>Lacak Resi Pesanan</b>, dan <b>Login Staff</b> untuk memblokir bot spam secara instan tanpa mengganggu kenyamanan pembeli.</span>
                </div>
            </div>

        </div>

        {{-- TOMBOL SIMPAN SEMUA PENGATURAN --}}
        <div class="flex items-center justify-end pt-6 border-t border-gray-100">
            <button type="submit" class="px-10 py-4 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/25 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                Simpan Semua Pengaturan
            </button>
        </div>

    </form>
</div>

<script>
function toggleSecretKeyVisibility() {
    const input = document.getElementById('dokuSecretKeyInput');
    const toggleText = document.getElementById('toggleText');
    if (input.type === 'password') {
        input.type = 'text';
        toggleText.innerText = '🙈 Sembunyikan Key';
    } else {
        input.type = 'password';
        toggleText.innerText = '👁️ Tampilkan Key';
    }
}
</script>
@endsection
