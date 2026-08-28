<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\DokuService;
use App\Services\TurnstileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OnlineOrderController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * TOKO ONLINE PUBLIK (GUEST / TANPA LOGIN)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Product::where('stock', '>', 0);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return Inertia::render('Online/Store', [
            'products' => $products,
            'search'   => $search,
        ]);
    }

    /**
     * CHECKOUT PESANAN ONLINE
     */
    public function store(Request $request)
    {
        // 1. Verifikasi Cloudflare Turnstile jika aktif
        if (TurnstileService::isEnabled()) {
            $turnstileToken = $request->input('cf-turnstile-response');
            if (!TurnstileService::verify($turnstileToken, $request->ip())) {
                return redirect()->route('order.index')->with('error', 'Verifikasi keamanan Cloudflare Turnstile gagal atau kadaluarsa. Silakan refresh dan coba lagi.');
            }
        }

        // Dukung items baik dari array input maupun items_json
        if ($request->filled('items_json') && is_string($request->input('items_json'))) {
            $decoded = json_decode($request->input('items_json'), true);
            if (is_array($decoded) && count($decoded) > 0) {
                $request->merge(['items' => $decoded]);
            }
        }

        $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'customer_notes'   => 'nullable|string|max:500',
            'courier'          => 'required|string|max:50',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor order unik
            $appName = Setting::where('key', 'app_name')->value('value') ?: 'SIKANDA';
            $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $appName));
            $prefix = strlen($cleanName) >= 3 ? substr($cleanName, 0, 3) : 'ORD';
            $orderNumber = "ORD-" . now()->format('Ymd') . "-" . strtoupper(Str::random(5));

            $totalAmount = 0;
            $itemsData = [];

            // Validasi dan hitung total barang
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock} pcs).");
                }

                $price = $product->price;
                if (!empty($product->discount_percent) && $product->discount_percent > 0) {
                    $price = $price - ($price * ($product->discount_percent / 100));
                }

                $subtotal = $price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                ];

                // Kurangi stok fisik produk
                $product->decrement('stock', $item['quantity']);
            }

            // Simpan pesanan utama
            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'customer_notes'   => $request->customer_notes,
                'courier'          => $request->courier,
                'total_amount'     => $totalAmount,
                'payment_method'   => 'qris',
                'payment_status'   => 'pending',
                'status'           => 'pending_payment',
            ]);

            // Simpan detail item pesanan
            foreach ($itemsData as $itemRow) {
                $order->items()->create($itemRow);
            }

            // Generate Dynamic QRIS DOKU
            $qrisUrl = null;
            try {
                $qrisUrl = $this->dokuService->generateOrderQris($order);
                if ($qrisUrl) {
                    $order->update(['qris_url' => $qrisUrl]);
                }
            } catch (\Exception $e) {
                Log::warning("DOKU Order QRIS Fallback: " . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('order.pay', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * HALAMAN PEMBAYARAN QRIS PUBLIK
     */
    public function pay($order_number)
    {
        $order = Order::with('items')->where('order_number', $order_number)->firstOrFail();

        // Jika sudah bayar, langsung arahkan ke tanda terima / lacak
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.receipt', $order->order_number);
        }

        // Pastikan QRIS URL resmi DOKU tersedia sama persis seperti di kasir POS
        if (empty($order->qris_url)) {
            try {
                $qrisUrl = $this->dokuService->generateOrderQris($order);
                if ($qrisUrl) {
                    $order->update(['qris_url' => $qrisUrl]);
                }
            } catch (\Exception $e) {
                Log::warning("DOKU Order QRIS Pay Generate Error: " . $e->getMessage());
            }
        }

        return Inertia::render('Online/Pay', [
            'order'            => $order,
            'doku_payment_url' => $order->qris_url,
        ]);
    }

    /**
     * AMBIL ATAU GENERATE QRIS URL SECARA AJAX
     */
    public function getQris($order_number)
    {
        $order = Order::where('order_number', $order_number)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if (!empty($order->qris_url)) {
            return response()->json(['success' => true, 'qris_url' => $order->qris_url]);
        }

        try {
            $qrisUrl = $this->dokuService->generateOrderQris($order);
            if ($qrisUrl) {
                $order->update(['qris_url' => $qrisUrl]);
                return response()->json(['success' => true, 'qris_url' => $qrisUrl]);
            }
        } catch (\Exception $e) {
            Log::error("DOKU getQris API Error: " . $e->getMessage());
        }

        return response()->json(['success' => false, 'message' => 'Gagal membuat QRIS DOKU. Silakan coba beberapa saat lagi.'], 500);
    }

    /**
     * CEK STATUS PEMBAYARAN (AJAX POLLING DARI BROWSER PEMBELI)
     */
    public function checkStatus($order_number)
    {
        $order = Order::where('order_number', $order_number)->first();
        if (!$order) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($order->payment_status !== 'paid') {
            try {
                $isPaidOnDoku = $this->dokuService->checkPaymentStatus($order->order_number);
                if ($isPaidOnDoku) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status'         => 'paid',
                        'paid_at'        => now(),
                    ]);
                }
            } catch (\Exception $e) {}
        }

        $level = match($order->status) {
            'pending_payment' => 1,
            'paid'            => 2,
            'processing'      => 3,
            'shipped'         => 4,
            'completed'       => 5,
            default           => 1,
        };

        return response()->json([
            'order_number'    => $order->order_number,
            'payment_status'  => $order->payment_status,
            'status'          => $order->status,
            'status_label'    => $order->status_label,
            'status_badge'    => $order->status_badge,
            'tracking_number' => $order->tracking_number,
            'courier'         => $order->courier,
            'level'           => $level,
            'is_paid'         => ($order->payment_status === 'paid'),
            'redirect_url'    => route('order.receipt', $order->order_number),
        ]);
    }

    /**
     * SIMULASI PEMBAYARAN QRIS (KHUSUS TESTING / DEMO SANDBOX)
     */
    public function simulatePay($order_number)
    {
        $order = Order::with('items.product')->where('order_number', $order_number)->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('order.receipt', $order->order_number)->with('success', 'Pesanan sudah lunas sebelumnya.');
        }

        $order->update([
            'payment_status' => 'paid',
            'status'         => 'paid', // Status: Menunggu Konfirmasi Toko
            'paid_at'        => now(),
        ]);

        return redirect()->route('order.receipt', $order->order_number)->with('success', 'Pembayaran QRIS Berhasil Dikonfirmasi!');
    }

    /**
     * STRUK / BUKTI PESANAN DIGITAL ONLINE
     */
    public function receipt($order_number)
    {
        $order = Order::with(['items.product', 'confirmedByUser'])->where('order_number', $order_number)->firstOrFail();

        return Inertia::render('Online/Receipt', [
            'order' => $order,
        ]);
    }

    /**
     * CETAK STRUK THERMAL PDF RESMI (UKURAN 58MM/80MM LENGKAP DENGAN QR LACAK)
     */
    public function printReceiptPdf($order_number)
    {
        $order = Order::with(['items.product', 'confirmedByUser'])->where('order_number', $order_number)->firstOrFail();
        $shop = Setting::pluck('value', 'key')->all();

        $logoBase64 = $this->getGrayscaleLogoBase64($shop['shop_logo'] ?? null);

        $pdf = Pdf::loadView('online.print-receipt', compact('order', 'shop', 'logoBase64'))
                  ->setPaper([0, 0, 164.41, 750], 'portrait'); 

        return $pdf->stream("Struk-{$order->order_number}.pdf");
    }

    /**
     * Konversi Logo Toko ke Format Base64 Grayscale / Hitam Putih Khusus Struk Thermal
     */
    private function getGrayscaleLogoBase64($relativePath)
    {
        if (empty($relativePath)) return null;

        $paths = [
            storage_path('app/public/' . $relativePath),
            storage_path('app/' . $relativePath),
            public_path('storage/' . $relativePath),
        ];

        $fullPath = null;
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $fullPath = $p;
                break;
            }
        }

        if (!$fullPath) return null;

        try {
            $imageData = file_get_contents($fullPath);
            if (!$imageData) return null;

            if (extension_loaded('gd')) {
                $image = @imagecreatefromstring($imageData);
                if ($image) {
                    imagefilter($image, IMG_FILTER_GRAYSCALE);
                    imagefilter($image, IMG_FILTER_CONTRAST, -15);
                    ob_start();
                    imagepng($image);
                    $grayscaleData = ob_get_clean();
                    imagedestroy($image);
                    return 'data:image/png;base64,' . base64_encode($grayscaleData);
                }
            }

            $mime = mime_content_type($fullPath) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * PORTAL CARI LACAK PESANAN (FORM INPUT RESI / ORDER NUMBER)
     */
    public function trackIndex(Request $request)
    {
        $orderNumber = $request->get('order_number') ?: $request->get('q');
        $order = null;

        if ($orderNumber) {
            $order = Order::with(['items.product', 'confirmedByUser'])
                ->where('order_number', $orderNumber)
                ->orWhere('tracking_number', $orderNumber)
                ->first();
        }

        return Inertia::render('Online/Track', [
            'order'        => $order,
            'order_number' => $orderNumber,
        ]);
    }

    /**
     * DETAIL PELACAKAN PESANAN (STEPPER VISUAL)
     */
    public function track($order_number)
    {
        $order = Order::with(['items.product', 'confirmedByUser'])->where('order_number', $order_number)->first();
        
        if (!$order) {
            $order = Order::with(['items.product', 'confirmedByUser'])->where('tracking_number', $order_number)->first();
        }

        return Inertia::render('Online/Track', [
            'order'        => $order,
            'order_number' => $order_number,
        ]);
    }

    /**
     * KONFIRMASI PESANAN DITERIMA OLEH PELANGGAN (MENYELESAIKAN PESANAN)
     */
    public function confirmReceived(Request $request, $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        if ($order->status === 'completed') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pesanan sudah diselesaikan sebelumnya.']);
            }
            return back()->with('info', 'Pesanan sudah selesai sebelumnya.');
        }

        if ($order->status === 'cancelled') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Pesanan telah dibatalkan.'], 422);
            }
            return back()->with('error', 'Pesanan telah dibatalkan.');
        }

        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Terima kasih! Pesanan Anda telah berhasil diselesaikan.',
                'status'       => 'completed',
                'status_label' => $order->status_label,
                'level'        => 5,
            ]);
        }

        return back()->with('success', 'Terima kasih! Pesanan Anda telah ditandai diterima dan selesai.');
    }
}
