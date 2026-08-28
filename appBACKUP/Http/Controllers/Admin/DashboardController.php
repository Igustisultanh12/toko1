<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'cashier') {
            return redirect()->route('cashier.pos.index');
        }
        
        $pendapatanHariIni = Sale::whereDate('created_at', today())->sum('total_amount');
        $transaksiHariIni = Sale::whereDate('created_at', today())->count();
        $produkTerjualHariIni = SaleDetail::whereDate('created_at', today())->sum('quantity');
        $totalProduk = Product::count();

        $salesDataForChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Sale::whereDate('created_at', $date)->sum('total_amount');
            $salesDataForChart['labels'][] = $date->format('d M');
            $salesDataForChart['data'][] = $sales;
        }
        $chartData = json_encode($salesDataForChart);

        // ===== PERBAIKAN DI SINI: Mengambil relasi 'user' bukan 'customer' =====
        $penjualanTerakhir = Sale::with('user')->latest()->take(5)->get();

        $produkTerlaris = SaleDetail::join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_terjual'))
            ->whereMonth('sale_details.created_at', now()->month)
            ->groupBy('products.name')
            ->orderBy('total_terjual', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendapatanHariIni',
            'transaksiHariIni',
            'produkTerjualHariIni',
            'totalProduk',
            'chartData',
            'penjualanTerakhir',
            'produkTerlaris'
        ));
    }
}
