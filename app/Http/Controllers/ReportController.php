<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $salesData = collect();
        $period = $request->input('period', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));
        $reportTitle = 'Laporan Penjualan';

        // Hanya jalankan query jika user memfilter
        if ($request->has('filter')) {
            // PERBAIKAN: Menambahkan whereHas untuk memastikan produk ada
            $query = SaleDetail::whereHas('product')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->selectRaw('products.name as product_name, products.price as product_price, SUM(sale_details.quantity) as total_quantity, SUM(sale_details.quantity * products.price) as total_sales')
                ->groupBy('products.id', 'products.name', 'products.price');

            switch ($period) {
                case 'daily':
                    $query->whereDate('sale_details.created_at', $date);
                    $reportTitle = 'Laporan Penjualan Harian Tanggal ' . Carbon::parse($date)->format('d F Y');
                    break;
                case 'weekly':
                    $startOfWeek = Carbon::parse($date)->startOfWeek();
                    $endOfWeek = Carbon::parse($date)->endOfWeek();
                    $query->whereBetween('sale_details.created_at', [$startOfWeek, $endOfWeek]);
                    $reportTitle = 'Laporan Penjualan Mingguan (' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y') . ')';
                    break;
                case 'monthly':
                    $query->whereYear('sale_details.created_at', Carbon::parse($month)->year)
                          ->whereMonth('sale_details.created_at', Carbon::parse($month)->month);
                    $reportTitle = 'Laporan Penjualan Bulanan ' . Carbon::parse($month)->format('F Y');
                    break;
                case 'yearly':
                    $query->whereYear('sale_details.created_at', $year);
                    $reportTitle = 'Laporan Penjualan Tahunan ' . $year;
                    break;
            }
            $salesData = $query->get();
        }

        if ($request->input('action') == 'download_pdf') {
            // Jalankan query lagi untuk memastikan data ada untuk PDF
            $pdfQuery = SaleDetail::whereHas('product')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->selectRaw('products.name as product_name, products.price as product_price, SUM(sale_details.quantity) as total_quantity, SUM(sale_details.quantity * products.price) as total_sales')
                ->groupBy('products.id', 'products.name', 'products.price');
            
            // Terapkan filter yang sama untuk PDF
            switch ($period) {
                case 'daily':
                    $pdfQuery->whereDate('sale_details.created_at', $date);
                    $reportTitle = 'Laporan Penjualan Harian Tanggal ' . Carbon::parse($date)->format('d F Y');
                    break;
                case 'weekly':
                    $startOfWeek = Carbon::parse($date)->startOfWeek();
                    $endOfWeek = Carbon::parse($date)->endOfWeek();
                    $pdfQuery->whereBetween('sale_details.created_at', [$startOfWeek, $endOfWeek]);
                    $reportTitle = 'Laporan Penjualan Mingguan (' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y') . ')';
                    break;
                case 'monthly':
                    $pdfQuery->whereYear('sale_details.created_at', Carbon::parse($month)->year)
                          ->whereMonth('sale_details.created_at', Carbon::parse($month)->month);
                    $reportTitle = 'Laporan Penjualan Bulanan ' . Carbon::parse($month)->format('F Y');
                    break;
                case 'yearly':
                    $pdfQuery->whereYear('sale_details.created_at', $year);
                    $reportTitle = 'Laporan Penjualan Tahunan ' . $year;
                    break;
            }
            $salesDataForPdf = $pdfQuery->get();

            $pdf = PDF::loadView('reports.pdf', ['salesData' => $salesDataForPdf, 'reportTitle' => $reportTitle])->setPaper('a4', 'landscape');
            return $pdf->download(strtolower(str_replace(' ', '_', $reportTitle)) . '.pdf');
        }

        return view('reports.index', compact('salesData', 'reportTitle', 'period', 'date', 'month', 'year'));
    }
}