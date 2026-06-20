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
            'statuses' => $this->cashierService->statuses(),
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
        abort_if($cashier->role !== 'kasir', 404);

        return view('admin.cashiers.show', [
            'cashier' => $cashier,
        ]);
    }

    public function edit(User $cashier): View
    {
        abort_if($cashier->role !== 'kasir', 404);

        return view('admin.cashiers.edit', [
            'cashier' => $cashier,
            'statuses' => $this->cashierService->statuses(),
        ]);
    }

    public function update(UpdateCashierRequest $request, User $cashier): RedirectResponse
    {
        $this->cashierService->updateCashier($cashier, $request->validated());

        return redirect()
            ->route('admin.cashiers.show', $cashier)
            ->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function updateStatus(User $cashier): RedirectResponse
    {
        $cashier = $this->cashierService->toggleStatus($cashier);

        $message = $cashier->status === 'aktif'
            ? 'Kasir berhasil diaktifkan.'
            : 'Kasir berhasil dinonaktifkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function resetPassword(User $cashier): RedirectResponse
    {
        $this->cashierService->resetPassword($cashier);

        return redirect()
            ->back()
            ->with('success', 'Password kasir berhasil direset ke default: ' . CashierService::DEFAULT_PASSWORD);
    }
}