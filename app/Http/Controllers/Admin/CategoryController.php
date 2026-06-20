<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(Request $request)
    {
        $statuses = $this->categoryService->statuses();

        $categories = $this->categoryService->getPaginatedCategories($request);

        $search = $request->query('search');
        $status = $request->query('status');

        return view('admin.categories.index', compact(
            'categories',
            'statuses',
            'search',
            'status'
        ));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori produk berhasil ditambahkan dan otomatis aktif.');
    }

    public function show(Category $category)
    {
        $category->loadCount('products');

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $statuses = $this->categoryService->statuses();

        return view('admin.categories.edit', compact('category', 'statuses'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->updateCategory($category, $request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function updateStatus(Category $category)
    {
        $newStatus = $this->categoryService->toggleStatus($category);

        return back()->with(
            'success',
            $newStatus === 'aktif'
                ? 'Kategori berhasil diaktifkan.'
                : 'Kategori berhasil dinonaktifkan.'
        );
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:categories,id'],
            'orders.*.sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $this->categoryService->reorderCategories($validated['orders']);

        return response()->json([
            'success' => true,
            'message' => 'Urutan kategori berhasil diperbarui.',
        ]);
    }

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->deleteCategory($category);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori produk berhasil dihapus.');
        } catch (Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}