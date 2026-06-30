<?php

namespace App\Services\Admin;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    public function getPaginatedCategories(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $statuses = $this->statuses();

        return Category::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_kategori', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($status && array_key_exists($status, $statuses), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createCategory(array $data): Category
    {
        $data['slug'] = $this->generateUniqueSlug($data['nama_kategori']);
        $data['status'] = 'aktif';
        $data['sort_order'] = $this->getNextSortOrder();

        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $data['slug'] = $this->generateUniqueSlug($data['nama_kategori'], $category->id);

        unset($data['sort_order']);

        $category->update($data);

        return $category;
    }

    public function toggleStatus(Category $category): string
    {
        $newStatus = $category->status === 'aktif' ? 'nonaktif' : 'aktif';

        $category->update([
            'status' => $newStatus,
        ]);

        return $newStatus;
    }

    public function deleteCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            if ($this->hasNonDeletedProducts($category)) {
                throw new Exception('Kategori tidak dapat dihapus karena masih memiliki produk yang belum dihapus.');
            }

            $category->update([
                'status' => 'nonaktif',
            ]);

            $category->delete();

            $this->normalizeSortOrder();
        });
    }

    public function reorderCategories(array $orders): void
    {
        DB::transaction(function () use ($orders) {
            foreach ($orders as $item) {
                Category::query()
                    ->where('id', $item['id'])
                    ->update([
                        'sort_order' => $item['sort_order'],
                    ]);
            }
        });

        $this->normalizeSortOrder();
    }

    private function hasNonDeletedProducts(Category $category): bool
    {
        return $category->products()->exists();
    }

    private function getNextSortOrder(): int
    {
        return ((int) Category::query()->max('sort_order')) + 1;
    }

    private function normalizeSortOrder(): void
    {
        Category::query()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (Category $category, int $index) {
                $category->update([
                    'sort_order' => $index + 1,
                ]);
            });
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 2;

        while (
            Category::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}