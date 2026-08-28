@extends('online.layout')

@section('title', 'Pembayaran QRIS Pesanan ' . $order->order_number)

@section('content')
<div x-data="qrisPayment" class="max-w-xl mx-auto px-4 py-8 space-y-6">

    {{-- KARTU PEMBAYARAN QRIS RESMI (PERSIS KASIR POS) --}}
    <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-gray-100 shadow-xl text-center space-y-6">
        
        {{-- HEADER STATUS --}}
        <div>
            <span class="px-4 py-1.5 bg-emerald-50 text-[#00880F] rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200 inline-block animate-pulse">
                ⚡ Pembayaran QRIS Resmi
            </span>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight mt-2">Pindai QRIS untuk Membayar</h2>
            <p class="text-xs text-gray-400 font-medium">Buka aplikasi GoPay, OVO, Dana, BCA, ShopeePay, atau Mobile Banking Anda.</p>
        </div>

        {{-- TOTAL TAGIHAN --}}
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-5 rounded-3xl border border-emerald-200/60">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Total Tagihan Pesanan:</span>
            <h3 class="text-3xl font-black text-[#00AA13] mt-1">{{ $order->formatted_total }}</h3>
            <span class="text-[10px] font-mono text-gray-500 font-bold block mt-1">No. Pesanan: {{ $order->order_number }}</span>
        </div>

        {{-- FRAME QRIS DINAMIS DOKU (PERSIS FRAME DI POS KASIR) --}}
        <div class="bg-gray-50 rounded-[2rem] overflow-hidden border-2 border-gray-100 shadow-inner w-full min-h-[500px] flex items-center justify-center relative">
            <template x-if="qrisUrl">
                <iframe :src="qrisUrl" class="w-full h-[520px] border-0 rounded-[2rem]"></iframe>
            </template>
            
            <template x-if="!qrisUrl && !loadError">
                <div class="p-8 text-center space-y-3">
                    <div class="animate-spin h-10 w-10 border-4 border-[#00AA13] border-t-transparent rounded-full mx-auto"></div>
                    <p class="font-black text-gray-500 text-xs uppercase tracking-wider">Menyiapkan QRIS Resmi DOKU...</p>
                </div>
            </template>

            <template x-if="!qrisUrl && loadError">
                <div class="p-8 text-center space-y-4 max-w-sm">
                    <div class="text-3xl">⚠️</div>
                    <p class="font-black text-gray-800 text-sm">Gagal Menghubungi Server QRIS</p>
                    <p class="text-xs text-gray-500 font-medium" x-text="errorMessage"></p>
                    <button @click="fetchQris()" class="px-6 py-2.5 bg-[#00AA13] hover:bg-[#00880F] text-white rounded-xl font-black text-xs uppercase tracking-wider shadow">
                        🔄 Coba Lagi
                    </button>
                </div>
            </template>
        </div>

        {{-- COUNTDOWN TIMER & TOMBOL CEK STATUS --}}
        <div class="space-y-3">
            <div class="flex items-center justify-center space-x-2 text-xs font-bold text-gray-500">
                <span>Selesaikan pembayaran dalam:</span>
                <span class="font-mono font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-xl text-sm" x-text="countdownText">15:00</span>
            </div>

            <button @click="checkStatusManual()" class="px-6 py-2.5 bg-emerald-50 text-[#00880F] hover:bg-emerald-100 rounded-xl text-xs font-black uppercase tracking-wider border border-emerald-200 transition">
                🔄 Sudah Transfer? Cek Status
            </button>
        </div>

        {{-- DETEKSI PEMBAYARAN OTOMATIS LIVE REALTIME --}}
        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center space-x-2 text-xs font-bold text-gray-600">
            <svg class="animate-spin h-4 w-4 text-[#00AA13]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Sistem otomatis mendeteksi ketika Anda selesai membayar...</span>
        </div>

    </div>

    {{-- RINCIAN BARANG --}}
    <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm space-y-3">
        <h4 class="font-black text-gray-900 uppercase text-xs">Rincian Barang yang Dipesan</h4>
        <div class="divide-y divide-gray-50">
            @foreach($order->items as $item)
                <div class="py-2.5 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-bold text-gray-800">{{ $item->product_name }}</p>
                        <span class="text-[10px] text-gray-400 font-medium">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </div>
                    <span class="font-black text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qrisPayment', () => ({
        orderNumber: @json($order->order_number),
        qrisUrl: @json($order->qris_url),
        loadError: false,
        errorMessage: '',
        timeLeft: 900, // 15 menit
        countdownText: '15:00',
        checkInterval: null,
        timerInterval: null,

        init() {
            // Jika QRIS URL belum tersedia, segera ambil dari backend
            if (!this.qrisUrl) {
                this.fetchQris();
            }

            // Jalankan hitung mundur 15 menit
            this.timerInterval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timerInterval);
                    this.countdownText = '00:00';
                } else {
                    const m = Math.floor(this.timeLeft / 60);
                    const s = this.timeLeft % 60;
                    this.countdownText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                }
            }, 1000);

            // Polling status pembayaran realtime via Webhook setiap 3 detik
            this.checkInterval = setInterval(() => {
                this.checkStatusAuto();
            }, 3000);

            // Tangkap event postMessage jika iframe DOKU selesai transaksi
            window.addEventListener('message', (event) => {
                if (event.data && (event.data.status === 'SUCCESS' || event.data.type === 'DOKU_PAYMENT_SUCCESS' || event.data.event === 'payment_success')) {
                    this.handlePaymentSuccess("{{ route('order.receipt', $order->order_number) }}");
                }
            });
        },

        async checkStatusAuto() {
            try {
                const res = await fetch(`/order/check-status/${this.orderNumber}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.is_paid) {
                        this.handlePaymentSuccess(data.redirect_url);
                    }
                }
            } catch (e) {
                console.log('Error checking payment status:', e);
            }
        },

        async checkStatusManual() {
            try {
                const res = await fetch(`/order/check-status/${this.orderNumber}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.is_paid) {
                        this.handlePaymentSuccess(data.redirect_url);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Menunggu Pembayaran',
                            text: 'Pembayaran QRIS Anda belum terkonfirmasi oleh server perbankan. Silakan selesaikan pembayaran di aplikasi m-banking atau e-wallet Anda.',
                            confirmButtonColor: '#00AA13'
                        });
                    }
                }
            } catch (e) {
                Swal.fire('Error', 'Gagal memeriksa status pembayaran.', 'error');
            }
        },

        handlePaymentSuccess(redirectUrl) {
            if (this.checkInterval) clearInterval(this.checkInterval);
            if (this.timerInterval) clearInterval(this.timerInterval);

            if (typeof window.playNotificationSound === 'function') {
                window.playNotificationSound('payment_success');
            }

            Swal.fire({
                icon: 'success',
                title: 'Pembayaran QRIS Lunas!',
                text: 'Pembayaran Anda berhasil diverifikasi. Mengalihkan ke struk pesanan...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = redirectUrl;
            });
        },

        async fetchQris() {
            this.loadError = false;
            this.errorMessage = '';
            try {
                const res = await fetch(`/order/get-qris/${this.orderNumber}`);
                const data = await res.json();
                if (res.ok && data.success && data.qris_url) {
                    this.qrisUrl = data.qris_url;
                } else {
                    this.loadError = true;
                    this.errorMessage = data.message || 'Gagal memuat QRIS DOKU.';
                }
            } catch (e) {
                this.loadError = true;
                this.errorMessage = 'Koneksi ke gateway DOKU terputus. Silakan coba lagi.';
            }
        }
    }));
});
</script>
@endsection
