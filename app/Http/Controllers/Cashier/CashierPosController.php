<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\Pos\StoreSaleRequest;
use App\Services\Cashier\CashierPosService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierPosController extends Controller
{
    public function __construct(
        protected CashierPosService $cashierPosService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cashier = $request->user();

        $activeShift = $this->cashierPosService->getActiveShift($cashier);

        if (! $activeShift) {
            return redirect()
                ->route('cashier.shifts.index')
                ->with('warning', 'Buka shift terlebih dahulu sebelum masuk ke POS.');
        }

        return view('cashier.pos.index', [
            'activeShift' => $activeShift,
            'categories' => $this->cashierPosService->getCategories(),
            'products' => $this->cashierPosService->getProducts(),
            'promos' => $this->cashierPosService->getActivePromos(),
            'activeTax' => $this->cashierPosService->getActiveTax(),
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->cashierPosService->createSale(
                $request->user(),
                $request->validated()
            );

            return redirect()
                ->route('cashier.pos.index')
                ->with('success', "Transaksi {$sale->kode_transaksi} berhasil disimpan.")
                ->with('clear_pos_cart', true);
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }
}