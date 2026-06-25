<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function getCategories()
    {
        return Category::query()
            ->where('status', 'aktif')
            ->orderBy('nama_kategori')
            ->get(['id', 'nama_kategori']);
    }

    public function getPaginatedProducts(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $condition = $request->query('condition');

        return Product::query()
            ->with(['category'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('nama_kategori', 'like', "%{$search}%");
                        });
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($condition === 'available', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->when($condition === 'out', function ($query) {
                $query->where('stock', 0);
            })
            ->orderBy('nama_produk')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPaginatedMovements(Product $product, Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $type = $request->query('type');
        $source = $request->query('source');

        return StockMovement::query()
            ->with(['product.category', 'creator'])
            ->where('product_id', $product->id)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($source, function ($query) use ($source) {
                $query->where('source', $source);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function increaseStock(Product $product, int $quantity, ?string $note = null): Product
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah stok masuk harus lebih dari 0.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity, $note) {
            $product = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore + $quantity;

            $product->update([
                'stock' => $stockAfter,
                'status_ketersediaan' => $stockAfter > 0 ? 'tersedia' : 'habis',
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source' => 'manual',
                'note' => $note ?: 'Tambah stok manual.',
                'created_by' => auth()->id(),
            ]);

            return $product;
        });
    }

    public function adjustStock(Product $product, int $targetStock, ?string $note = null): Product
    {
        if ($targetStock < 0) {
            throw ValidationException::withMessages([
                'stock' => 'Stok aktual tidak boleh kurang dari 0.',
            ]);
        }

        return DB::transaction(function () use ($product, $targetStock, $note) {
            $product = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = (int) $product->stock;
            $stockAfter = $targetStock;
            $difference = $stockAfter - $stockBefore;

            if ($difference === 0) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok aktual sama dengan stok saat ini.',
                ]);
            }

            $product->update([
                'stock' => $stockAfter,
                'status_ketersediaan' => $stockAfter > 0 ? 'tersedia' : 'habis',
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source' => 'correction',
                'note' => $note ?: 'Koreksi stok manual.',
                'created_by' => auth()->id(),
            ]);

            return $product;
        });
    }
}