<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class OnlineOrderAdminController extends Controller
{
    /**
     * DAFTAR PESANAN ONLINE (PANEL ADMIN & KASIR)
     */
    public function index(Request $request)
    {
        try {
            Artisan::call('orders:cleanup-unpaid', ['--hours' => 24]);
        } catch (\Exception $e) {}

        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Order::with(['items.product', 'confirmedByUser', 'latestComplaint']);

        if ($status === 'all') {
            // Tampilkan semua
        } elseif ($status === 'unconfirmed') {
            $query->where('status', 'paid')->where('payment_status', 'paid');
        } elseif ($status === 'pending') {
            $query->where('payment_status', '!=', 'paid');
        } else {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
        ]);
    }

    /**
     * DETAIL PESANAN ONLINE
     */
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'confirmedByUser', 'complaint'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * UPDATE STATUS & RESI PESANAN
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'          => 'required|string|in:pending_payment,paid,processing,shipped,completed,cancelled',
            'courier'         => 'nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order->update([
            'status'          => $request->status,
            'courier'         => $request->courier ?: $order->courier,
            'tracking_number' => $request->tracking_number ? strtoupper(trim($request->tracking_number)) : $order->tracking_number,
        ]);

        return back()->with('success', "Data pesanan {$order->order_number} berhasil diperbarui.");
    }

    /**
     * KONFIRMASI PESANAN (paid -> processing)
     */
    public function confirmOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status !== 'paid' && $order->status !== 'paid') {
            return back()->with('error', 'Pesanan belum dibayar atau status tidak valid.');
        }

        $order->update([
            'status'       => 'processing',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', "Pesanan {$order->order_number} berhasil dikonfirmasi dan siap diproses!");
    }

    /**
     * KIRIM PESANAN & INPUT RESI (processing -> shipped)
     */
    public function shipOrder(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier'         => 'nullable|string|max:50',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'tracking_number' => strtoupper(trim($request->tracking_number)),
            'courier'         => $request->courier ?: $order->courier,
            'status'          => 'shipped',
            'shipped_at'      => now(),
        ]);

        return back()->with('success', "Pesanan {$order->order_number} berhasil dikirim dengan Nomor Resi: {$order->tracking_number}!");
    }

    /**
     * SELESAIKAN PESANAN (shipped -> completed)
     */
    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', "Pesanan {$order->order_number} telah ditandai selesai.");
    }

    /**
     * BATALKAN PESANAN & KEMBALIKAN STOK
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Pesanan sudah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'cancelled',
            ]);

            // Kembalikan stok fisik jika pesanan belum selesai
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            DB::commit();
            return back()->with('success', "Pesanan {$order->order_number} telah dibatalkan dan stok produk telah dikembalikan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * UBAH PROSES / STATUS PESANAN ONLINE SECARA FLEKSIBEL DARI MODAL DETAIL
     */
    public function updateProcess(Request $request, $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        $request->validate([
            'status'          => 'required|in:paid,processing,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'courier'         => 'nullable|string|max:50',
            'customer_notes'  => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $updateData = [
                'status'         => $newStatus,
                'courier'        => $request->courier ?: $order->courier,
                'customer_notes' => $request->customer_notes ?: $order->customer_notes,
            ];

            if ($request->filled('tracking_number')) {
                $updateData['tracking_number'] = strtoupper(trim($request->tracking_number));
            }

            if ($newStatus === 'processing' && !$order->confirmed_at) {
                $updateData['confirmed_by'] = Auth::id();
                $updateData['confirmed_at'] = now();
            } elseif ($newStatus === 'shipped' && !$order->shipped_at) {
                $updateData['shipped_at'] = now();
            } elseif ($newStatus === 'completed' && !$order->completed_at) {
                $updateData['completed_at'] = now();
            } elseif ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                // Kembalikan stok
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update($updateData);
            DB::commit();

            return back()->with('success', "Status pesanan {$order->order_number} berhasil diperbarui menjadi " . strtoupper($order->status_label) . "!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal memperbarui proses pesanan: " . $e->getMessage());
        }
    }

    /**
     * REALTIME POLLING NOTIFIKASI PESANAN BARU (KASIR & ADMIN)
     */
    public function checkNewOrders()
    {
        // Ambil pesanan yang berstatus 'paid' & payment_status 'paid' (Sudah bayar tapi belum dikonfirmasi)
        $unconfirmedOrders = Order::with('items')
            ->where('status', 'paid')
            ->where('payment_status', 'paid')
            ->orderBy('paid_at', 'desc')
            ->get();

        $latestOrder = $unconfirmedOrders->first();

        return response()->json([
            'count'        => $unconfirmedOrders->count(),
            'has_new'      => ($unconfirmedOrders->count() > 0),
            'latest_order' => $latestOrder ? [
                'id'            => $latestOrder->id,
                'order_number'  => $latestOrder->order_number,
                'customer_name' => $latestOrder->customer_name,
                'total_amount'  => $latestOrder->total_amount,
                'formatted_total' => $latestOrder->formatted_total,
                'courier'       => $latestOrder->courier,
                'items_count'   => $latestOrder->items->sum('quantity'),
                'paid_at_human' => $latestOrder->paid_at ? $latestOrder->paid_at->diffForHumans() : 'Baru saja',
            ] : null,
        ]);
    }

    /**
     * CETAK LABEL RESI PENGIRIMAN A6 DARI PESANAN ONLINE
     */
    public function printShippingLabel($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $shop = Setting::pluck('value', 'key')->all();

        $recipient = [
            'name'    => $order->customer_name,
            'phone'   => $order->customer_phone,
            'address' => $order->customer_address,
        ];

        $sender = [
            'name'    => $shop['shop_name'] ?? 'TOKO BERKAH',
            'phone'   => $shop['shop_phone'] ?? '081234567890',
            'address' => $shop['shop_address'] ?? 'Jember, Jawa Timur',
        ];

        $courier = $order->courier ?: 'J&T Express';
        $trackingNumber = $order->tracking_number ?: $order->order_number;
        $notes = $order->customer_notes ?: 'FRAGILE - JANGAN DIBANTING';

        $pdf = Pdf::loadView('shipping.label_pdf', [
            'order'          => $order,
            'recipient'      => $recipient,
            'sender'         => $sender,
            'courier'        => $courier,
            'trackingNumber' => $trackingNumber,
            'notes'          => $notes,
            'shop'           => $shop,
            'items'          => $order->items,
        ])->setPaper('a6', 'portrait');

        return $pdf->stream("Label-Resi-{$order->order_number}.pdf");
    }

    /**
     * CETAK STRUK THERMAL PESANAN ONLINE (UNTUK KASIR/ADMIN)
     */
    public function printThermalReceipt($id)
    {
        $order = Order::with(['items.product', 'confirmedByUser'])->findOrFail($id);
        $shop = Setting::pluck('value', 'key')->all();

        $logoBase64 = $this->getGrayscaleLogoBase64($shop['shop_logo'] ?? null);

        $pdf = Pdf::loadView('online.print-receipt', compact('order', 'shop', 'logoBase64'))
                  ->setPaper([0, 0, 164.41, 750], 'portrait'); 

        return $pdf->stream("Struk-{$order->order_number}.pdf");
    }

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
}
