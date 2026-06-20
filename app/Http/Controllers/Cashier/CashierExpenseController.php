<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\Expense\StoreCashierExpenseRequest;
use App\Models\CashierExpense;
use App\Services\Cashier\CashierExpenseService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierExpenseController extends Controller
{
    public function __construct(
        protected CashierExpenseService $cashierExpenseService
    ) {}

    public function index(Request $request): View
    {
        $cashier = $request->user();

        return view('cashier.expenses.index', [
            'activeShift' => $this->cashierExpenseService->getActiveShift($cashier),
            'expenses' => $this->cashierExpenseService->getPaginatedExpenses($cashier, $request),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $cashier = $request->user();

        $activeShift = $this->cashierExpenseService->getActiveShift($cashier);

        if (! $activeShift) {
            return redirect()
                ->route('cashier.shifts.index')
                ->with('warning', 'Buka shift terlebih dahulu sebelum mencatat pengeluaran.');
        }

        return view('cashier.expenses.create', [
            'activeShift' => $activeShift,
        ]);
    }

    public function store(StoreCashierExpenseRequest $request): RedirectResponse
    {
        try {
            $this->cashierExpenseService->createExpense(
                $request->user(),
                $request->validated()
            );

            return redirect()
                ->route('cashier.expenses.index')
                ->with('success', 'Pengeluaran berhasil dicatat.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function destroy(Request $request, CashierExpense $expense): RedirectResponse
    {
        try {
            $this->cashierExpenseService->deleteExpense(
                $request->user(),
                $expense
            );

            return redirect()
                ->route('cashier.expenses.index')
                ->with('success', 'Pengeluaran berhasil dihapus.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}