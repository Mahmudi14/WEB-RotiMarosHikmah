<?php

namespace App\Http\Controllers\Finance;

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
            'canManage' => false,
            'routePrefix' => 'finance.stocks',
            'pageTitle' => 'Stok Produk',
            'pageSubtitle' => 'Lihat stok produk dan riwayat pergerakan stok.',
        ]);
    }

    public function movements(Product $product, Request $request)
    {
        $product->load('category');

        $movements = $this->stockService->getPaginatedMovements($product, $request);

        return view('admin.stocks.movements', [
            'product' => $product,
            'movements' => $movements,
            'canManage' => false,
            'routePrefix' => 'finance.stocks',
            'backRoute' => route('finance.stocks.index'),
            'pageTitle' => 'Riwayat Stok',
        ]);
    }
}