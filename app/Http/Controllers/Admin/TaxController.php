<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tax\StoreTaxRequest;
use App\Http\Requests\Admin\Tax\UpdateTaxRequest;
use App\Models\Tax;
use App\Services\Admin\TaxService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxController extends Controller
{
    public function __construct(
        protected TaxService $taxService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.taxes.index', [
            'taxes' => $this->taxService->getPaginatedTaxes($request),
            'statuses' => $this->taxService->statuses(),
        ]);
    }

    public function create(): View
    {
        return view('admin.taxes.create');
    }

    public function store(StoreTaxRequest $request): RedirectResponse
    {
        $this->taxService->createTax($request->validated());

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Pajak berhasil ditambahkan dan diaktifkan.');
    }

    public function show(Tax $tax): View
    {
        return view('admin.taxes.show', [
            'tax' => $tax,
        ]);
    }

    public function edit(Tax $tax): View
    {
        return view('admin.taxes.edit', [
            'tax' => $tax,
            'statuses' => $this->taxService->statuses(),
        ]);
    }

    public function update(UpdateTaxRequest $request, Tax $tax): RedirectResponse
    {
        $this->taxService->updateTax($tax, $request->validated());

        return redirect()
            ->route('admin.taxes.show', $tax)
            ->with('success', 'Pajak berhasil diperbarui.');
    }

    public function updateStatus(Tax $tax): RedirectResponse
    {
        $tax = $this->taxService->toggleStatus($tax);

        $message = $tax->status === 'aktif'
            ? 'Pajak berhasil diaktifkan. Pajak aktif sebelumnya otomatis dinonaktifkan.'
            : 'Pajak berhasil dinonaktifkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function destroy(Tax $tax): RedirectResponse
    {
        try {
            $this->taxService->deleteTax($tax);

            return redirect()
                ->route('admin.taxes.index')
                ->with('success', 'Pajak berhasil dihapus.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}