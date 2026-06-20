<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function productStatuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    public function availabilityStatuses(): array
    {
        return [
            'tersedia' => 'Tersedia',
            'habis' => 'Habis',
        ];
    }

    public function getCategoriesForForm(?int $currentCategoryId = null)
    {
        return Category::query()
            ->where(function ($query) use ($currentCategoryId) {
                $query->where('status', 'aktif');

                if ($currentCategoryId) {
                    $query->orWhere('id', $currentCategoryId);
                }
            })
            ->orderBy('nama_kategori')
            ->get(['id', 'nama_kategori', 'status']);
    }

    public function getPaginatedProducts(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $status = $request->query('status');
        $availability = $request->query('status_ketersediaan');

        return Product::query()
            ->with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('nama_kategori', 'like', "%{$search}%");
                        });
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($status && array_key_exists($status, $this->productStatuses()), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($availability && array_key_exists($availability, $this->availabilityStatuses()), function ($query) use ($availability) {
                $query->where('status_ketersediaan', $availability);
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        $data = $this->normalizeData($data);

        $data['slug'] = $this->generateUniqueSlug($data['nama_produk']);
        $data['status_ketersediaan'] = 'tersedia';
        $data['status'] = 'aktif';

        if ($image) {
            $data['gambar'] = $this->storeImage($image);
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        $data = $this->normalizeData($data);

        $data['slug'] = $this->generateUniqueSlug($data['nama_produk'], $product->id);

        if ($image) {
            $this->deleteImage($product->gambar);
            $data['gambar'] = $this->storeImage($image);
        }

        $product->update($data);

        return $product;
    }

    public function toggleAvailability(Product $product): string
    {
        $newAvailability = $product->status_ketersediaan === 'tersedia'
            ? 'habis'
            : 'tersedia';

        $product->update([
            'status_ketersediaan' => $newAvailability,
        ]);

        return $newAvailability;
    }

    public function toggleStatus(Product $product): string
    {
        $newStatus = $product->status === 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $product->update([
            'status' => $newStatus,
        ]);

        return $newStatus;
    }

    public function deleteProduct(Product $product): void
    {
        $this->deleteImage($product->gambar);

        $product->delete();
    }

    private function normalizeData(array $data): array
    {
        if (array_key_exists('kode_produk', $data) && blank($data['kode_produk'])) {
            $data['kode_produk'] = null;
        }

        unset($data['gambar']);

        return $data;
    }

    private function storeImage(UploadedFile $image): string
    {
        return $image->store('products', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 2;

        while (
            Product::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}