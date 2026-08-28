<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting; 
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str; 
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class SaleController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    public function index()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name', 'asc')->get();

        return Inertia::render('Cashier/Pos', [
            'products' => $products,
        ]);
    }

    public function checkProduct(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();
        if ($product) {
            if ($product->stock > 0) return response()->json($product);
            return response()->json(['error' => 'Stok produk habis!'], 422);
        }
        return response()->json(['error' => 'Produk tidak ditemukan!'], 404);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        if (!$query) return response()->json([]);
        $products = Product::where('name', 'LIKE', "%{$query}%")
                            ->where('stock', '>', 0)
                            ->limit(10)
                            ->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $total = $request->input('total') ?? $request->input('total_amount');
        $amountPaid = $request->input('amount_paid') ?? $request->input('cash_given') ?? $total;

        $request->merge([
            'total'       => $total,
            'amount_paid' => $amountPaid,
        ]);

        $request->validate([
            'customer_name'  => 'nullable|string|max:100',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.quantity'=> 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'total'          => 'required|numeric',
            'amount_paid'    => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate Nomor Transaksi Dinamis Sesuai Nama Aplikasi Toko
            $appName = Setting::where('key', 'app_name')->value('value') ?: 'SIKANDA';
            $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $appName));
            $prefix = strlen($cleanName) >= 3 ? substr($cleanName, 0, 3) : 'INV';
            $transactionNumber = "{$prefix}-" . now()->format('Ymd') . "-" . strtoupper(Str::random(5));

            // 2. Simpan Data Utama
            $sale = Sale::create([
                'transaction_number' => $transactionNumber,
                'user_id'            => Auth::id(),
                'customer_name'      => $request->customer_name ?: 'Pelanggan Umum',
                'total_amount'       => $request->total,
                'amount_paid'        => $request->amount_paid,
                'payment_method'     => $request->payment_method,
                'payment_status'     => ($request->payment_method === 'cash') ? 'success' : 'pending',
                'status'             => ($request->payment_method === 'cash') ? 'success' : 'pending',
            ]);

            // 3. Simpan Detail & Potong Stok
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup!");
                }
                $sale->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price,
                    'discount_at_transaction' => $product->discount_percent ?? 0,
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            // 4. LOGIKA QRIS
            $qrString = null;
            if ($request->payment_method === 'qris') {
                $qrString = $this->dokuService->generateQris($sale); 
                if (!$qrString) {
                    throw new \Exception('Gagal generate QRIS. Gunakan Tunai.');
                }
            }

            DB::commit();

            // --- INTEGRASI NOTIFIKASI TELEGRAM (KHUSUS CASH) ---
            if ($sale->payment_method === 'cash') {
                $this->sendTelegramCashNotification($sale);
            }

            // Generate Signed URL untuk unduh faktur berlaku 24 jam
            $signedInvoiceUrl = URL::temporarySignedRoute(
                'invoice.public.signed',
                now()->addHours(24),
                ['transaction_number' => $sale->transaction_number]
            );

            return response()->json([
                'success'            => true,
                'sale'               => $sale,
                'sale_id'            => $sale->id,
                'qris_url'           => $qrString,
                'qr_string'          => $qrString,
                'signed_invoice_url' => $signedInvoiceUrl
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Transaksi POS Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Kirim Notifikasi Transaksi Kasir (TUNAI) ke Telegram Pemilik Toko
     */
    private function sendTelegramNotification(Sale $sale)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) return;

        // Muat ulang relasi detail untuk mendapatkan nama produk
        $sale->load('details.product');

        $itemDetails = "";
        foreach ($sale->details as $index => $detail) {
            $itemName = $detail->product->name ?? 'Produk';
            $itemDetails .= ($index + 1) . ". " . $itemName . " (x" . $detail->quantity . ")\n";
        }

        $customerName = $sale->customer_name ?? 'Pelanggan Umum';
        $shopName = Setting::where('key', 'shop_name')->value('value') ?: 'KASIR';
        $appName = Setting::where('key', 'app_name')->value('value') ?: 'POS';

        $message = "💵 *LAPORAN TUNAI (CASH) - {$shopName}* 💵\n\n"
                 . "💰 *TOTAL:* Rp " . number_format($sale->total_amount, 0, ',', '.') . "\n"
                 . "🧾 *INVOICE:* `" . $sale->transaction_number . "`\n"
                 . "👤 *PELANGGAN:* " . $customerName . "\n"
                 . "⏰ *WAKTU:* " . now()->format('d/m/Y H:i') . " WIB\n"
                 . "🧑‍💼 *KASIR:* " . Auth::user()->name . "\n\n"
                 . "📦 *RINCIAN BARANG:*\n"
                 . "_" . $itemDetails . "_\n"
                 . "✅ *Status:* Transaksi Selesai & Uang Diterima.\n"
                 . "💻 *SISTEM:* {$appName}";

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal kirim Telegram Cash: " . $e->getMessage());
        }
    }

    public function checkStatus(Sale $sale)
    {
        $isPaid = in_array(strtolower($sale->payment_status ?? ''), ['success', 'paid']);
        $isStatusOk = in_array(strtolower($sale->status ?? ''), ['success', 'paid']);

        // Jika di lokal belum lunas, aktif cek langsung ke DOKU API sebagai fallback realtime
        if (!$isPaid && !$isStatusOk) {
            try {
                $isPaidOnDoku = $this->dokuService->checkPaymentStatus($sale->transaction_number);
                if ($isPaidOnDoku) {
                    $sale->update([
                        'status'         => 'success',
                        'payment_status' => 'success',
                    ]);
                    $isPaid = true;
                    $isStatusOk = true;
                }
            } catch (\Exception $e) {}
        }

        return response()->json([
            'status' => ($isPaid || $isStatusOk) ? 'success' : 'pending'
        ]);
    }

    public function forceConfirm(Sale $sale)
    {
        $sale->update([
            'status'         => 'success',
            'payment_status' => 'success',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil dikonfirmasi lunas.']);
    }

    public function generateReceipt(Sale $sale)
    {
        $sale->load(['details.product', 'user']);
        $shop = Setting::pluck('value', 'key')->all();
        
        $logoBase64 = $this->getGrayscaleLogoBase64($shop['shop_logo'] ?? null);
        
        $pdf = Pdf::loadView('cashier.print-receipt', compact('sale', 'shop', 'logoBase64'))
                  ->setPaper([0, 0, 164.41, 600], 'portrait'); 
        return $pdf->stream("Nota-{$sale->transaction_number}.pdf");
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
            if (extension_loaded('gd')) {
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo) {
                    $mime = $imageInfo['mime'];
                    $im = null;
                    if ($mime === 'image/png') {
                        $im = @imagecreatefrompng($fullPath);
                    } elseif ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                        $im = @imagecreatefromjpeg($fullPath);
                    } elseif ($mime === 'image/webp') {
                        $im = @imagecreatefromwebp($fullPath);
                    }

                    if ($im) {
                        $width = imagesx($im);
                        $height = imagesy($im);
                        
                        // Buat canvas baru dengan latar belakang PUTIH MURNI (agar transparansi tidak menjadi hitam)
                        $canvas = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($canvas, 255, 255, 255);
                        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
                        
                        // Salin gambar ke atas background putih
                        imagecopy($canvas, $im, 0, 0, 0, 0, $width, $height);
                        
                        // Terapkan grayscale / hitam putih
                        imagefilter($canvas, IMG_FILTER_GRAYSCALE);
                        
                        ob_start();
                        imagepng($canvas);
                        $output = ob_get_clean();
                        imagedestroy($canvas);
                        imagedestroy($im);
                        return 'data:image/png;base64,' . base64_encode($output);
                    }
                }
            }

            // Fallback
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (\Exception $e) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }

    /**
     * Generate Label Resi Pengiriman Paket (Ukuran Standar A6 Portrait / Sticker)
     */
    public function generateShippingLabel(Request $request, Sale $sale)
    {
        $sale->load(['details.product', 'user']);
        $shop = Setting::pluck('value', 'key')->all();

        $recipientName = $request->input('recipient_name', $sale->customer_name ?: 'Pelanggan Umum');
        $recipientPhone = $request->input('recipient_phone', '');
        $recipientAddress = $request->input('recipient_address', '');
        $senderName = $request->input('sender_name', $shop['shop_name'] ?? 'PENGIRIM');
        $senderPhone = $request->input('sender_phone', $shop['shop_phone'] ?? '');
        $senderAddress = $request->input('sender_address', $shop['shop_address'] ?? '');
        $courier = $request->input('courier', 'Reguler');
        $notes = $request->input('notes', 'FRAGILE - JANGAN DIBANTING / DITINDIH');

        $pdf = Pdf::loadView('shipping.label_pdf', compact(
            'sale', 'shop', 'recipientName', 'recipientPhone', 'recipientAddress',
            'senderName', 'senderPhone', 'senderAddress', 'courier', 'notes'
        ))->setPaper('a6', 'portrait');

        return $pdf->stream("Label_Pengiriman_{$sale->transaction_number}.pdf");
    }
}