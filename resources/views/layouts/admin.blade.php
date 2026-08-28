<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ $shop['app_name'] ?? 'SIKANDA' }} POS</title>
    @if(!empty($shop['app_favicon']))
        <link rel="icon" href="{{ route('media.file', ['path' => $shop['app_favicon']]) }}">
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F6F8F9; }
        .bg-gojek { background-color: #00AA13; }
        .text-gojek { color: #00AA13; }
        .border-gojek { border-color: #00AA13; }
        .sidebar-gojek { 
            background: linear-gradient(180deg, #00360D 0%, #004D13 50%, #00661A 100%); 
        }
        [x-cloak] { display: none !important; }
        
        /* Custom smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Custom SweetAlert2 Style */
        .swal2-popup {
            border-radius: 2rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 1.8rem !important;
        }
        .swal2-title {
            font-weight: 900 !important;
            font-size: 1.15rem !important;
            color: #1A202C !important;
            text-transform: uppercase !important;
            letter-spacing: -0.02em !important;
        }
        .swal2-html-container {
            font-size: 0.82rem !important;
            color: #4A5568 !important;
            font-weight: 500 !important;
        }
        .swal2-confirm {
            border-radius: 1rem !important;
            font-weight: 800 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 0.75rem 1.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
        .swal2-cancel {
            border-radius: 1rem !important;
            font-weight: 800 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 0.75rem 1.5rem !important;
        }
    </style>
</head>
<body class="flex min-h-screen antialiased text-gray-800 bg-[#F6F8F9]">
    <aside class="w-72 sidebar-gojek text-white hidden lg:flex flex-col shadow-2xl shrink-0 sticky top-0 h-screen z-30">
        @include('partials.sidebar')
    </aside>

    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        {{-- TOP HEADER --}}
        <header class="bg-white/95 backdrop-blur-md px-6 sm:px-8 py-4 border-b border-gray-100/80 flex justify-between items-center sticky top-0 z-20 shrink-0 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-7 bg-emerald-500 rounded-full"></div>
                <div>
                    <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">@yield('header_title', 'Dashboard')</h2>
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">{{ $shop['shop_name'] ?? '' }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                {{-- BADGE TOKO BUKA --}}
                <div class="hidden sm:flex items-center space-x-2 bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full border border-emerald-200/60 text-xs font-black">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="uppercase tracking-wider text-[10px]">Toko Aktif</span>
                </div>

                {{-- SHORTCUT BUKA KASIR --}}
                <a href="{{ route('cashier.pos.index') }}" 
                   class="flex items-center space-x-2 px-5 py-2.5 bg-[#00AA13] hover:bg-[#00880F] active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2.5"/></svg>
                    <span>Kasir POS</span>
                </a>

                {{-- USER CHIP --}}
                <div class="flex items-center space-x-2.5 pl-2 border-l border-gray-200">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-[#00880F] flex items-center justify-center font-black text-sm border border-emerald-200 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <span class="block text-xs font-black text-gray-800 leading-tight">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ strtoupper(Auth::user()->role ?? 'Admin') }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT CONTAINER --}}
        <main class="flex-1 p-6 sm:p-8 md:p-10">
            @yield('content')
        </main>

        {{-- FOOTER COPYRIGHT --}}
        <footer class="mt-auto py-6 border-t border-gray-200/60 text-center bg-white">
            <p class="text-[11px] font-bold text-gray-400">
                &copy; {{ date('Y') }} <span class="text-gray-700 font-black">I Gusti Sultan</span>. All rights reserved.
            </p>
        </footer>
    </div>

    {{-- GLOBAL SWEETALERT2 & AUDIO NOTIFICATION SCRIPTS --}}
    <script>
        // WEB AUDIO SYNTHESIZER SOUND ENGINE (100% RELIABLE & ZERO-DEPENDENCY)
        window.playNotificationSound = function(type = 'chime') {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();

                if (type === 'order_new' || type === 'chime') {
                    // Double Ding-Dong Bell (880Hz -> 1320Hz)
                    const now = ctx.currentTime;
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, now);
                    gain1.gain.setValueAtTime(0.3, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.4);

                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1320, now + 0.18);
                    gain2.gain.setValueAtTime(0.35, now + 0.18);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.18);
                    osc2.stop(now + 0.8);

                } else if (type === 'payment_success' || type === 'success') {
                    // Success Arpeggio (C5 -> E5 -> G5 -> C6)
                    const notes = [523.25, 659.25, 783.99, 1046.50];
                    notes.forEach((freq, idx) => {
                        const startTime = ctx.currentTime + (idx * 0.1);
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, startTime);
                        gain.gain.setValueAtTime(0.25, startTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(startTime);
                        osc.stop(startTime + 0.35);
                    });
                } else if (type === 'status_update') {
                    // Status Notification Tone
                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(659.25, now);
                    osc.frequency.exponentialRampToValueAtTime(987.77, now + 0.25);
                    gain.gain.setValueAtTime(0.25, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.5);
                }
            } catch(e) {
                console.log('Audio playback error:', e);
            }
        };

        function confirmDelete(event, message = 'Yakin ingin menghapus data ini?', confirmText = 'Ya, Hapus!') {
            if (event) {
                event.preventDefault();
            }
            const form = event ? (event.target.closest('form') || event.target) : null;
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EE2737',
                cancelButtonColor: '#6B7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.playNotificationSound('success');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! addslashes(session('success')) !!}",
                    confirmButtonColor: '#00AA13',
                    timer: 3500,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: "{!! addslashes(session('error')) !!}",
                    confirmButtonColor: '#EE2737'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Periksa Masukan Anda',
                    html: '<div style="text-align:left; font-size:12px; margin-top:8px;">' +
                          '@foreach($errors->all() as $error)<p style="margin:2px 0;">• {{ addslashes($error) }}</p>@endforeach' +
                          '</div>',
                    confirmButtonColor: '#EE2737'
                });
            @endif

            // REALTIME POLLING NOTIFIKASI PESANAN ONLINE BARU (SETIAP 4 DETIK)
            let lastNotifiedOrderCount = 0;

            async function checkNewOnlineOrders() {
                try {
                    const res = await fetch("{{ route('orders.realtime-check') }}");
                    if (res.ok) {
                        const data = await res.json();
                        const badge = document.getElementById('sidebarOrderBadge');
                        
                        if (data.count > 0) {
                            if (badge) {
                                badge.innerText = data.count;
                                badge.classList.remove('hidden');
                            }

                            // Jika ada pesanan baru yang belum pernah diberitahukan
                            if (data.count > lastNotifiedOrderCount) {
                                lastNotifiedOrderCount = data.count;
                                window.playNotificationSound('order_new');

                                if (data.latest_order) {
                                    Swal.fire({
                                        title: '🛒 PESANAN ONLINE MASUK!',
                                        html: `<div style="text-align:left; font-size:12px; line-height:1.6; margin-top:6px;">
                                                    <p><b>No Pesanan:</b> <code style="color:#00AA13; font-weight:bold;">${data.latest_order.order_number}</code></p>
                                                    <p><b>Nama Pembeli:</b> ${data.latest_order.customer_name}</p>
                                                    <p><b>Total:</b> <b style="color:#00AA13;">${data.latest_order.formatted_total}</b> (QRIS LUNAS)</p>
                                                    <p><b>Kurir:</b> ${data.latest_order.courier}</p>
                                                    <p style="color:#718096; font-size:11px;">Status: Menunggu Konfirmasi Toko</p>
                                               </div>`,
                                        icon: 'info',
                                        showCancelButton: true,
                                        confirmButtonColor: '#00AA13',
                                        cancelButtonColor: '#6B7280',
                                        confirmButtonText: 'Buka & Konfirmasi',
                                        cancelButtonText: 'Nanti'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "{{ route('admin.orders.index') }}?status=unconfirmed";
                                        }
                                    });
                                }
                            }
                        } else {
                            if (badge) {
                                badge.classList.add('hidden');
                            }
                            lastNotifiedOrderCount = 0;
                        }
                    }
                } catch (err) {
                    console.log('Error checking online orders:', err);
                }
            }

            // Jalankan pertama kali dan polling berkala
            checkNewOnlineOrders();
            setInterval(checkNewOnlineOrders, 6000);
        });
    </script>
</body>
</html>