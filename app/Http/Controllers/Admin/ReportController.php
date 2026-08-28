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
     * Halaman Utama Laporan Penjualan (Pusat Laporan)
     */
    public function index(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        // Menghitung Statistik Ringkasan
        $allMatchingSales = (clone $query)->get();

        $stats = [
            'total_revenue'      => $allMatchingSales->where('payment_status', 'success')->sum('total_amount'),
            'total_transactions' => $allMatchingSales->count(),
            'total_items_sold'   => $allMatchingSales->sum(fn($s) => $s->details->sum('quantity')),
            'cash_revenue'       => $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount'),
            'qris_revenue'       => $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount'),
            'pending_count'      => $allMatchingSales->where('payment_status', 'pending')->count(),
        ];

        // Transaksi dengan pagination untuk web
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return \Inertia\Inertia::render('Admin/Reports/Index', [
            'sales'              => $transactions,
            'total_revenue'      => (float) $stats['total_revenue'],
            'total_sales_count'  => (int) $stats['total_transactions'],
            'total_items_sold'   => (int) $stats['total_items_sold'],
            'filters'            => $filters,
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
     * [KODE]-[METODE]/[TANGGAL]/[BULAN_ROMAWI]/[NAMA_APLIKASI]/[TAHUN]
     * Tanggal otomatis mengikuti tanggal data laporan yang difilter (bukan tanggal saat diklik cetak).
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
        $appName = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($shop['app_name'] ?? 'SIKANDA')) ?: 'SIKANDA';
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
     * Export Laporan Penjualan ke PDF (Landscape A4 dengan TTE QR)
     */
    public function exportPdf(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalNominal = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalQty = $transactions->sum(fn($s) => $s->details->sum('quantity'));

        $targetDate = !empty($filters['date']) ? $filters['date'] : ($transactions->first()->created_at ?? now());
        $docNumber = $this->generateDocNumber('LPK', 'JUAL', $shop, $targetDate);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'sales', $docNumber);

        $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'periodLabel', 'filters', 'shop', 'totalNominal', 'totalQty', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Penjualan_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export Laporan Penjualan ke Excel
     */
    public function exportExcel(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $totalNominal = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalQty = $transactions->sum(fn($s) => $s->details->sum('quantity'));

        return Excel::download(new class($transactions, $periodLabel, $totalNominal, $totalQty) implements FromView, ShouldAutoSize {
            private $transactions;
            private $periodLabel;
            private $totalNominal;
            private $totalQty;

            public function __construct($transactions, $periodLabel, $totalNominal, $totalQty) {
                $this->transactions = $transactions;
                $this->periodLabel = $periodLabel;
                $this->totalNominal = $totalNominal;
                $this->totalQty = $totalQty;
            }

            public function view(): \Illuminate\Contracts\View\View {
                return view('reports.excel', [
                    'transactions' => $this->transactions,
                    'periodLabel'  => $this->periodLabel,
                    'totalNominal' => $this->totalNominal,
                    'totalQty'     => $this->totalQty,
                ]);
            }
        }, 'Laporan_Penjualan_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Cetak Struk Ringkasan Harian
     */
    public function printDailyReport(Request $request)
    {
        $today = now()->format('Y-m-d');
        $sales = Sale::whereDate('created_at', $today)
                     ->with(['details.product', 'user'])
                     ->get();

        $totalRevenue = $sales->where('payment_status', 'success')->sum('total_amount');
        $reportDate = now()->translatedFormat('l, d F Y');

        // Ringkasan item
        $summaryItems = [];
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $pId = $detail->product_id;
                $pName = $detail->product->name ?? 'Produk Dihapus';
                if (!isset($summaryItems[$pId])) {
                    $summaryItems[$pId] = [
                        'name' => $pName,
                        'quantity' => 0,
                        'total_price' => 0,
                    ];
                }
                $summaryItems[$pId]['quantity'] += $detail->quantity;
                $summaryItems[$pId]['total_price'] += ($detail->price_at_transaction * $detail->quantity);
            }
        }

        return view('admin.reports.print-daily', [
            'summaryItems' => array_values($summaryItems),
            'totalRevenue' => $totalRevenue,
            'reportDate'   => $reportDate,
        ]);
    }

    /**
     * Halaman Laporan Monitoring QRIS
     */
    public function qrisReport(Request $request)
    {
        $search = $request->get('search');

        $todaySales = Sale::where('payment_method', 'qris')->where('payment_status', 'success')->whereDate('created_at', Carbon::today())->get();
        $todayGross = $todaySales->sum('total_amount');
        $todayFee = round($todayGross * 0.007, 0);
        $todayNet = $todayGross - $todayFee;

        $totalSales = Sale::where('payment_method', 'qris')->where('payment_status', 'success')->get();
        $totalGross = $totalSales->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        $stats = [
            'today_gross'   => $todayGross,
            'today_fee'     => $todayFee,
            'today_revenue' => $todayNet,
            'total_gross'   => $totalGross,
            'total_fee'     => $totalFee,
            'total_success' => $totalNet,
            'pending_count' => Sale::where('payment_method', 'qris')->where('payment_status', 'pending')->count(),
        ];

        $chartData = Sale::where('payment_method', 'qris')
            ->where('payment_status', 'success')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(ROUND(total_amount * 0.993)) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $transactions = Sale::where('payment_method', 'qris')
            ->with(['details.product', 'user'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('reports.qris', compact('stats', 'chartData', 'transactions', 'search'));
    }

    /**
     * Export PDF Khusus Kanal QRIS (Dipotong Biaya DOKU 0.7%)
     */
    public function exportQrisPdf(Request $request)
    {
        $search = $request->get('search');

        $transactions = Sale::where('payment_method', 'qris')
            ->with(['details.product', 'user'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $shop = Setting::pluck('value', 'key')->all();

        $totalGross = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        $targetDate = $request->input('date') ?: ($transactions->first()->created_at ?? now());
        $docNumber = $this->generateDocNumber('LKEU', 'QRIS', $shop, $targetDate);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'qris', $docNumber);

        $pdf = Pdf::loadView('reports.qris_pdf', compact('transactions', 'totalGross', 'totalFee', 'totalNet', 'shop', 'signerName', 'signerTitle', 'verifyUrl', 'tteQrBase64', 'docNumber'))->setPaper('a4', 'landscape');
        return $pdf->download('LPK_QRIS_'.date('Ymd_His').'.pdf');
    }

    /**
     * Export Excel Khusus Kanal QRIS (Rincian Biaya DOKU 0.7%)
     */
    public function exportQrisExcel(Request $request)
    {
        $search = $request->get('search');

        $transactions = Sale::where('payment_method', 'qris')
            ->with(['details.product', 'user'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalGross = $transactions->where('payment_status', 'success')->sum('total_amount');
        $totalFee = round($totalGross * 0.007, 0);
        $totalNet = $totalGross - $totalFee;

        return Excel::download(new class($transactions, $totalGross, $totalFee, $totalNet) implements FromView, ShouldAutoSize {
            private $transactions;
            private $totalGross;
            private $totalFee;
            private $totalNet;

            public function __construct($transactions, $totalGross, $totalFee, $totalNet) {
                $this->transactions = $transactions;
                $this->totalGross = $totalGross;
                $this->totalFee = $totalFee;
                $this->totalNet = $totalNet;
            }

            public function view(): \Illuminate\Contracts\View\View {
                return view('reports.qris_excel', [
                    'transactions' => $this->transactions,
                    'totalGross'   => $this->totalGross,
                    'totalFee'     => $this->totalFee,
                    'totalNet'     => $this->totalNet,
                ]);
            }
        }, 'LPK_QRIS_'.date('Ymd').'.xlsx');
    }

    /**
     * Halaman Laporan Keuangan (Arus Kas, Omset, Pemasukan Tunai & QRIS Bersih)
     */
    public function financialReport(Request $request)
    {
        [$query, $periodLabel, $filters] = $this->buildSalesReportQuery($request);

        $allMatchingSales = (clone $query)->get();

        $cashIncome = $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount');
        $qrisGross = $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount');
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
            'pending_income'     => $allMatchingSales->where('payment_status', 'pending')->sum('total_amount'),
            'total_transactions' => $allMatchingSales->where('payment_status', 'success')->count(),
            'cash_count'         => $allMatchingSales->where('payment_method', 'cash')->where('payment_status', 'success')->count(),
            'qris_count'         => $allMatchingSales->where('payment_method', 'qris')->where('payment_status', 'success')->count(),
            'pending_count'      => $allMatchingSales->where('payment_status', 'pending')->count(),
        ];

        // Pisahkan data transaksi tunai dan qris untuk tab instan
        $cashTransactions = (clone $query)->where('payment_method', 'cash')->orderBy('created_at', 'desc')->get();
        $qrisTransactions = (clone $query)->where('payment_method', 'qris')->orderBy('created_at', 'desc')->get();

        // Chart tren arus kas 7 hari terakhir (QRIS setelah dipotong 0.7%)
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

        return view('reports.finance', compact('transactions', 'cashTransactions', 'qrisTransactions', 'stats', 'chartData', 'periodLabel', 'filters'));
    }

    /**
     * Export PDF Laporan Keuangan (Landscape Surat Resmi dengan TTE QR)
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
        $totalCash = $transactions->where('payment_method', 'cash')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisGross = $transactions->where('payment_method', 'qris')->where('payment_status', 'success')->sum('total_amount');
        $totalQrisFee = round($totalQrisGross * 0.007, 0);
        $totalQrisNet = $totalQrisGross - $totalQrisFee;
        $totalNominal = $totalCash + $totalQrisNet;

        return Excel::download(new class($transactions, $periodLabel, $totalNominal, $totalCash, $totalQrisGross, $totalQrisFee, $totalQrisNet) implements FromView, ShouldAutoSize {
            private $transactions;
            private $periodLabel;
            private $totalNominal;
            private $totalCash;
            private $totalQrisGross;
            private $totalQrisFee;
            private $totalQrisNet;

            public function __construct($transactions, $periodLabel, $totalNominal, $totalCash, $totalQrisGross, $totalQrisFee, $totalQrisNet) {
                $this->transactions = $transactions;
                $this->periodLabel = $periodLabel;
                $this->totalNominal = $totalNominal;
                $this->totalCash = $totalCash;
                $this->totalQrisGross = $totalQrisGross;
                $this->totalQrisFee = $totalQrisFee;
                $this->totalQrisNet = $totalQrisNet;
            }

            public function view(): \Illuminate\Contracts\View\View {
                return view('reports.finance_excel', [
                    'transactions'   => $this->transactions,
                    'periodLabel'    => $this->periodLabel,
                    'totalNominal'   => $this->totalNominal,
                    'totalCash'      => $this->totalCash,
                    'totalQrisGross' => $this->totalQrisGross,
                    'totalQrisFee'   => $this->totalQrisFee,
                    'totalQrisNet'   => $this->totalQrisNet,
                ]);
            }
        }, 'Laporan_Keuangan_'.date('Ymd').'.xlsx');
    }

    /**
     * Cetak Faktur / Nota Transaksi Spesifik ke PDF (Format Surat Resmi dengan TTE QR)
     */
    public function exportInvoicePdf(Sale $sale)
    {
        $sale->load(['details.product', 'user']);
        $shop = Setting::pluck('value', 'key')->all();

        $user = Auth::user();
        $signerName = $user ? $user->name : ($sale->user->name ?? ($shop['cashier_officer_name'] ?? 'Petugas Kasir'));
        $targetUser = $user ?: $sale->user;
        $signerTitle = ($targetUser && !empty($targetUser->alias))
            ? $targetUser->alias
            : ($targetUser ? ($targetUser->role === 'admin' ? ($shop['cashier_officer_title'] ?? 'Administrator Toko') : 'Petugas Kasir') : ($shop['cashier_officer_title'] ?? 'Petugas Kasir'));

        $verifyUrl = route('verify.tte', ['transaction_number' => $sale->transaction_number]);
        $tteQrBase64 = QRCodeService::generateBase64($verifyUrl, 160);

        $pdf = Pdf::loadView('reports.invoice_pdf', compact('sale', 'shop', 'tteQrBase64', 'signerTitle', 'signerName', 'verifyUrl'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Faktur_' . $sale->transaction_number . '.pdf');
    }

    /**
     * Cetak Faktur PDF Publik via Nomor Invoice (Direct fallback)
     */
    public function publicInvoicePdfByNumber(Request $request, $transactionNumber)
    {
        return $this->downloadSignedInvoice($request, $transactionNumber);
    }

    /**
     * Generate Tautan Unduh Faktur Bertanda Tangan Digital (Kadaluarsa dalam 24 Jam)
     */
    public function getSignedInvoiceLink($transactionNumber)
    {
        $sale = Sale::where('transaction_number', $transactionNumber)->firstOrFail();
        
        $signedUrl = URL::temporarySignedRoute(
            'invoice.public.signed',
            now()->addHours(24),
            ['transaction_number' => $sale->transaction_number]
        );

        return response()->json([
            'success'    => true,
            'signed_url' => $signedUrl,
            'expires_at' => now()->addHours(24)->toIso8601String()
        ]);
    }

    /**
     * Unduh / Tampilkan Faktur PDF Pelanggan dengan Validasi Kadaluarsa 24 Jam & TTE QR
     */
    public function downloadSignedInvoice(Request $request, $transactionNumber)
    {
        $sale = Sale::with(['details.product', 'user'])
                    ->where('transaction_number', $transactionNumber)
                    ->firstOrFail();
        
        $shop = Setting::pluck('value', 'key')->all();

        // Jika diakses oleh Admin atau Kasir yang sedang login, izinkan selalu
        if (!Auth::check()) {
            // Jika diakses oleh pelanggan publik dari WhatsApp, wajib signature valid & belum lewat 24 jam
            if (!$request->hasValidSignature()) {
                return response()->view('reports.invoice_expired', compact('sale', 'shop'), 403);
            }
        }

        $user = Auth::user();
        $signerName = $user ? $user->name : ($sale->user->name ?? ($shop['cashier_officer_name'] ?? 'Petugas Kasir'));
        $targetUser = $user ?: $sale->user;
        $signerTitle = ($targetUser && !empty($targetUser->alias))
            ? $targetUser->alias
            : ($targetUser ? ($targetUser->role === 'admin' ? ($shop['cashier_officer_title'] ?? 'Administrator Toko') : 'Petugas Kasir') : ($shop['cashier_officer_title'] ?? 'Petugas Kasir'));

        $verifyUrl = route('verify.tte', ['transaction_number' => $sale->transaction_number]);
        $tteQrBase64 = QRCodeService::generateBase64($verifyUrl, 160);

        $pdf = Pdf::loadView('reports.invoice_pdf', compact('sale', 'shop', 'tteQrBase64', 'signerTitle', 'signerName', 'verifyUrl'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Faktur_' . $sale->transaction_number . '.pdf');
    }

    /**
     * Helper Query untuk Laporan Stok & Inventaris Produk
     */
    private function buildStockReportQuery(Request $request)
    {
        $search = $request->input('search');
        $stockStatus = $request->input('stock_status', 'all');
        $sortBy = $request->input('sort_by', 'name_asc');

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        switch ($stockStatus) {
            case 'low':
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
                $statusLabel = 'Stok Menipis (<= 10 pcs)';
                break;
            case 'empty':
                $query->where('stock', '<=', 0);
                $statusLabel = 'Stok Habis (0 pcs)';
                break;
            case 'available':
                $query->where('stock', '>', 10);
                $statusLabel = 'Stok Tersedia (> 10 pcs)';
                break;
            default:
                $statusLabel = 'Semua Stok Barang';
                break;
        }

        switch ($sortBy) {
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $filters = [
            'search'       => $search,
            'stock_status' => $stockStatus,
            'sort_by'      => $sortBy,
        ];

        return [$query, $statusLabel, $filters];
    }

    /**
     * Halaman Utama Laporan Stok & Inventaris Barang
     */
    public function stockReport(Request $request)
    {
        [$query, $statusLabel, $filters] = $this->buildStockReportQuery($request);

        $allProducts = Product::all();
        $stats = [
            'total_products'    => $allProducts->count(),
            'total_stock'       => $allProducts->sum('stock'),
            'total_valuation'   => $allProducts->sum(fn($p) => $p->stock * $p->price),
            'low_stock_count'   => $allProducts->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'empty_stock_count' => $allProducts->where('stock', '<=', 0)->count(),
        ];

        $products = (clone $query)->paginate(15)->withQueryString();

        return view('reports.stock', compact('products', 'stats', 'statusLabel', 'filters'));
    }

    /**
     * Export Laporan Stok Barang ke PDF (Landscape A4 dengan format kop resmi yang sama)
     */
    public function exportStockPdf(Request $request)
    {
        [$query, $statusLabel, $filters] = $this->buildStockReportQuery($request);

        $products = $query->get();
        $shop = Setting::pluck('value', 'key')->all();

        $totalProducts = $products->count();
        $totalStock = $products->sum('stock');
        $totalValuation = $products->sum(fn($p) => $p->stock * $p->price);

        $docNumber = $this->generateDocNumber('LSTK', 'STOK', $shop);
        [$signerName, $signerTitle, $verifyUrl, $tteQrBase64] = $this->getSignerData($shop, 'stock', $docNumber);

        $pdf = Pdf::loadView('reports.stock_pdf', compact(
            'products',
            'statusLabel',
            'filters',
            'shop',
            'totalProducts',
            'totalStock',
            'totalValuation',
            'signerName',
            'signerTitle',
            'verifyUrl',
            'tteQrBase64',
            'docNumber'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Stok_Barang_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export Laporan Stok Barang ke Excel
     */
    public function exportStockExcel(Request $request)
    {
        [$query, $statusLabel, $filters] = $this->buildStockReportQuery($request);

        $products = $query->get();
        $totalProducts = $products->count();
        $totalStock = $products->sum('stock');
        $totalValuation = $products->sum(fn($p) => $p->stock * $p->price);

        return Excel::download(new class($products, $statusLabel, $totalProducts, $totalStock, $totalValuation) implements FromView, ShouldAutoSize {
            private $products;
            private $statusLabel;
            private $totalProducts;
            private $totalStock;
            private $totalValuation;

            public function __construct($products, $statusLabel, $totalProducts, $totalStock, $totalValuation) {
                $this->products = $products;
                $this->statusLabel = $statusLabel;
                $this->totalProducts = $totalProducts;
                $this->totalStock = $totalStock;
                $this->totalValuation = $totalValuation;
            }

            public function view(): \Illuminate\Contracts\View\View {
                return view('reports.stock_excel', [
                    'products'       => $this->products,
                    'statusLabel'    => $this->statusLabel,
                    'totalProducts'  => $this->totalProducts,
                    'totalStock'     => $this->totalStock,
                    'totalValuation' => $this->totalValuation,
                ]);
            }
        }, 'Laporan_Stok_Barang_' . date('Ymd_His') . '.xlsx');
    }
}