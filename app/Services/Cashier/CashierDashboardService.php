<?php

namespace App\Services\Cashier;

use App\Models\CashierShift;
use App\Models\PrintJob;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Collection;

class CashierDashboardService
{
    public function getActiveShift(User $cashier): ?CashierShift
    {
        return CashierShift::query()
            ->with(['terminal'])
            ->where('cashier_id', $cashier->id)
            ->where('status', 'aktif')
            ->latest('opened_at')
            ->first();
    }

    public function getShiftSummary(?CashierShift $shift): array
    {
        if (! $shift) {
            return [
                'sales_count' => 0,
                'total_cash_sales' => 0,
                'total_non_cash_sales' => 0,
                'total_sales' => 0,
                'total_expenses' => 0,
                'net_income' => 0,
                'cash_in_system' => 0,
            ];
        }

        $salesQuery = $shift->sales()
            ->where('status', 'selesai');

        $totalCashSales = (float) (clone $salesQuery)
            ->where('payment_method', 'tunai')
            ->sum('grand_total');

        $totalNonCashSales = (float) (clone $salesQuery)
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->sum('grand_total');

        $totalSales = $totalCashSales + $totalNonCashSales;

        $totalExpenses = (float) $shift->expenses()
            ->sum('nominal');

        $netIncome = $totalSales - $totalExpenses;

        $cashInSystem = (float) $shift->opening_cash + $totalCashSales - $totalExpenses;

        return [
            'sales_count' => (clone $salesQuery)->count(),
            'total_cash_sales' => $totalCashSales,
            'total_non_cash_sales' => $totalNonCashSales,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'cash_in_system' => $cashInSystem,
        ];
    }

    public function getTodaySummary(User $cashier): array
    {
        $salesQuery = Sale::query()
            ->where('cashier_id', $cashier->id)
            ->where('status', 'selesai')
            ->whereDate('created_at', now()->toDateString());

        return [
            'transactions' => (clone $salesQuery)->count(),
            'grand_total' => (float) (clone $salesQuery)->sum('grand_total'),
            'cash_total' => (float) (clone $salesQuery)->where('payment_method', 'tunai')->sum('grand_total'),
            'non_cash_total' => (float) (clone $salesQuery)->whereIn('payment_method', ['qris', 'transfer'])->sum('grand_total'),
        ];
    }

    public function getRecentSales(User $cashier, int $limit = 6): Collection
    {
        $activeShift = $this->getActiveShift($cashier);

        if (! $activeShift) {
            return collect();
        }

        return Sale::query()
            ->with(['terminal'])
            ->where('cashier_id', $cashier->id)
            ->where('cashier_shift_id', $activeShift->id)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getTopProductsToday(User $cashier, int $limit = 5): Collection
    {
        return SaleItem::query()
            ->selectRaw('
                sale_items.nama_produk,
                sale_items.nama_kategori,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_subtotal
            ')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.cashier_id', $cashier->id)
            ->where('sales.status', 'selesai')
            ->whereDate('sales.created_at', now()->toDateString())
            ->groupBy('sale_items.nama_produk', 'sale_items.nama_kategori')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function getPrintJobSummary(?CashierShift $shift): array
    {
        if (! $shift) {
            return [
                'pending' => 0,
                'printing' => 0,
                'printed' => 0,
                'failed' => 0,
            ];
        }

        $query = PrintJob::query()
            ->where('cashier_shift_id', $shift->id)
            ->where('type', 'receipt');

        return [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'printing' => (clone $query)->where('status', 'printing')->count(),
            'printed' => (clone $query)->where('status', 'printed')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
        ];
    }
}