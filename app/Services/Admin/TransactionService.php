<?php

namespace App\Services\Admin;

use App\Models\PosTerminal;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionService
{
    public function paymentMethods(): array
    {
        return [
            'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];
    }

    public function statuses(): array
    {
        return [
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public function getCashiers(): Collection
    {
        return User::query()
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getTerminals(): Collection
    {
        return PosTerminal::query()
            ->orderBy('kode_terminal')
            ->get(['id', 'kode_terminal', 'nama_terminal']);
    }

    public function getPaginatedSales(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQuery($request)
            ->with(['cashier', 'terminal', 'shift'])
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getSummary(Request $request): array
    {
        $query = $this->baseQuery($request);

        $completedQuery = (clone $query)->where('status', 'selesai');

        return [
            'total_transactions' => (clone $query)->count(),
            'completed_transactions' => (clone $completedQuery)->count(),
            'cancelled_transactions' => (clone $query)->where('status', 'dibatalkan')->count(),
            'grand_total' => (float) (clone $completedQuery)->sum('grand_total'),
            'cash_total' => (float) (clone $completedQuery)->where('payment_method', 'tunai')->sum('grand_total'),
            'non_cash_total' => (float) (clone $completedQuery)->whereIn('payment_method', ['qris', 'transfer'])->sum('grand_total'),
            'discount_total' => (float) (clone $completedQuery)->sum('total_diskon'),
            'tax_total' => (float) (clone $completedQuery)->sum('total_pajak'),
        ];
    }

    public function findSale(Sale $sale): Sale
    {
        return $sale->load([
            'items',
            'cashier',
            'terminal',
            'shift',
            'promo',
            'tax',
            'cancelledBy',
            'printJobs',
        ]);
    }

    protected function baseQuery(Request $request): Builder
    {
        return Sale::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('kode_transaksi', 'like', "%{$search}%")
                        ->orWhereHas('cashier', fn($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('terminal', fn($query) => $query->where('nama_terminal', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('tanggal_mulai'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->filled('tanggal_selesai'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->tanggal_selesai);
            })
            ->when($request->filled('cashier_id'), function ($query) use ($request) {
                $query->where('cashier_id', $request->cashier_id);
            })
            ->when($request->filled('pos_terminal_id'), function ($query) use ($request) {
                $query->where('pos_terminal_id', $request->pos_terminal_id);
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            });
    }
}