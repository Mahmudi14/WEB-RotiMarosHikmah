<?php

namespace App\Services\Cashier;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PrintJob;
use App\Models\CashierShift;
use Exception;

class CashierTransactionService
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

    public function getActiveShift(User $cashier): ?CashierShift
    {
        return CashierShift::query()
            ->where('cashier_id', $cashier->id)
            ->where('status', 'aktif')
            ->latest('opened_at')
            ->first();
    }

    public function getPaginatedSales(User $cashier, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $activeShift = $this->getActiveShift($cashier);

        if (! $activeShift) {
            return new LengthAwarePaginator([], 0, $perPage, $request->integer('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return Sale::query()
            ->with(['terminal', 'items'])
            ->where('cashier_id', $cashier->id)
            ->where('cashier_shift_id', $activeShift->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('kode_transaksi', 'like', "%{$search}%")
                        ->orWhereHas('items', function ($query) use ($search) {
                            $query->where('nama_produk', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getSummary(User $cashier, Request $request): array
    {
        $activeShift = $this->getActiveShift($cashier);

        $emptySummary = [
            'total_transactions' => 0,
            'completed_transactions' => 0,
            'cancelled_transactions' => 0,

            'total_sales' => 0,
            'gross_sales' => 0,

            'cash_sales' => 0,
            'qris_sales' => 0,
            'transfer_sales' => 0,
            'non_cash_sales' => 0,

            // Alias untuk Blade lama
            'total_cash' => 0,
            'total_non_cash' => 0,
            'total_qris' => 0,
            'total_transfer' => 0,
        ];

        if (! $activeShift) {
            return $emptySummary;
        }

        $baseQuery = Sale::query()
            ->where('cashier_id', $cashier->id)
            ->where('cashier_shift_id', $activeShift->id);

        $completedQuery = (clone $baseQuery)
            ->where('status', 'selesai');

        $totalSales = (float) (clone $completedQuery)->sum('grand_total');

        $cashSales = (float) (clone $completedQuery)
            ->where('payment_method', 'tunai')
            ->sum('grand_total');

        $qrisSales = (float) (clone $completedQuery)
            ->where('payment_method', 'qris')
            ->sum('grand_total');

        $transferSales = (float) (clone $completedQuery)
            ->where('payment_method', 'transfer')
            ->sum('grand_total');

        $nonCashSales = $qrisSales + $transferSales;

        return [
            'total_transactions' => (clone $baseQuery)->count(),

            'completed_transactions' => (clone $baseQuery)
                ->where('status', 'selesai')
                ->count(),

            'cancelled_transactions' => (clone $baseQuery)
                ->where('status', 'dibatalkan')
                ->count(),

            'total_sales' => $totalSales,
            'gross_sales' => $totalSales,

            'cash_sales' => $cashSales,
            'qris_sales' => $qrisSales,
            'transfer_sales' => $transferSales,
            'non_cash_sales' => $nonCashSales,

            'total_cash' => $cashSales,
            'total_non_cash' => $nonCashSales,
            'total_qris' => $qrisSales,
            'total_transfer' => $transferSales,
        ];
    }

    public function findOwnSale(User $cashier, Sale $sale): Sale
    {
        $activeShift = $this->getActiveShift($cashier);

        abort_if(! $activeShift, 404);

        abort_if((int) $sale->cashier_id !== (int) $cashier->id, 404);
        abort_if((int) $sale->cashier_shift_id !== (int) $activeShift->id, 404);

        return $sale->load([
            'cashier',
            'terminal',
            'shift',
            'items',
            'printJobs' => fn($query) => $query->latest('created_at'),
        ]);
    }

    protected function baseQuery(User $cashier, Request $request): Builder
    {
        return Sale::query()
            ->where('cashier_id', $cashier->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where('kode_transaksi', 'like', "%{$search}%");
            })
            ->when($request->filled('tanggal_mulai'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->filled('tanggal_selesai'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->tanggal_selesai);
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            });
    }


    public function reprintReceipt(User $cashier, Sale $sale): PrintJob
    {
        $sale = $this->findOwnSale($cashier, $sale);

        if ($sale->status !== 'selesai') {
            throw new Exception('Struk hanya dapat dicetak ulang untuk transaksi selesai.');
        }

        $latestReceiptJob = $sale->printJobs
            ->where('type', 'receipt')
            ->sortByDesc('created_at')
            ->first();

        if ($latestReceiptJob && in_array($latestReceiptJob->status, ['pending', 'printing'], true)) {
            throw new Exception('Struk masih menunggu proses cetak. Cetak ulang tersedia setelah status berhasil atau gagal.');
        }

        return PrintJob::create([
            'pos_terminal_id' => $sale->pos_terminal_id,
            'sale_id' => $sale->id,
            'cashier_shift_id' => $sale->cashier_shift_id,
            'type' => 'receipt',
            'payload' => [
                'kode_transaksi' => $sale->kode_transaksi,
                'printed_at' => now()->format('d/m/Y H:i'),
                'is_reprint' => true,

                'terminal' => [
                    'kode_terminal' => $sale->terminal?->kode_terminal,
                    'nama_terminal' => $sale->terminal?->nama_terminal,
                ],

                'cashier' => [
                    'name' => $sale->cashier?->name,
                ],

                'items' => $sale->items->map(fn($item) => [
                    'nama_produk' => $item->nama_produk,
                    'qty' => (int) $item->qty,
                    'harga_satuan' => (float) $item->harga_satuan,
                    'subtotal' => (float) $item->subtotal,
                ])->values()->all(),

                'summary' => [
                    'subtotal' => (float) $sale->subtotal,
                    'promo' => $sale->nama_promo,
                    'total_diskon' => (float) $sale->total_diskon,
                    'pajak' => $sale->nama_pajak,
                    'total_pajak' => (float) $sale->total_pajak,
                    'grand_total' => (float) $sale->grand_total,
                    'payment_method' => $sale->payment_method,
                    'paid_amount' => (float) $sale->paid_amount,
                    'change_amount' => (float) $sale->change_amount,
                ],
            ],
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }
}