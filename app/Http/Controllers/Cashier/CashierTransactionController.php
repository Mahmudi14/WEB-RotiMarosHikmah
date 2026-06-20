<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\Cashier\CashierTransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;
use Illuminate\Http\RedirectResponse;

class CashierTransactionController extends Controller
{
    public function __construct(
        protected CashierTransactionService $cashierTransactionService
    ) {}

    public function index(Request $request): View
    {
        $cashier = $request->user();

        return view('cashier.transactions.index', [
            'sales' => $this->cashierTransactionService->getPaginatedSales($cashier, $request),
            'summary' => $this->cashierTransactionService->getSummary($cashier, $request),
            'paymentMethods' => $this->cashierTransactionService->paymentMethods(),
            'statuses' => $this->cashierTransactionService->statuses(),
        ]);
    }

    public function show(Request $request, Sale $sale): View
    {
        return view('cashier.transactions.show', [
            'sale' => $this->cashierTransactionService->findOwnSale($request->user(), $sale),
        ]);
    }

    public function reprint(Request $request, Sale $sale): RedirectResponse
    {
        try {
            $this->cashierTransactionService->reprintReceipt(
                $request->user(),
                $sale
            );

            return redirect()
                ->route('cashier.transactions.show', $sale)
                ->with('success', 'Struk berhasil dimasukkan ke antrean cetak ulang.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}