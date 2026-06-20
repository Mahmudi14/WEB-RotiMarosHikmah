<?php

namespace App\Services\Admin;

use App\Models\CashierExpense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IncomeAnalysisService
{
    public function getCashiers(): Collection
    {
        return User::query()
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function chartViews(): array
    {
        return [
            'daily' => 'Per Hari',
            'monthly' => 'Per Bulan',
            'yearly' => 'Per Tahun',
            'overall' => 'Keseluruhan',
        ];
    }

    public function chartMetrics(): array
    {
        return [
            'net_income' => 'Pendapatan Bersih',
            'expenses' => 'Pengeluaran',
            'gross_sales' => 'Pendapatan Kotor',
        ];
    }

    public function months(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    public function years(): Collection
    {
        $saleYears = Sale::query()
            ->selectRaw('YEAR(created_at) as tahun')
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('tahun');

        $expenseYears = CashierExpense::query()
            ->selectRaw('YEAR(tanggal_pengeluaran) as tahun')
            ->whereNotNull('tanggal_pengeluaran')
            ->distinct()
            ->pluck('tahun');

        return $saleYears
            ->merge($expenseYears)
            ->push(now()->year)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();
    }

    public function getFilterState(Request $request): array
    {
        $chartView = in_array($request->chart_view, array_keys($this->chartViews()), true)
            ? $request->chart_view
            : 'monthly';

        $chartMetric = in_array($request->chart_metric, array_keys($this->chartMetrics()), true)
            ? $request->chart_metric
            : 'net_income';

        $year = $this->normalizeYear($request->year);
        $month = $this->normalizeMonth($request->month);

        if ($chartView === 'daily') {
            $year ??= now()->year;
            $month ??= now()->month;
        }

        if ($chartView === 'monthly') {
            $year ??= now()->year;
            $month = null;
        }

        if ($chartView === 'yearly') {
            $year = null;
            $month = null;
        }

        if ($chartView === 'overall') {
            if ($month && ! $year) {
                $year = now()->year;
            }
        }

        return [
            'cashier_id' => $request->filled('cashier_id') ? (int) $request->cashier_id : null,
            'chart_view' => $chartView,
            'chart_metric' => $chartMetric,
            'year' => $year,
            'month' => $month,
        ];
    }

    public function getScopeLabel(Request $request): string
    {
        $filters = $this->getFilterState($request);

        if ($filters['chart_view'] === 'daily') {
            return $this->monthName($filters['month']) . ' ' . $filters['year'];
        }

        if ($filters['chart_view'] === 'monthly') {
            return 'Tahun ' . $filters['year'];
        }

        if ($filters['chart_view'] === 'yearly') {
            return 'Semua Tahun';
        }

        if ($filters['chart_view'] === 'overall') {
            if ($filters['year'] && $filters['month']) {
                return $this->monthName($filters['month']) . ' ' . $filters['year'];
            }

            if ($filters['year']) {
                return 'Tahun ' . $filters['year'];
            }

            return 'Keseluruhan';
        }

        return 'Keseluruhan';
    }

    public function getDateScope(Request $request): array
    {
        $filters = $this->getFilterState($request);

        if ($filters['chart_view'] === 'daily') {
            $startDate = Carbon::create($filters['year'], $filters['month'], 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth()->endOfDay();

            return [$startDate, $endDate];
        }

        if ($filters['chart_view'] === 'monthly') {
            return [
                Carbon::create($filters['year'], 1, 1)->startOfDay(),
                Carbon::create($filters['year'], 12, 31)->endOfDay(),
            ];
        }

        if ($filters['chart_view'] === 'overall') {
            if ($filters['year'] && $filters['month']) {
                $startDate = Carbon::create($filters['year'], $filters['month'], 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();

                return [$startDate, $endDate];
            }

            if ($filters['year']) {
                return [
                    Carbon::create($filters['year'], 1, 1)->startOfDay(),
                    Carbon::create($filters['year'], 12, 31)->endOfDay(),
                ];
            }
        }

        return [null, null];
    }

    public function getSummary(Request $request): array
    {
        $salesQuery = $this->salesQuery($request);
        $expenseQuery = $this->expenseQuery($request);

        $grossSales = (float) (clone $salesQuery)->sum('grand_total');
        $expenses = (float) (clone $expenseQuery)->sum('nominal');
        $transactions = (clone $salesQuery)->count();

        return [
            'transactions' => $transactions,
            'gross_sales' => $grossSales,
            'expenses' => $expenses,
            'net_income' => $grossSales - $expenses,
            'average_transaction' => $transactions > 0 ? $grossSales / $transactions : 0,

            'cash_sales' => (float) (clone $salesQuery)->where('payment_method', 'tunai')->sum('grand_total'),
            'qris_sales' => (float) (clone $salesQuery)->where('payment_method', 'qris')->sum('grand_total'),
            'transfer_sales' => (float) (clone $salesQuery)->where('payment_method', 'transfer')->sum('grand_total'),

            'discounts' => (float) (clone $salesQuery)->sum('total_diskon'),
            'taxes' => (float) (clone $salesQuery)->sum('total_pajak'),
        ];
    }

    public function getChartData(Request $request): Collection
    {
        $filters = $this->getFilterState($request);

        if ($filters['chart_view'] === 'overall') {
            $salesQuery = $this->salesQuery($request);
            $expenseQuery = $this->expenseQuery($request);

            $totalPendapatan = (float) (clone $salesQuery)->sum('grand_total');
            $totalPengeluaran = (float) (clone $expenseQuery)->sum('nominal');

            return collect([
                (object) [
                    'periode' => 'overall',
                    'label' => $this->getScopeLabel($request),
                    'total_transaksi' => (clone $salesQuery)->count(),
                    'total_pendapatan' => $totalPendapatan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'pendapatan_bersih' => $totalPendapatan - $totalPengeluaran,
                    'cash_sales' => (float) (clone $salesQuery)->where('payment_method', 'tunai')->sum('grand_total'),
                    'qris_sales' => (float) (clone $salesQuery)->where('payment_method', 'qris')->sum('grand_total'),
                    'transfer_sales' => (float) (clone $salesQuery)->where('payment_method', 'transfer')->sum('grand_total'),
                ],
            ]);
        }

        $salesGroup = $this->salesGroupExpression($filters['chart_view']);
        $expenseGroup = $this->expenseGroupExpression($filters['chart_view']);

        $sales = $this->salesQuery($request)
            ->selectRaw("
                {$salesGroup} as periode,
                COUNT(*) as total_transaksi,
                SUM(grand_total) as total_pendapatan,
                SUM(CASE WHEN payment_method = 'tunai' THEN grand_total ELSE 0 END) as cash_sales,
                SUM(CASE WHEN payment_method = 'qris' THEN grand_total ELSE 0 END) as qris_sales,
                SUM(CASE WHEN payment_method = 'transfer' THEN grand_total ELSE 0 END) as transfer_sales
            ")
            ->groupByRaw($salesGroup)
            ->orderBy('periode')
            ->get()
            ->keyBy('periode');

        $expenses = $this->expenseQuery($request)
            ->selectRaw("
                {$expenseGroup} as periode,
                SUM(nominal) as total_pengeluaran
            ")
            ->groupByRaw($expenseGroup)
            ->orderBy('periode')
            ->get()
            ->keyBy('periode');

        $keys = $this->chartPeriodKeys($request, $sales, $expenses);

        return $keys->map(function ($periodKey) use ($sales, $expenses, $filters) {
            $sale = $sales->get($periodKey);
            $expense = $expenses->get($periodKey);

            $totalPendapatan = (float) ($sale->total_pendapatan ?? 0);
            $totalPengeluaran = (float) ($expense->total_pengeluaran ?? 0);

            return (object) [
                'periode' => (string) $periodKey,
                'label' => $this->formatPeriodLabel((string) $periodKey, $filters['chart_view']),
                'total_transaksi' => (int) ($sale->total_transaksi ?? 0),
                'total_pendapatan' => $totalPendapatan,
                'total_pengeluaran' => $totalPengeluaran,
                'pendapatan_bersih' => $totalPendapatan - $totalPengeluaran,
                'cash_sales' => (float) ($sale->cash_sales ?? 0),
                'qris_sales' => (float) ($sale->qris_sales ?? 0),
                'transfer_sales' => (float) ($sale->transfer_sales ?? 0),
            ];
        });
    }

    public function getTopProducts(Request $request, int $limit = 10): Collection
    {
        [$startDate, $endDate] = $this->getDateScope($request);
        $filters = $this->getFilterState($request);

        return SaleItem::query()
            ->selectRaw('
                sale_items.nama_produk,
                sale_items.nama_kategori,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_subtotal
            ')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'selesai')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->when($filters['cashier_id'], function ($query) use ($filters) {
                $query->where('sales.cashier_id', $filters['cashier_id']);
            })
            ->groupBy('sale_items.nama_produk', 'sale_items.nama_kategori')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function getCashierPerformance(Request $request): Collection
    {
        [$startDate, $endDate] = $this->getDateScope($request);
        $filters = $this->getFilterState($request);

        return Sale::query()
            ->selectRaw('
                sales.cashier_id,
                users.name as cashier_name,
                COUNT(sales.id) as total_transaksi,
                SUM(sales.grand_total) as total_pendapatan,
                SUM(CASE WHEN sales.payment_method = "tunai" THEN sales.grand_total ELSE 0 END) as total_tunai,
                SUM(CASE WHEN sales.payment_method IN ("qris", "transfer") THEN sales.grand_total ELSE 0 END) as total_non_tunai
            ')
            ->join('users', 'users.id', '=', 'sales.cashier_id')
            ->where('sales.status', 'selesai')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->when($filters['cashier_id'], function ($query) use ($filters) {
                $query->where('sales.cashier_id', $filters['cashier_id']);
            })
            ->groupBy('sales.cashier_id', 'users.name')
            ->orderByDesc('total_pendapatan')
            ->get();
    }

    public function getExpenseBreakdown(Request $request): Collection
    {
        return $this->expenseQuery($request)
            ->selectRaw('
                kategori_pengeluaran,
                COUNT(*) as total_data,
                SUM(nominal) as total_nominal
            ')
            ->groupBy('kategori_pengeluaran')
            ->orderByDesc('total_nominal')
            ->get();
    }

    protected function salesQuery(Request $request): Builder
    {
        [$startDate, $endDate] = $this->getDateScope($request);
        $filters = $this->getFilterState($request);

        return Sale::query()
            ->where('status', 'selesai')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($filters['cashier_id'], function ($query) use ($filters) {
                $query->where('cashier_id', $filters['cashier_id']);
            });
    }

    protected function expenseQuery(Request $request): Builder
    {
        [$startDate, $endDate] = $this->getDateScope($request);
        $filters = $this->getFilterState($request);

        return CashierExpense::query()
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_pengeluaran', [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            })
            ->when($filters['cashier_id'], function ($query) use ($filters) {
                $query->where('cashier_id', $filters['cashier_id']);
            });
    }

    protected function chartPeriodKeys(Request $request, Collection $sales, Collection $expenses): Collection
    {
        $filters = $this->getFilterState($request);
        [$startDate, $endDate] = $this->getDateScope($request);

        if ($filters['chart_view'] === 'daily') {
            $keys = collect();
            $cursor = $startDate->copy();

            while ($cursor->lte($endDate)) {
                $keys->push($cursor->toDateString());
                $cursor->addDay();
            }

            return $keys;
        }

        if ($filters['chart_view'] === 'monthly') {
            return collect(range(1, 12))
                ->map(fn($month) => sprintf('%04d-%02d', $filters['year'], $month));
        }

        return $sales->keys()
            ->merge($expenses->keys())
            ->push((string) now()->year)
            ->unique()
            ->sort()
            ->values();
    }

    protected function salesGroupExpression(string $chartView): string
    {
        return match ($chartView) {
            'daily' => 'DATE(created_at)',
            'monthly' => "DATE_FORMAT(created_at, '%Y-%m')",
            'yearly' => 'YEAR(created_at)',
            default => 'DATE(created_at)',
        };
    }

    protected function expenseGroupExpression(string $chartView): string
    {
        return match ($chartView) {
            'daily' => 'DATE(tanggal_pengeluaran)',
            'monthly' => "DATE_FORMAT(tanggal_pengeluaran, '%Y-%m')",
            'yearly' => 'YEAR(tanggal_pengeluaran)',
            default => 'DATE(tanggal_pengeluaran)',
        };
    }

    protected function formatPeriodLabel(string $periodKey, string $chartView): string
    {
        if ($chartView === 'daily') {
            $date = Carbon::parse($periodKey);

            return $date->format('d') . ' ' . $this->monthShortName((int) $date->format('n'));
        }

        if ($chartView === 'monthly') {
            [$year, $month] = explode('-', $periodKey);

            return $this->monthShortName((int) $month) . ' ' . $year;
        }

        return $periodKey;
    }

    protected function normalizeYear(mixed $year): ?int
    {
        if (! is_numeric($year)) {
            return null;
        }

        $year = (int) $year;

        return $year >= 2000 && $year <= 2100 ? $year : null;
    }

    protected function normalizeMonth(mixed $month): ?int
    {
        if (! is_numeric($month)) {
            return null;
        }

        $month = (int) $month;

        return $month >= 1 && $month <= 12 ? $month : null;
    }

    protected function monthName(int $month): string
    {
        return $this->months()[$month] ?? '-';
    }

    protected function monthShortName(int $month): string
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ][$month] ?? '-';
    }
}