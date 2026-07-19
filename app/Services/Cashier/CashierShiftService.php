<?php

namespace App\Services\Cashier;

use App\Models\CashierShift;
use App\Models\PosTerminal;
use App\Models\PrintJob;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CashierShiftService
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

    public function getActiveShifts(): Collection
    {
        return CashierShift::query()
            ->with(['cashier', 'terminal'])
            ->where('status', 'aktif')
            ->latest('opened_at')
            ->get();
    }

    public function getPaginatedShifts(User $cashier, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return CashierShift::query()
            ->with(['terminal'])
            ->where('cashier_id', $cashier->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('opened_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAvailableTerminals(): mixed
    {
        return PosTerminal::query()
            ->where('status', 'aktif')
            ->whereDoesntHave('cashierShifts', function ($query) {
                $query->where('status', 'aktif');
            })
            ->orderBy('nama_terminal')
            ->get();
    }

    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'ditutup' => 'Ditutup',
        ];
    }

    public function openShift(User $cashier, array $data): CashierShift
    {
        return DB::transaction(function () use ($cashier, $data) {
            User::query()
                ->whereKey($cashier->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasActiveShift = CashierShift::query()
                ->where('cashier_id', $cashier->id)
                ->where('status', 'aktif')
                ->lockForUpdate()
                ->exists();

            if ($hasActiveShift) {
                throw new Exception(
                    'Kamu masih memiliki shift aktif. Tutup shift terlebih dahulu sebelum membuka shift baru.'
                );
            }

            $terminal = PosTerminal::query()
                ->whereKey($data['pos_terminal_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($terminal->status !== 'aktif') {
                throw new Exception('Terminal kasir tidak aktif.');
            }

            $terminalUsed = CashierShift::query()
                ->where('pos_terminal_id', $terminal->id)
                ->where('status', 'aktif')
                ->lockForUpdate()
                ->exists();

            if ($terminalUsed) {
                throw new Exception(
                    'Terminal kasir sedang digunakan pada shift aktif lain.'
                );
            }

            return CashierShift::create([
                'cashier_id' => $cashier->id,
                'pos_terminal_id' => $terminal->id,
                'opening_cash' => $data['opening_cash'],
                'opened_at' => now(),
                'status' => 'aktif',
                'opening_note' => $data['opening_note'] ?? null,
            ]);
        }, 3);
    }

    public function calculateTotals(CashierShift $shift): array
    {
        $totalCashSales = (float) $shift->sales()
            ->where('status', 'selesai')
            ->where('payment_method', 'tunai')
            ->sum('grand_total');

        $totalNonCashSales = (float) $shift->sales()
            ->where('status', 'selesai')
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->sum('grand_total');

        $totalExpenses = (float) $shift->expenses()
            ->sum('nominal');

        // Pemasukan shift: tunai + non tunai
        $totalIncome = $totalCashSales + $totalNonCashSales;

        // Pemasukan bersih shift: semua pemasukan dikurangi pengeluaran
        $netIncome = $totalIncome - $totalExpenses;

        // Uang kas/laci menurut sistem: hanya uang yang benar-benar ada di laci
        $cashInSystem = (float) $shift->opening_cash + $totalCashSales - $totalExpenses;

        return [
            'total_cash_sales' => $totalCashSales,
            'total_non_cash_sales' => $totalNonCashSales,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,

            'cash_in_system' => $cashInSystem,
            'expected_cash' => $cashInSystem,
        ];
    }

    public function closeShift(
        User $cashier,
        CashierShift $shift,
        array $data
    ): CashierShift {
        return DB::transaction(function () use ($cashier, $shift, $data) {
            $shift = CashierShift::query()
                ->whereKey($shift->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureOwnShift($cashier, $shift);

            if ($shift->status !== 'aktif') {
                throw new Exception('Shift ini sudah ditutup.');
            }

            $totals = $this->calculateTotals($shift);

            $closingCash = (float) $data['closing_cash'];

            $cashDifference = $closingCash - $totals['cash_in_system'];

            $shift->update([
                'closing_cash' => $closingCash,
                'total_cash_sales' => $totals['total_cash_sales'],
                'total_non_cash_sales' => $totals['total_non_cash_sales'],
                'total_expenses' => $totals['total_expenses'],
                'expected_cash' => $totals['cash_in_system'],
                'cash_difference' => $cashDifference,
                'closed_at' => now(),
                'status' => 'ditutup',
                'closing_note' => $data['closing_note'] ?? null,
            ]);

            $shift = $shift
                ->refresh()
                ->load(['cashier', 'terminal']);

            $this->createShiftReportPrintJob($shift);

            return $shift;
        });
    }


    public function ensureOwnShift(User $cashier, CashierShift $shift): void
    {
        abort_if($shift->cashier_id !== $cashier->id, 404);
    }

    private function createShiftReportPrintJob(CashierShift $shift): PrintJob
    {
        $shift->loadMissing(['cashier', 'terminal']);

        return PrintJob::create([
            'pos_terminal_id' => $shift->pos_terminal_id,
            'sale_id' => null,
            'cashier_shift_id' => $shift->id,
            'type' => 'shift_report',
            'payload' => $this->shiftReportPayload($shift),
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    public function reprintShiftReport(User $cashier, CashierShift $shift): PrintJob
    {
        return DB::transaction(function () use ($cashier, $shift) {
            $shift = CashierShift::query()
                ->whereKey($shift->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureOwnShift($cashier, $shift);

            if ($shift->status !== 'ditutup') {
                throw new Exception('Struk shift hanya dapat dicetak ulang setelah shift ditutup.');
            }

            $existingPrintJob = PrintJob::query()
                ->where('cashier_shift_id', $shift->id)
                ->where('type', 'shift_report')
                ->whereIn('status', ['pending', 'printing'])
                ->latest('created_at')
                ->first();

            if ($existingPrintJob) {
                return $existingPrintJob;
            }

            return $this->createShiftReportPrintJob($shift);
        });
    }

    public function getRecentlyClosedShiftForPrint(User $cashier, mixed $shiftId): ?CashierShift
    {
        if (! $shiftId) {
            return null;
        }

        return CashierShift::query()
            ->with([
                'terminal',
                'printJobs' => fn($query) => $query
                    ->where('type', 'shift_report')
                    ->latest('created_at'),
            ])
            ->whereKey($shiftId)
            ->where('cashier_id', $cashier->id)
            ->where('status', 'ditutup')
            ->first();
    }

    private function shiftReportPayload(CashierShift $shift): array
    {
        $salesQuery = Sale::query()
            ->where('cashier_shift_id', $shift->id)
            ->where('status', 'selesai');

        $cashSales = (float) (clone $salesQuery)
            ->where('payment_method', 'tunai')
            ->sum('grand_total');

        $qrisSales = (float) (clone $salesQuery)
            ->where('payment_method', 'qris')
            ->sum('grand_total');

        $transferSales = (float) (clone $salesQuery)
            ->where('payment_method', 'transfer')
            ->sum('grand_total');

        $grossRevenue = $cashSales + $qrisSales + $transferSales;

        $totalTransactions = (int) (clone $salesQuery)->count();

        $expenses = $shift->expenses()
            ->latest('tanggal_pengeluaran')
            ->latest('created_at')
            ->get()
            ->map(fn($expense) => [
                'title' => $expense->kategori_pengeluaran,
                'amount' => (float) $expense->nominal,
                'note' => $expense->keterangan ?? '',
            ])
            ->values()
            ->all();

        $totalExpense = (float) $shift->expenses()->sum('nominal');

        $soldItems = SaleItem::query()
            ->whereHas('sale', function ($query) use ($shift) {
                $query->where('cashier_shift_id', $shift->id)
                    ->where('status', 'selesai');
            })
            ->selectRaw('
            nama_produk as name,
            SUM(qty) as quantity,
            SUM(subtotal) as total_amount
        ')
            ->groupBy('nama_produk')
            ->orderByDesc('quantity')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'quantity' => (int) $item->quantity,
                'total_amount' => (float) $item->total_amount,
            ])
            ->values()
            ->all();

        $soldItemsCount = collect($soldItems)->sum('quantity');

        $shiftStart = $shift->opened_at;
        $shiftEnd = $shift->closed_at ?? now();

        return [
            'cashier_name' => $shift->cashier?->name ?? '-',
            'shift_start' => $shiftStart?->format('d/m/Y H:i') ?? '-',
            'shift_end' => $shiftEnd?->format('d/m/Y H:i') ?? '-',
            'duration' => $this->formatShiftDuration($shiftStart, $shiftEnd),
            'total_transactions' => $totalTransactions,
            'sold_items_count' => (int) $soldItemsCount,
            'printed_at' => now()->format('d/m/Y H:i'),

            'terminal' => [
                'id' => $shift->terminal?->id,
                'kode_terminal' => $shift->terminal?->kode_terminal,
                'nama_terminal' => $shift->terminal?->nama_terminal,
            ],

            'cash_management' => [
                'starting_cash' => (float) $shift->opening_cash,
                'cash_sales' => $cashSales,
                'cash_expense' => $totalExpense,
                'ending_cash' => (float) $shift->closing_cash,
                'expected_cash' => (float) $shift->expected_cash,
                'cash_difference' => (float) $shift->cash_difference,
            ],

            'payment_summary' => [
                'cash' => $cashSales,
                'qris' => $qrisSales,
                'transfer' => $transferSales,
                'total_payment' => $grossRevenue,
            ],

            'expenses' => $expenses,

            'sold_items' => $soldItems,

            'summary' => [
                'gross_revenue' => $grossRevenue,
                'total_expense' => $totalExpense,
                'net_revenue' => $grossRevenue - $totalExpense,
            ],

            'notes' => [
                'opening_note' => $shift->opening_note ?? '',
                'closing_note' => $shift->closing_note ?? '',
            ],
        ];
    }

    private function formatShiftDuration($start, $end): string
    {
        if (! $start || ! $end) {
            return '-';
        }

        $totalMinutes = (int) $start->diffInMinutes($end);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        if ($hours <= 0) {
            return "{$minutes} menit";
        }

        if ($minutes <= 0) {
            return "{$hours} jam";
        }

        return "{$hours} jam {$minutes} menit";
    }

    public function getProductSalesSummary(CashierShift $shift): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.cashier_shift_id', $shift->id)
            ->where('sales.status', 'selesai')
            ->select(
                'sale_items.product_id',
                'sale_items.nama_produk',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_subtotal')
            )
            ->groupBy('sale_items.product_id', 'sale_items.nama_produk')
            ->orderByDesc('total_qty')
            ->orderBy('sale_items.nama_produk')
            ->get();
    }
}