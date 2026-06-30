<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cashier\StoreCashierRequest;
use App\Http\Requests\Admin\Cashier\UpdateCashierRequest;
use App\Models\User;
use App\Services\Admin\CashierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function __construct(
        protected CashierService $cashierService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.cashiers.index', [
            'cashiers' => $this->cashierService->getPaginatedCashiers($request),
        ]);
    }

    public function create(): View
    {
        return view('admin.cashiers.create');
    }

    public function store(StoreCashierRequest $request): RedirectResponse
    {
        $cashier = $this->cashierService->createCashier($request->validated());

        return redirect()
            ->route('admin.cashiers.show', $cashier)
            ->with('success', 'Kasir berhasil ditambahkan. Password default: ' . CashierService::DEFAULT_PASSWORD);
    }

    public function show(User $cashier): View
    {
        $this->abortIfNotActiveCashier($cashier);

        return view('admin.cashiers.show', [
            'cashier' => $cashier,
        ]);
    }

    public function edit(User $cashier): View
    {
        $this->abortIfNotActiveCashier($cashier);

        return view('admin.cashiers.edit', [
            'cashier' => $cashier,
        ]);
    }

    public function update(UpdateCashierRequest $request, User $cashier): RedirectResponse
    {
        $this->abortIfNotActiveCashier($cashier);

        $this->cashierService->updateCashier($cashier, $request->validated());

        return redirect()
            ->route('admin.cashiers.show', $cashier)
            ->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function resetPassword(User $cashier): RedirectResponse
    {
        $this->abortIfNotActiveCashier($cashier);

        $this->cashierService->resetPassword($cashier);

        return redirect()
            ->back()
            ->with('success', 'Password kasir berhasil direset ke default: ' . CashierService::DEFAULT_PASSWORD);
    }

    private function abortIfNotActiveCashier(User $cashier): void
    {
        abort_if(
            $cashier->role !== 'kasir' || $cashier->status !== 'aktif',
            404
        );
    }
}