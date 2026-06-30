<?php

namespace App\Services\Admin;

use App\Models\PosTerminal;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\CashierShift;
use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;

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
            ->orderByRaw("CASE WHEN status = 'aktif' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'status']);
    }

    public function getTerminals(): Collection
    {
        return PosTerminal::withTrashed()
            ->orderBy('kode_terminal')
            ->get(['id', 'kode_terminal', 'nama_terminal', 'deleted_at']);
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

    public function cancelSale(Sale $sale, User $admin, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $admin, $reason) {
            $sale = Sale::query()
                ->with(['items'])
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($sale->status === 'dibatalkan') {
                throw new Exception('Transaksi sudah dibatalkan sebelumnya.');
            }

            if ($sale->status !== 'selesai') {
                throw new Exception('Hanya transaksi selesai yang dapat dibatalkan.');
            }

            if (! $sale->cashier_shift_id) {
                throw new Exception('Transaksi tidak memiliki data shift, sehingga tidak dapat dibatalkan.');
            }

            $shift = CashierShift::query()
                ->whereKey($sale->cashier_shift_id)
                ->lockForUpdate()
                ->first();

            if (! $shift) {
                throw new Exception('Shift transaksi tidak ditemukan.');
            }

            if ($shift->status !== 'aktif') {
                throw new Exception('Transaksi dari shift yang sudah ditutup tidak dapat dibatalkan.');
            }

            foreach ($sale->items as $item) {
                if (! $item->product_id) {
                    throw new Exception("Produk {$item->nama_produk} tidak ditemukan. Stok tidak dapat dikembalikan.");
                }

                $product = Product::withTrashed()
                    ->whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw new Exception("Produk {$item->nama_produk} tidak ditemukan. Stok tidak dapat dikembalikan.");
                }

                $qty = (int) $item->qty;

                if ($qty <= 0) {
                    continue;
                }

                $stockBefore = (int) $product->stock;
                $stockAfter = $stockBefore + $qty;

                $product->update([
                    'stock' => $stockAfter,
                    'status_ketersediaan' => $stockAfter > 0 ? 'tersedia' : 'habis',
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => 'sale_cancel',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'note' => 'Pengembalian stok dari pembatalan transaksi ' . $sale->kode_transaksi . '. Alasan: ' . $reason,
                    'created_by' => $admin->id,
                ]);
            }

            $sale->update([
                'status' => 'dibatalkan',
                'cancelled_by' => $admin->id,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            return $sale->load([
                'cashier',
                'terminal',
                'shift',
                'items',
                'cancelledBy',
                'printJobs' => fn($query) => $query->latest('created_at'),
            ]);
        });
    }
}