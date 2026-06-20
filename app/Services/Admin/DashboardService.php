<?php

namespace App\Services\Admin;

use App\Models\CashierExpense;
use App\Models\CashierShift;
use App\Models\PrintJob;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getTodaySummary(): array
    {
        $today = now()->toDateString();

        $salesQuery = Sale::query()
            ->where('status', 'selesai')
            ->whereDate('created_at', $today);

        $expenseQuery = CashierExpense::query()
            ->whereDate('tanggal_pengeluaran', $today);

        return [
            'transactions' => (clone $salesQuery)->count(),
            'gross_sales' => (float) (clone $salesQuery)->sum('grand_total'),
            'cash_sales' => (float) (clone $salesQuery)->where('payment_method', 'tunai')->sum('grand_total'),
            'non_cash_sales' => (float) (clone $salesQuery)->whereIn('payment_method', ['qris', 'transfer'])->sum('grand_total'),
            'expenses' => (float) (clone $expenseQuery)->sum('nominal'),
            'net_income' => (float) (clone $salesQuery)->sum('grand_total') - (float) (clone $expenseQuery)->sum('nominal'),
            'discounts' => (float) (clone $salesQuery)->sum('total_diskon'),
            'taxes' => (float) (clone $salesQuery)->sum('total_pajak'),
        ];
    }

    public function getShiftSummary(): array
    {
        return [
            'active_shifts' => CashierShift::query()->where('status', 'aktif')->count(),
            'closed_shifts_today' => CashierShift::query()
                ->where('status', 'ditutup')
                ->whereDate('closed_at', now()->toDateString())
                ->count(),
            'active_cashiers' => User::query()
                ->where('role', 'kasir')
                ->whereHas('cashierShifts', fn($query) => $query->where('status', 'aktif'))
                ->count(),
        ];
    }

    public function getPrintJobSummary(): array
    {
        $query = PrintJob::query()
            ->whereDate('created_at', now()->toDateString());

        return [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'printing' => (clone $query)->where('status', 'printing')->count(),
            'printed' => (clone $query)->where('status', 'printed')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
        ];
    }

    public function getRecentSales(int $limit = 5): Collection
    {
        return Sale::query()
            ->with(['cashier', 'terminal'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getActiveShifts(int $limit = 5): Collection
    {
        return CashierShift::query()
            ->with(['cashier', 'terminal'])
            ->where('status', 'aktif')
            ->latest('opened_at')
            ->limit($limit)
            ->get();
    }

    public function getTopProductsToday(int $limit = 5): Collection
    {
        return SaleItem::query()
            ->selectRaw('
                sale_items.nama_produk,
                sale_items.nama_kategori,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_subtotal
            ')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'selesai')
            ->whereDate('sales.created_at', now()->toDateString())
            ->groupBy('sale_items.nama_produk', 'sale_items.nama_kategori')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }
}