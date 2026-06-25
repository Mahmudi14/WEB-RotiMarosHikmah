<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\Admin\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $adminTransactionService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.transactions.index', [
            'sales' => $this->adminTransactionService->getPaginatedSales($request),
            'summary' => $this->adminTransactionService->getSummary($request),
            'paymentMethods' => $this->adminTransactionService->paymentMethods(),
            'statuses' => $this->adminTransactionService->statuses(),
            'cashiers' => $this->adminTransactionService->getCashiers(),
            'terminals' => $this->adminTransactionService->getTerminals(),
        ]);
    }

    public function show(Sale $sale): View
    {
        return view('admin.transactions.show', [
            'sale' => $this->adminTransactionService->findSale($sale),
        ]);
    }

    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancel_reason.string' => 'Alasan pembatalan harus berupa teks.',
            'cancel_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
            'cancel_reason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        try {
            $this->adminTransactionService->cancelSale(
                sale: $sale,
                admin: $request->user(),
                reason: $validated['cancel_reason'],
            );

            return redirect()
                ->route('admin.transactions.show', $sale)
                ->with('success', 'Transaksi berhasil dibatalkan dan stok produk dikembalikan.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}