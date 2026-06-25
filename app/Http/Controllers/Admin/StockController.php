<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Admin\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $products = $this->stockService->getPaginatedProducts($request);
        $categories = $this->stockService->getCategories();

        return view('admin.stocks.index', [
            'products' => $products,
            'categories' => $categories,
            'canManage' => true,
            'routePrefix' => 'admin.stocks',
            'pageTitle' => 'Manajemen Stok',
            'pageSubtitle' => 'Kelola stok produk, tambah stok masuk, koreksi stok, dan lihat riwayat pergerakan stok.',
        ]);
    }

    public function movements(Product $product, Request $request)
    {
        $product->load('category');

        $movements = $this->stockService->getPaginatedMovements($product, $request);

        return view('admin.stocks.movements', [
            'product' => $product,
            'movements' => $movements,
            'canManage' => true,
            'routePrefix' => 'admin.stocks',
            'backRoute' => route('admin.stocks.index'),
            'pageTitle' => 'Riwayat Stok',
        ]);
    }

    public function adjust(Product $product, Request $request)
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'stock.required' => 'Stok aktual wajib diisi.',
            'stock.integer' => 'Stok aktual harus berupa angka bulat.',
            'stock.min' => 'Stok aktual tidak boleh kurang dari 0.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $this->stockService->adjustStock(
            $product,
            (int) $validated['stock'],
            $validated['note'] ?? null
        );

        return back()->with('success', 'Stok produk berhasil diperbarui.');
    }
}