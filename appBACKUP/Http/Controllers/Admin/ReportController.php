<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'daily');
        $date = $request->input('date', now()->format('Y-m-d'));
        $month = $request->input('month', now()->format('Y-m'));
        $year = $request->input('year', now()->format('Y'));

        $salesData = collect();
        $reportTitle = '';

        if ($request->has('filter')) {
            $query = SaleDetail::join('products', 'sale_details.product_id', '=', 'products.id')
                ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->select(
                    'products.name as product_name',
                    'products.price as product_price',
                    DB::raw('SUM(sale_details.quantity) as total_quantity'),
                    DB::raw('SUM(sale_details.quantity * sale_details.price_at_transaction) as total_sales')
                )
                ->groupBy('products.id', 'products.name', 'products.price');

            switch ($period) {
                case 'daily':
                    $query->whereDate('sales.created_at', $date);
                    $reportTitle = 'Laporan Penjualan Harian - ' . Carbon::parse($date)->translatedFormat('d F Y');
                    break;
                case 'weekly':
                    $startOfWeek = Carbon::parse($date)->startOfWeek();
                    $endOfWeek = Carbon::parse($date)->endOfWeek();
                    $query->whereBetween('sales.created_at', [$startOfWeek, $endOfWeek]);
                    $reportTitle = 'Laporan Penjualan Mingguan (' . $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y') . ')';
                    break;
                case 'monthly':
                    $query->whereYear('sales.created_at', Carbon::parse($month)->year)
                          ->whereMonth('sales.created_at', Carbon::parse($month)->month);
                    $reportTitle = 'Laporan Penjualan Bulanan - ' . Carbon::parse($month)->translatedFormat('F Y');
                    break;
                case 'yearly':
                    $query->whereYear('sales.created_at', $year);
                    $reportTitle = 'Laporan Penjualan Tahunan - ' . $year;
                    break;
            }

            $salesData = $query->get();

            if ($request->input('action') === 'download_pdf') {
                $pdf = PDF::loadView('admin.reports.pdf', compact('salesData', 'reportTitle'));
                return $pdf->download(str_replace(' ', '_', strtolower($reportTitle)) . '.pdf');
            }
        }

        return view('admin.reports.index', compact('salesData', 'reportTitle', 'period', 'date', 'month', 'year'));
    }

    /**
     * PERBAIKAN: Mengambil semua transaksi harian, bukan ringkasan.
     */
    public function printDailyReport(Request $request)
    {
        $today = now()->format('Y-m-d');

        // Mengambil semua data penjualan hari ini, beserta relasi yang dibutuhkan
        $sales = Sale::whereDate('created_at', $today)
                     ->with(['saleDetails.product', 'user']) // Memuat relasi user (kasir)
                     ->get();

        // Menghitung total pendapatan hari ini
        $totalRevenue = $sales->sum('total_amount');

        // Mengirim data ke view
        return view('admin.reports.print-daily', [
            'sales' => $sales, // Mengirim semua data transaksi
            'totalRevenue' => $totalRevenue,
            'reportDate' => now()->translatedFormat('l, d F Y'),
        ]);
    }
}
