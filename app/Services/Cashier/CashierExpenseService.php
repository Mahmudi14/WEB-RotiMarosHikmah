<?php

namespace App\Services\Cashier;

use App\Models\CashierExpense;
use App\Models\CashierShift;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CashierExpenseService
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

    public function getPaginatedExpenses(User $cashier, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $activeShift = $this->getActiveShift($cashier);

        if (! $activeShift) {
            return new LengthAwarePaginator(
                [],
                0,
                $perPage,
                $request->integer('page', 1),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return CashierExpense::query()
            ->with(['shift', 'terminal'])
            ->where('cashier_id', $cashier->id)
            ->where('cashier_shift_id', $activeShift->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('kategori_pengeluaran', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createExpense(User $cashier, array $data): CashierExpense
    {
        return DB::transaction(function () use ($cashier, $data) {
            $activeShift = CashierShift::query()
                ->where('cashier_id', $cashier->id)
                ->where('status', 'aktif')
                ->latest('opened_at')
                ->lockForUpdate()
                ->first();

            if (! $activeShift) {
                throw new Exception(
                    'Kamu belum membuka shift. Buka shift terlebih dahulu sebelum mencatat pengeluaran.'
                );
            }

            $totalCashSales = (float) $activeShift->sales()
                ->where('status', 'selesai')
                ->where('payment_method', 'tunai')
                ->sum('grand_total');

            $totalExpenses = (float) $activeShift->expenses()
                ->sum('nominal');

            $availableCash =
                (float) $activeShift->opening_cash
                + $totalCashSales
                - $totalExpenses;

            $expenseAmount = (float) $data['harga'];

            if ($expenseAmount > $availableCash) {
                throw new Exception(
                    'Kas laci tidak mencukupi. Kas tersedia saat ini Rp '
                        . number_format(max(0, $availableCash), 0, ',', '.')
                        . '.'
                );
            }

            return CashierExpense::create([
                'cashier_id' => $cashier->id,
                'cashier_shift_id' => $activeShift->id,
                'pos_terminal_id' => $activeShift->pos_terminal_id,
                'tanggal_pengeluaran' => now()->toDateString(),
                'kategori_pengeluaran' => $data['nama_pengeluaran'],
                'nominal' => $expenseAmount,
                'keterangan' => $data['deskripsi'] ?? null,
            ]);
        }, 3);
    }

    public function deleteExpense(User $cashier, CashierExpense $expense): void
    {
        DB::transaction(function () use ($cashier, $expense) {
            $expense = CashierExpense::query()
                ->whereKey($expense->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureOwnExpense($cashier, $expense);

            $shift = CashierShift::query()
                ->whereKey($expense->cashier_shift_id)
                ->where('cashier_id', $cashier->id)
                ->lockForUpdate()
                ->first();

            if (! $shift) {
                throw new Exception('Shift pengeluaran tidak ditemukan.');
            }

            if ($shift->status !== 'aktif') {
                throw new Exception('Pengeluaran tidak dapat dihapus karena shift sudah ditutup.');
            }

            $expense->delete();
        });
    }

    public function ensureOwnExpense(User $cashier, CashierExpense $expense): void
    {
        abort_if((int) $expense->cashier_id !== (int) $cashier->id, 404);
    }
}