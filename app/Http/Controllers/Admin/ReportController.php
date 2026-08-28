<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\QRCodeService;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Helper Query untuk Laporan Penjualan (Harian, Bulanan, 3 Bulan, Tahunan)
     */
    private function buildSalesReportQuery(Request $request)
    {
        $period = $request->input('period', 'all');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $quarter = (int) $request->input('quarter', ceil(date('n') / 3));
        $year = (int) $request->input('year', date('Y'));
        $paymentMethod = $request->input('payment_method', 'all');
        $paymentStatus = $request->input('payment_status', 'all');
        $search = $request->input('search');

        $query = Sale::with(['details.product', 'user']);

        // 1. Filter Periode
        switch ($period) {
            case 'daily':
                $query->whereDate('created_at', $date);
                $periodLabel = 'Harian (' . Carbon::parse($date)->translatedFormat('d F Y') . ')';
                break;

            case 'monthly':
                $carbonMonth = Carbon::parse($month);
                $query->whereYear('created_at', $carbonMonth->year)
                      ->whereMonth('created_at', $carbonMonth->month);
                $periodLabel = 'Bulanan (' . $carbonMonth->translatedFormat('F Y') . ')';
                break;

            case 'quarterly':
            case '3_months':
                $qYear = $year ?: (int) date('Y');
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                $startDate = Carbon::create($qYear, $startMonth, 1)->startOfDay();
                $endDate = Carbon::create($qYear, $endMonth, 1)->endOfMonth()->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                
                $quarterNames = [
                    1 => 'Kuartal 1 (Januari - Maret)',
                    2 => 'Kuartal 2 (April - Juni)',
                    3 => 'Kuartal 3 (Juli - September)',
                    4 => 'Kuartal 4 (Oktober - Desember)',
                ];
                $periodLabel = 'Periode 3 Bulan - ' . ($quarterNames[$quarter] ?? "Q{$quarter}") . ' ' . $qYear;
                break;

            case 'yearly':
                $query->whereYear('created_at', $year);
                $periodLabel = 'Tahunan (' . $year . ')';
                break;

            case 'all':
            default:
                $period = 'all';
                $periodLabel = 'Semua Periode';
                break;
        }

        // 2. Filter Metode Pembayaran
        if ($paymentMethod && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        // 3. Filter Status Pembayaran
        if ($paymentStatus && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        // 4. Filter Pencarian No. Invoice atau Nama Pelanggan
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $filters = [
            'period'         => $period,
            'date'           => $date,
            'month'          => $month,
            'quarter'        => $quarter,
            'year'           => $year,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'search'         => $search,
        ];

        return [$query, $periodLabel, $filters];
    }

    /**
     * Halaman Utama Laporan Penjualan (Pusat Laporan - Vue 3 Inertia)
     */
    public function index(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $allMatchingSales = (clone $query)->get();

        $stats = [
            'total_revenue'      => (float) $allMatchingSales->where('payment_status', 'success')->sum('total_amount'),
            'total_transactions' => (int) $allMatchingSales->count(),
            'total_items_sold'   => (int) $allMatchingSales->sum(fn($s) => $s->details->sum('quantity')),
            'cash_revenue'       => (float) $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount'),
            'qris_revenue'       => (float) $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount'),
            'pending_count'      => (int) $allMatchingSales->where('payment_status', 'pending')->count(),
        ];

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Reports/Index', [
            'transactions' => $transactions,
            'stats'        => $stats,
            'periodLabel'  => $periodLabel,
            'filters'      => $filters,
        ]);
    }

    /**
     * Helper untuk konversi angka bulan ke angka romawi (I - XII)
     */
    private function getRomanMonth($monthNumber = null)
    {
        $month = (int) ($monthNumber ?: date('n'));
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romans[$month] ?? 'I';
    }

    /**
     * Helper untuk generate nomor dokumen resmi format dinamis:
     */
    private function generateDocNumber(string $prefix, ?string $subType, array $shop, $targetDate = null)
    {
        if ($targetDate) {
            $carbonDate = is_string($targetDate) ? Carbon::parse($targetDate) : $targetDate;
        } else {
            $carbonDate = Carbon::now();
        }

        $day = $carbonDate->format('d');
        $romanMonth = $this->getRomanMonth($carbonDate->month);
        $appName = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($shop['app_name'] ?? 'SIBALOG')) ?: 'SIBALOG';
        $year = $carbonDate->format('Y');

        $sub = $subType ? strtoupper($subType) : 'ALL';
        return "{$prefix}-{$sub}/{$day}/{$romanMonth}/{$appName}/{$year}";
    }

    /**
     * Helper untuk Generate Data Penandatangan TTE Dinamis Sesuai User yang Mencetak
     */
    private function getSignerData(array $shop, string $docType, string $docNo)
    {
        $user = Auth::user();
        $signerName = $user ? $user->name : ($shop['cashier_officer_name'] ?? 'Admin');
        $signerTitle = ($user && !empty($user->alias))
            ? $user->alias
            : ($user ? ($user->role === 'admin' ? ($shop['cashier_officer_title'] ?? 'Administrator Toko') : 'Petugas Kasir') : ($shop['cashier_officer_title'] ?? 'Petugas Kasir'));

        $verifyUrl = route('verify.document', [
            'type'      => $docType,
            'doc_no'    => $docNo,
            'signer'    => base64_encode($signerName),
            'title'     => base64_encode($signerTitle),
            'timestamp' => time(),
        ]);

        $tteQrBase64 = QRCodeService::generateBase64($verifyUrl, 160);

        return [$signerName, $signerTitle, $verifyUrl, $tteQrBase64];
    }

    /**
     * Export Laporan Penjualan ke PDF (DomPDF Landscape A4 dengan TTE QR)
     */
    public function exportPdf(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalRevenue = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalItems = $transactions->sum(fn($s) => $s->details->sum('quantity'));

        $targetDate = null;
        if (!empty($filters['date'])) {
            $targetDate = $filters['date'];
        } elseif (!empty($filters['month'])) {
            $targetDate = Carbon::parse($filters['month'])->startOfMonth();
        } elseif (!empty($filters['year'])) {
            $targetDate = Carbon::createFromDate($filters['year'], 1, 1);
        } elseif ($transactions->isNotEmpty()) {
            $targetDate = $transactions->first()->created_at;
        } else {
            $targetDate = now();
        }

        $method = strtolower($filters['payment_method'] ?? 'all');
        $subType = ($method === 'cash') ? 'TUNAI' : (($method === 'qris') ? 'QRIS' : 'SEMUA');
        $docNumber = $this->generateDocNumber('LPJ', $subType, $shop, $targetDate);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'sales', $docNumber);

        $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'periodLabel', 'filters', 'shop', 'totalRevenue', 'totalItems', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Penjualan_'.date('Ymd_His').'.pdf');
    }

    /**
     * Export Laporan Penjualan ke Excel
     */
    public function exportExcel(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();
        $totalRevenue = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalItems = $transactions->sum(fn($s) => $s->details->sum('quantity'));

        return Excel::download(new class($transactions, $periodLabel, $filters, $shop, $totalRevenue, $totalItems) implements FromView, ShouldAutoSize {
            protected $transactions, $periodLabel, $filters, $shop, $totalRevenue, $totalItems;

            public function __construct($transactions, $periodLabel, $filters, $shop, $totalRevenue, $totalItems)
            {
                $this->transactions = $transactions;
                $this->periodLabel  = $periodLabel;
                $this->filters      = $filters;
                $this->shop         = $shop;
                $this->totalRevenue = $totalRevenue;
                $this->totalItems   = $totalItems;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('reports.excel', [
                    'transactions' => $this->transactions,
                    'periodLabel'  => $this->periodLabel,
                    'filters'      => $this->filters,
                    'shop'         => $this->shop,
                    'totalRevenue' => $this->totalRevenue,
                    'totalItems'   => $this->totalItems,
                ]);
            }
        }, 'Laporan_Penjualan_'.date('Ymd_His').'.xlsx');
    }

    /**
     * Cetak Struk Ringkasan Harian (Thermal)
     */
    public function printDailyReport(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $sales = Sale::with('details.product')
            ->whereDate('created_at', $date)
            ->where('payment_status', 'success')
            ->get();

        $totalCash = $sales->where('payment_method', 'cash')->sum('total_amount');
        $totalQris = $sales->where('payment_method', 'qris')->sum('total_amount');
        $totalRevenue = $totalCash + $totalQris;
        $totalItems = $sales->sum(fn($s) => $s->details->sum('quantity'));
        $totalTransactions = $sales->count();

        $shop = Setting::pluck('value', 'key')->all();

        return view('reports.print_daily', compact('date', 'sales', 'totalCash', 'totalQris', 'totalRevenue', 'totalItems', 'totalTransactions', 'shop'));
    }

    /**
     * Download Faktur Penjualan (Invoice) Format PDF DomPDF dengan QR TTE
     */
    public function exportInvoicePdf($sale)
    {
        if (is_numeric($sale)) {
            $sale = Sale::with(['details.product', 'user'])->findOrFail($sale);
        } else {
            $sale = Sale::with(['details.product', 'user'])->where('transaction_number', $sale)->firstOrFail();
        }

        $shop = Setting::pluck('value', 'key')->all();
        $docNumber = $sale->transaction_number;
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'invoice', $docNumber);

        $pdf = Pdf::loadView('reports.invoice_pdf', compact('sale', 'shop', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("INVOICE_{$sale->transaction_number}.pdf");
    }

    /**
     * Halaman Laporan Keuangan (Arus Kas, Omset, Pemasukan Tunai & QRIS Bersih - Vue 3 Inertia)
     */
    public function financialReport(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $allMatchingSales = (clone $query)->get();

        $cashIncome = (float) $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount');
        $qrisGross = (float) $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount');
        $qrisFee = round($qrisGross * 0.007, 0);
        $qrisNet = $qrisGross - $qrisFee;
        $totalNetIncome = $cashIncome + $qrisNet;

        $stats = [
            'total_income'       => $totalNetIncome,
            'cash_income'        => $cashIncome,
            'qris_gross'         => $qrisGross,
            'qris_fee'           => $qrisFee,
            'qris_income'        => $qrisNet,
            'qris_net'           => $qrisNet,
            'pending_income'     => (float) $allMatchingSales->where('payment_status', 'pending')->sum('total_amount'),
            'total_transactions' => (int) $allMatchingSales->where('payment_status', 'success')->count(),
            'cash_count'         => (int) $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->count(),
            'qris_count'         => (int) $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->count(),
            'pending_count'      => (int) $allMatchingSales->where('payment_status', 'pending')->count(),
        ];

        $cashTransactions = (clone $query)->where('payment_method', 'cash')->orderBy('created_at', 'desc')->get();
        $qrisTransactions = (clone $query)->where('payment_method', 'qris')->orderBy('created_at', 'desc')->get();

        $chartData = Sale::where('payment_status', 'success')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END) as cash_total"),
                DB::raw("SUM(CASE WHEN payment_method = 'qris' THEN ROUND(total_amount * 0.993) ELSE 0 END) as qris_total"),
                DB::raw("SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE ROUND(total_amount * 0.993) END) as total")
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Reports/Finance', [
            'transactions'     => $transactions,
            'cashTransactions' => $cashTransactions,
            'qrisTransactions' => $qrisTransactions,
            'stats'            => $stats,
            'chartData'        => $chartData,
            'periodLabel'      => $periodLabel,
            'filters'          => $filters,
        ]);
    }

    /**
     * Export PDF Laporan Keuangan (DomPDF Landscape dengan TTE QR)
     */
    public function exportFinancePdf(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalCash = $transactions->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisGross = $transactions->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisFee = round($totalQrisGross * 0.007, 0);
        $totalQrisNet = $totalQrisGross - $totalQrisFee;
        $totalNominal = $totalCash + $totalQrisNet;

        $targetDate = null;
        if (!empty($filters['date'])) {
            $targetDate = $filters['date'];
        } elseif (!empty($filters['month'])) {
            $targetDate = Carbon::parse($filters['month'])->startOfMonth();
        } elseif (!empty($filters['year'])) {
            $targetDate = Carbon::createFromDate($filters['year'], 1, 1);
        } elseif ($transactions->isNotEmpty()) {
            $targetDate = $transactions->first()->created_at;
        } else {
            $targetDate = now();
        }

        $method = strtolower($filters['payment_method'] ?? 'all');
        $subType = ($method === 'cash') ? 'TUNAI' : (($method === 'qris') ? 'QRIS' : 'KAS');
        $docNumber = $this->generateDocNumber('LKEU', $subType, $shop, $targetDate);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'finance', $docNumber);

        $pdf = Pdf::loadView('reports.finance_pdf', compact('transactions', 'periodLabel', 'filters', 'shop', 'totalNominal', 'totalCash', 'totalQrisGross', 'totalQrisFee', 'totalQrisNet', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Keuangan_'.date('Ymd_His').'.pdf');
    }

    /**
     * Export Excel Laporan Keuangan
     */
    public function exportFinanceExcel(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalCash = $transactions->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisGross = $transactions->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisFee = round($totalQrisGross * 0.007, 0);
        $totalQrisNet = $totalQrisGross - $totalQrisFee;
        $totalNominal = $totalCash + $totalQrisNet;

        return Excel::download(new class($transactions, $periodLabel, $filters, $shop, $totalNominal, $totalCash, $totalQrisGross, $totalQrisFee, $totalQrisNet) implements FromView, ShouldAutoSize {
            protected $transactions, $periodLabel, $filters, $shop, $totalNominal, $totalCash, $totalQrisGross, $totalQrisFee, $totalQrisNet;

            public function __construct($transactions, $periodLabel, $filters, $shop, $totalNominal, $totalCash, $totalQrisGross, $totalQrisFee, $totalQrisNet)
            {
                $this->transactions    = $transactions;
                $this->periodLabel     = $periodLabel;
                $this->filters         = $filters;
                $this->shop            = $shop;
                $this->totalNominal    = $totalNominal;
                $this->totalCash       = $totalCash;
                $this->totalQrisGross  = $totalQrisGross;
                $this->totalQrisFee    = $totalQrisFee;
                $this->totalQrisNet    = $totalQrisNet;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('reports.finance_excel', [
                    'transactions'   => $this->transactions,
                    'periodLabel'    => $this->periodLabel,
                    'filters'        => $this->filters,
                    'shop'           => $this->shop,
                    'totalNominal'   => $this->totalNominal,
                    'totalCash'      => $this->totalCash,
                    'totalQrisGross' => $this->totalQrisGross,
                    'totalQrisFee'   => $this->totalQrisFee,
                    'totalQrisNet'   => $this->totalQrisNet,
                ]);
            }
        }, 'Laporan_Keuangan_'.date('Ymd_His').'.xlsx');
    }

    /**
     * Halaman Laporan Monitoring QRIS DOKU (Vue 3 Inertia)
     */
    public function qrisReport(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $query->where('payment_method', 'qris');
        $allQris = (clone $query)->get();

        $totalGross = (float) $allQris->where('payment_status', 'success')->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        $stats = [
            'total_qris_net'     => $totalNet,
            'total_qris_gross'   => $totalGross,
            'total_qris_fee'     => $totalFee,
            'total_transactions' => (int) $allQris->where('payment_status', 'success')->count(),
            'pending_count'      => (int) $allQris->where('payment_status', 'pending')->count(),
            'pending_amount'     => (float) $allQris->where('payment_status', 'pending')->sum('total_amount'),
        ];

        $chartData = Sale::where('payment_method', 'qris')
            ->where('payment_status', 'success')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as gross_total'),
                DB::raw('ROUND(SUM(total_amount) * 0.993) as net_total')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Reports/Qris', [
            'transactions' => $transactions,
            'stats'        => $stats,
            'chartData'    => $chartData,
            'periodLabel'  => $periodLabel,
            'filters'      => $filters,
        ]);
    }

    /**
     * Export PDF Laporan QRIS (DomPDF)
     */
    public function exportQrisPdf(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $query->where('payment_method', 'qris');
        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalGross = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        $targetDate = !empty($filters['date']) ? $filters['date'] : now();
        $docNumber = $this->generateDocNumber('LQRS', 'DOKU', $shop, $targetDate);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'qris', $docNumber);

        $pdf = Pdf::loadView('reports.qris_pdf', compact('transactions', 'periodLabel', 'filters', 'shop', 'totalGross', 'totalFee', 'totalNet', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_QRIS_'.date('Ymd_His').'.pdf');
    }

    /**
     * Export Excel Laporan QRIS
     */
    public function exportQrisExcel(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $query->where('payment_method', 'qris');
        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalGross = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        return Excel::download(new class($transactions, $totalGross, $totalFee, $totalNet) implements FromView, ShouldAutoSize {
            protected $transactions, $totalGross, $totalFee, $totalNet;

            public function __construct($transactions, $totalGross, $totalFee, $totalNet)
            {
                $this->transactions = $transactions;
                $this->totalGross   = $totalGross;
                $this->totalFee     = $totalFee;
                $this->totalNet     = $totalNet;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('reports.qris_excel', [
                    'transactions' => $this->transactions,
                    'totalGross'   => $this->totalGross,
                    'totalFee'     => $this->totalFee,
                    'totalNet'     => $this->totalNet,
                ]);
            }
        }, 'Laporan_QRIS_'.date('Ymd_His').'.xlsx');
    }

    /**
     * Halaman Laporan Stok & Inventaris (Vue 3 Inertia)
     */
    public function stockReport(Request $request)
    {
        $stockStatus = $request->input('stock_status', 'all');
        $sortBy = $request->input('sort_by', 'name_asc');
        $search = $request->input('search');

        $query = Product::query();

        if ($stockStatus === 'available') {
            $query->where('stock', '>', 10);
            $statusLabel = 'Stok Tersedia (> 10 pcs)';
        } elseif ($stockStatus === 'low') {
            $query->whereBetween('stock', [1, 10]);
            $statusLabel = 'Stok Menipis (1 - 10 pcs)';
        } elseif ($stockStatus === 'empty') {
            $query->where('stock', '<=', 0);
            $statusLabel = 'Stok Habis (0 pcs)';
        } else {
            $statusLabel = 'Semua Kondisi Stok';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $allProducts = Product::all();
        $totalValuation = $allProducts->sum(fn($p) => (float)$p->price * (int)$p->stock);
        $totalPhysicalStock = $allProducts->sum('stock');
        $lowStockCount = $allProducts->where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $emptyStockCount = $allProducts->where('stock', '<=', 0)->count();

        $stats = [
            'total_valuation'      => $totalValuation,
            'total_products_count' => $allProducts->count(),
            'total_physical_stock' => $totalPhysicalStock,
            'low_stock_count'      => $lowStockCount,
            'empty_stock_count'    => $emptyStockCount,
        ];

        $products = $query->paginate(15)->withQueryString();

        $filters = [
            'stock_status' => $stockStatus,
            'sort_by'      => $sortBy,
            'search'       => $search,
        ];

        return Inertia::render('Admin/Reports/Stock', [
            'products'    => $products,
            'stats'       => $stats,
            'statusLabel' => $statusLabel,
            'filters'     => $filters,
        ]);
    }

    /**
     * Export PDF Laporan Stok (DomPDF Landscape)
     */
    public function exportStockPdf(Request $request)
    {
        $stockStatus = $request->input('stock_status', 'all');
        $sortBy = $request->input('sort_by', 'name_asc');
        $search = $request->input('search');

        $query = Product::query();

        if ($stockStatus === 'available') {
            $query->where('stock', '>', 10);
            $statusLabel = 'Stok Tersedia (> 10 pcs)';
        } elseif ($stockStatus === 'low') {
            $query->whereBetween('stock', [1, 10]);
            $statusLabel = 'Stok Menipis (1 - 10 pcs)';
        } elseif ($stockStatus === 'empty') {
            $query->where('stock', '<=', 0);
            $statusLabel = 'Stok Habis (0 pcs)';
        } else {
            $statusLabel = 'Semua Kondisi Stok';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'stock_desc': $query->orderBy('stock', 'desc'); break;
            case 'stock_asc':  $query->orderBy('stock', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'latest':     $query->orderBy('created_at', 'desc'); break;
            case 'name_asc':
            default:           $query->orderBy('name', 'asc'); break;
        }

        $products = $query->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalValuation = $products->sum(fn($p) => (float)$p->price * (int)$p->stock);
        $totalPhysicalStock = $products->sum('stock');

        $docNumber = $this->generateDocNumber('LSTK', strtoupper($stockStatus), $shop, now());
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'stock', $docNumber);

        $pdf = Pdf::loadView('reports.stock_pdf', compact('products', 'statusLabel', 'shop', 'totalValuation', 'totalPhysicalStock', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Stok_'.date('Ymd_His').'.pdf');
    }

    /**
     * Export Excel Laporan Stok
     */
    public function exportStockExcel(Request $request)
    {
        $stockStatus = $request->input('stock_status', 'all');
        $sortBy = $request->input('sort_by', 'name_asc');
        $search = $request->input('search');

        $query = Product::query();

        if ($stockStatus === 'available') {
            $query->where('stock', '>', 10);
            $statusLabel = 'Stok Tersedia (> 10 pcs)';
        } elseif ($stockStatus === 'low') {
            $query->whereBetween('stock', [1, 10]);
            $statusLabel = 'Stok Menipis (1 - 10 pcs)';
        } elseif ($stockStatus === 'empty') {
            $query->where('stock', '<=', 0);
            $statusLabel = 'Stok Habis (0 pcs)';
        } else {
            $statusLabel = 'Semua Kondisi Stok';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'stock_desc': $query->orderBy('stock', 'desc'); break;
            case 'stock_asc':  $query->orderBy('stock', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'latest':     $query->orderBy('created_at', 'desc'); break;
            case 'name_asc':
            default:           $query->orderBy('name', 'asc'); break;
        }

        $products = $query->get();
        $shop = Setting::pluck('value', 'key')->all();
        $totalValuation = $products->sum(fn($p) => (float)$p->price * (int)$p->stock);
        $totalPhysicalStock = $products->sum('stock');

        return Excel::download(new class($products, $statusLabel, $shop, $totalValuation, $totalPhysicalStock) implements FromView, ShouldAutoSize {
            protected $products, $statusLabel, $shop, $totalValuation, $totalPhysicalStock;

            public function __construct($products, $statusLabel, $shop, $totalValuation, $totalPhysicalStock)
            {
                $this->products           = $products;
                $this->statusLabel        = $statusLabel;
                $this->shop               = $shop;
                $this->totalValuation     = $totalValuation;
                $this->totalPhysicalStock = $totalPhysicalStock;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('reports.stock_excel', [
                    'products'           => $this->products,
                    'statusLabel'        => $this->statusLabel,
                    'shop'               => $this->shop,
                    'totalValuation'     => $this->totalValuation,
                    'totalPhysicalStock' => $this->totalPhysicalStock,
                ]);
            }
        }, 'Laporan_Stok_'.date('Ymd_His').'.xlsx');
    }
}