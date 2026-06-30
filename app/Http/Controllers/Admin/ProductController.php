<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $products = $this->productService->getPaginatedProducts($request);

        $categories = $this->productService->getCategoriesForForm();
        $productStatuses = $this->productService->productStatuses();
        $availabilityStatuses = $this->productService->availabilityStatuses();

        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $status = $request->query('status');
        $availability = $request->query('status_ketersediaan');

        return view('admin.products.index', compact(
            'products',
            'categories',
            'productStatuses',
            'availabilityStatuses',
            'search',
            'categoryId',
            'status',
            'availability'
        ));
    }

    public function create()
    {
        $categories = $this->productService->getCategoriesForForm();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->productService->createProduct(
            $request->validated(),
            $request->file('gambar')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('category');

        $categories = $this->productService->getCategoriesForForm($product->category_id);
        $productStatuses = $this->productService->productStatuses();
        $availabilityStatuses = $this->productService->availabilityStatuses();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'productStatuses',
            'availabilityStatuses'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('gambar')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function updateAvailability(Product $product)
    {
        $newAvailability = $this->productService->toggleAvailability($product);

        return back()->with(
            'success',
            $newAvailability === 'tersedia'
                ? 'Produk berhasil ditandai tersedia.'
                : 'Produk berhasil ditandai habis.'
        );
    }

    public function updateStatus(Product $product)
    {
        $newStatus = $this->productService->toggleStatus($product);

        return back()->with(
            'success',
            $newStatus === 'aktif'
                ? 'Produk berhasil diaktifkan.'
                : 'Produk berhasil dinonaktifkan.'
        );
    }

    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}