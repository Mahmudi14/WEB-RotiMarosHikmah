<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PromoService
{
    public function discountTypes(): array
    {
        return [
            'persentase' => 'Persentase',
            'nominal' => 'Nominal',
        ];
    }

    public function scopes(): array
    {
        return [
            'semua_menu' => 'Semua Menu',
            'menu_tertentu' => 'Menu Tertentu',
        ];
    }

    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    public function getPaginatedPromos(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $this->syncStatusesByPeriod();

        return Promo::query()
            ->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('nama_promo', 'like', "%{$search}%")
                        ->orWhere('kode_promo', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $today = now()->toDateString();

                if ($request->status === 'aktif') {
                    $query->where(function ($query) use ($today) {
                        $query->whereNull('tanggal_mulai')
                            ->orWhereDate('tanggal_mulai', '<=', $today);
                    })
                        ->where(function ($query) use ($today) {
                            $query->whereNull('tanggal_selesai')
                                ->orWhereDate('tanggal_selesai', '>=', $today);
                        });
                }

                if ($request->status === 'nonaktif') {
                    $query->where(function ($query) use ($today) {
                        $query->whereDate('tanggal_mulai', '>', $today)
                            ->orWhereDate('tanggal_selesai', '<', $today);
                    });
                }
            })
            ->when($request->filled('tipe_diskon'), function ($query) use ($request) {
                $query->where('tipe_diskon', $request->tipe_diskon);
            })
            ->when($request->filled('cakupan_promo'), function ($query) use ($request) {
                $query->where('cakupan_promo', $request->cakupan_promo);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function syncStatusesByPeriod(): void
    {
        $today = now()->toDateString();

        Promo::query()
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', '!=', 'aktif')
            ->update([
                'status' => 'aktif',
            ]);

        Promo::query()
            ->where(function ($query) use ($today) {
                $query->whereDate('tanggal_mulai', '>', $today)
                    ->orWhereDate('tanggal_selesai', '<', $today);
            })
            ->where('status', '!=', 'nonaktif')
            ->update([
                'status' => 'nonaktif',
            ]);
    }

    private function resolveStatusByDate(array $data): string
    {
        $today = now()->toDateString();

        if ($today < $data['tanggal_mulai']) {
            return 'nonaktif';
        }

        if ($today > $data['tanggal_selesai']) {
            return 'nonaktif';
        }

        return 'aktif';
    }

    public function getProductsForForm(?Promo $promo = null): Collection
    {
        $selectedProductIds = $promo
            ? $promo->products()->pluck('products.id')->toArray()
            : [];

        return Product::query()
            ->where(function ($query) use ($selectedProductIds) {
                $query->where('status', 'aktif');

                if (! empty($selectedProductIds)) {
                    $query->orWhereIn('id', $selectedProductIds);
                }
            })
            ->orderBy('nama_produk')
            ->get();
    }

    public function createPromo(array $data): Promo
    {
        return DB::transaction(function () use ($data) {
            $productIds = $data['product_ids'] ?? [];

            unset($data['product_ids']);

            $data['status'] = $this->resolveStatusByDate($data);

            $promo = Promo::create($data);

            $this->syncPromoProducts($promo, $productIds);

            return $promo;
        });
    }

    public function updatePromo(Promo $promo, array $data): Promo
    {
        return DB::transaction(function () use ($promo, $data) {
            $productIds = $data['product_ids'] ?? [];

            unset($data['product_ids']);
            unset($data['status']);

            $data['status'] = $this->resolveStatusByDate($data);

            $promo->update($data);

            $this->syncPromoProducts($promo, $productIds);

            return $promo->refresh();
        });
    }

    public function deletePromo(Promo $promo): void
    {
        DB::transaction(function () use ($promo) {
            $promo->update([
                'status' => 'nonaktif',
            ]);

            $promo->delete();
        });
    }

    private function syncPromoProducts(Promo $promo, array $productIds): void
    {
        if ($promo->cakupan_promo === 'semua_menu') {
            $promo->products()->sync([]);
            return;
        }

        $promo->products()->sync($productIds);
    }
}