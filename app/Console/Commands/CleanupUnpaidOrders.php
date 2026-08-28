<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup-unpaid {--hours=24 : Umur maksimal pesanan/transaksi yang belum dibayar dalam jam}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus otomatis pesanan online dan transaksi kasir yang tidak dibayar lebih dari 24 jam serta kembalikan stok produk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours') ?: 24;
        $cutoffTime = now()->subHours($hours);

        $this->info("Menjalankan pembersihan pesanan & transaksi belum bayar sebelum {$cutoffTime->toDateTimeString()} ({$hours} jam lalu)...");

        $deletedOrdersCount = 0;
        $deletedSalesCount = 0;

        // 1. BERSIHKAN PESANAN ONLINE (ORDERS) YANG BELUM BAYAR > 24 JAM
        $unpaidOrders = Order::with('items.product')
            ->where('payment_status', '!=', 'paid')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        foreach ($unpaidOrders as $order) {
            DB::beginTransaction();
            try {
                // Kembalikan stok produk yang sebelumnya terpotong saat checkout
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $orderNumber = $order->order_number;

                // Hapus item dan pesanan dari database
                $order->items()->delete();
                $order->delete();

                DB::commit();
                $deletedOrdersCount++;
                Log::info("Auto-Cleanup: Pesanan online {$orderNumber} (kadaluarsa > {$hours} jam) dihapus dan stok dikembalikan.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal menghapus pesanan online kadaluarsa {$order->order_number}: " . $e->getMessage());
            }
        }

        // 2. BERSIHKAN TRANSAKSI KASIR QRIS (SALES) YANG BELUM SELESAI / PENDING > 24 JAM
        $unpaidSales = Sale::with('details.product')
            ->where('payment_status', '!=', 'success')
            ->where('status', '!=', 'success')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        foreach ($unpaidSales as $sale) {
            DB::beginTransaction();
            try {
                // Kembalikan stok jika transaksi belum lunas
                foreach ($sale->details as $detail) {
                    if ($detail->product) {
                        $detail->product->increment('stock', $detail->quantity);
                    }
                }

                $txNumber = $sale->transaction_number;

                // Hapus detail dan transaksi dari database
                $sale->details()->delete();
                $sale->delete();

                DB::commit();
                $deletedSalesCount++;
                Log::info("Auto-Cleanup: Transaksi kasir POS {$txNumber} (kadaluarsa > {$hours} jam) dihapus dan stok dikembalikan.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal menghapus transaksi POS kadaluarsa {$sale->transaction_number}: " . $e->getMessage());
            }
        }

        $this->info("Pembersihan selesai! {$deletedOrdersCount} pesanan online dan {$deletedSalesCount} transaksi kasir kadaluarsa berhasil dihapus dari database.");
        return Command::SUCCESS;
    }
}
