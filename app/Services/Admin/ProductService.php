<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        $stockCondition = $request->query('stock_condition');

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
            ->when($stockCondition === 'available', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->when($stockCondition === 'out', function ($query) {
                $query->where('stock', '<=', 0);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        $storedImagePath = null;

        try {
            return DB::transaction(function () use ($data, $image, &$storedImagePath) {
                $data = $this->normalizeData($data);

                $initialStock = (int) ($data['stock'] ?? 0);

                $data['stock'] = max(0, $initialStock);
                $data['slug'] = $this->generateUniqueSlug($data['nama_produk']);
                $data['status_ketersediaan'] = $data['stock'] > 0 ? 'tersedia' : 'habis';
                $data['status'] = 'aktif';

                if ($image) {
                    $storedImagePath = $this->storeImage($image);
                    $data['gambar'] = $storedImagePath;
                }

                $product = Product::create($data);

                if ($product->stock > 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $product->stock,
                        'stock_before' => 0,
                        'stock_after' => $product->stock,
                        'source' => 'initial',
                        'note' => 'Stok awal saat produk dibuat.',
                        'created_by' => auth()->id(),
                    ]);
                }

                return $product;
            });
        } catch (Throwable $e) {
            if ($storedImagePath) {
                $this->deleteImage($storedImagePath);
            }

            throw $e;
        }
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        $storedImagePath = null;
        $oldImageToDelete = null;

        try {
            $updatedProduct = DB::transaction(function () use ($product, $data, $image, &$storedImagePath, &$oldImageToDelete) {
                $data = $this->normalizeData($data);

                unset($data['stock']);
                unset($data['status_ketersediaan']);

                $data['slug'] = $this->generateUniqueSlug($data['nama_produk'], $product->id);

                $data['status_ketersediaan'] = (int) $product->stock > 0
                    ? 'tersedia'
                    : 'habis';

                if ($image) {
                    $storedImagePath = $this->storeImage($image);
                    $data['gambar'] = $storedImagePath;
                    $oldImageToDelete = $product->gambar;
                }

                $product->update($data);

                return $product->refresh();
            });

            if ($oldImageToDelete) {
                $this->deleteImage($oldImageToDelete);
            }

            return $updatedProduct;
        } catch (Throwable $e) {
            if ($storedImagePath) {
                $this->deleteImage($storedImagePath);
            }

            throw $e;
        }
    }

    public function toggleAvailability(Product $product): string
    {
        $newAvailability = $product->status_ketersediaan === 'tersedia'
            ? 'habis'
            : 'tersedia';

        if ($newAvailability === 'tersedia' && (int) $product->stock <= 0) {
            throw ValidationException::withMessages([
                'status_ketersediaan' => 'Produk dengan stok 0 tidak dapat ditandai tersedia.',
            ]);
        }

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
        $imageToDelete = $product->gambar;

        DB::transaction(function () use ($product) {
            $product->update([
                'status' => 'nonaktif',
                'status_ketersediaan' => 'habis',
                'gambar' => null,
            ]);

            $product->delete();
        });

        if ($imageToDelete) {
            $this->deleteImage($imageToDelete);
        }
    }

    private function normalizeData(array $data): array
    {
        if (array_key_exists('kode_produk', $data) && blank($data['kode_produk'])) {
            $data['kode_produk'] = null;
        }

        if (array_key_exists('stock', $data)) {
            $data['stock'] = max(0, (int) $data['stock']);
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
            Product::withTrashed()
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