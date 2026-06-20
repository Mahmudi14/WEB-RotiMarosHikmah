<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\Shift\CloseCashierShiftRequest;
use App\Http\Requests\Cashier\Shift\StoreCashierShiftRequest;
use App\Models\CashierShift;
use App\Services\Cashier\CashierShiftService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierShiftController extends Controller
{
    public function __construct(
        protected CashierShiftService $cashierShiftService
    ) {}

    public function index(Request $request): View
    {
        $cashier = $request->user();

        return view('cashier.shifts.index', [
            'activeShift' => $this->cashierShiftService->getActiveShift($cashier),
            'activeShifts' => $this->cashierShiftService->getActiveShifts(),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $cashier = $request->user();

        if ($this->cashierShiftService->getActiveShift($cashier)) {
            return redirect()
                ->route('cashier.shifts.index')
                ->with('warning', 'Kamu masih memiliki shift aktif.');
        }

        return view('cashier.shifts.create', [
            'terminals' => $this->cashierShiftService->getAvailableTerminals(),
        ]);
    }

    public function store(StoreCashierShiftRequest $request): RedirectResponse
    {
        try {
            $shift = $this->cashierShiftService->openShift(
                $request->user(),
                $request->validated()
            );

            return redirect()
                ->route('cashier.shifts.index', $shift)
                ->with('success', 'Shift berhasil dibuka. Kamu sudah bisa mulai transaksi POS.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function show(Request $request, CashierShift $cashierShift): View|RedirectResponse
    {
        $this->cashierShiftService->ensureOwnShift($request->user(), $cashierShift);

        if ($cashierShift->status !== 'aktif') {
            return redirect()
                ->route('cashier.shifts.index')
                ->with('warning', 'Shift tersebut sudah ditutup.');
        }

        $cashierShift->load(['terminal']);

        $productSales = $this->cashierShiftService->getProductSalesSummary($cashierShift);

        return view('cashier.shifts.show', [
            'shift' => $cashierShift,
            'totals' => $this->cashierShiftService->calculateTotals($cashierShift),
            'productSales' => $productSales,
            'totalProductsSold' => (int) $productSales->sum('total_qty'),
        ]);
    }

    public function closeForm(Request $request, CashierShift $cashierShift): View|RedirectResponse
    {
        $this->cashierShiftService->ensureOwnShift($request->user(), $cashierShift);

        if ($cashierShift->status !== 'aktif') {
            return redirect()
                ->route('cashier.shifts.show', $cashierShift)
                ->with('warning', 'Shift ini sudah ditutup.');
        }

        return view('cashier.shifts.close', [
            'shift' => $cashierShift->load(['terminal']),
            'totals' => $this->cashierShiftService->calculateTotals($cashierShift),
        ]);
    }

    public function closeShift(CloseCashierShiftRequest $request, CashierShift $cashierShift): RedirectResponse
    {
        try {
            $shift = $this->cashierShiftService->closeShift(
                $request->user(),
                $cashierShift,
                $request->validated()
            );

            return redirect()
                ->route('cashier.shifts.index')
                ->with('success', 'Shift berhasil ditutup. Laporan shift masuk ke antrean print.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }
}