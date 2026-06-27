<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Promo\StorePromoRequest;
use App\Http\Requests\Admin\Promo\UpdatePromoRequest;
use App\Models\Promo;
use App\Services\Admin\PromoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function __construct(
        protected PromoService $promoService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.promos.index', [
            'promos' => $this->promoService->getPaginatedPromos($request),
            'discountTypes' => $this->promoService->discountTypes(),
            'scopes' => $this->promoService->scopes(),
            'statuses' => $this->promoService->statuses(),
        ]);
    }

    public function create(): View
    {
        return view('admin.promos.create', [
            'products' => $this->promoService->getProductsForForm(),
            'discountTypes' => $this->promoService->discountTypes(),
            'scopes' => $this->promoService->scopes(),
        ]);
    }

    public function store(StorePromoRequest $request): RedirectResponse
    {
        $this->promoService->createPromo($request->validated());

        return redirect()
            ->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    public function show(Promo $promo): View
    {
        $promo->load('products');

        return view('admin.promos.show', [
            'promo' => $promo,
        ]);
    }

    public function edit(Promo $promo): View
    {
        $promo->load('products');

        return view('admin.promos.edit', [
            'promo' => $promo,
            'products' => $this->promoService->getProductsForForm($promo),
            'discountTypes' => $this->promoService->discountTypes(),
            'scopes' => $this->promoService->scopes(),
            'statuses' => $this->promoService->statuses(),
        ]);
    }

    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    {
        $this->promoService->updatePromo($promo, $request->validated());

        return redirect()
            ->route('admin.promos.show', $promo)
            ->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $this->promoService->deletePromo($promo);

        return redirect()
            ->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}