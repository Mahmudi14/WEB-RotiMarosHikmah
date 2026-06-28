<?php

namespace App\Services\Cashier;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\PrintJob;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\StockMovement;
use App\Models\TransactionCounter;
use Illuminate\Database\QueryException;

class CashierPosService
{
    public function getActiveShift(User $cashier): ?CashierShift
    {
        return CashierShift::query()
            ->with(['terminal'])
            ->where('cashier_id', $cashier->id)
            ->where('status', 'aktif')
            ->latest('opened_at')
            ->first();
    }

    public function getCategories(): Collection
    {
        return Category::query()
            ->select('id', 'nama_kategori', 'sort_order')
            ->where('status', 'aktif')
            ->whereHas('products', function ($query) {
                $query->where('products.status', 'aktif')
                    ->where('products.status_ketersediaan', 'tersedia')
                    ->where('products.stock', '>', 0);
            })
            ->orderBy('sort_order')
            ->orderBy('nama_kategori')
            ->get();
    }

    public function getProducts(): Collection
    {
        return Product::query()
            ->with(['category'])
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.status', 'aktif')
            ->where('products.status_ketersediaan', 'tersedia')
            ->where('products.stock', '>', 0)
            ->where('categories.status', 'aktif')
            ->orderBy('categories.sort_order')
            ->orderBy('products.nama_produk')
            ->select('products.*')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->nama_kategori,
                    'category_order' => $product->category?->sort_order ?? 999999,
                    'kode_produk' => $product->kode_produk,
                    'nama_produk' => $product->nama_produk,
                    'harga_jual' => (float) $product->harga_jual,
                    'harga_jual_formatted' => $product->harga_jual_formatted,
                    'stock' => (int) $product->stock,
                    'gambar_url' => $product->gambar ? Storage::url($product->gambar) : null,
                ];
            });
    }

    public function createSale(User $cashier, array $data): Sale
    {
        return DB::transaction(function () use ($cashier, $data) {
            $shift = CashierShift::query()
                ->with(['terminal'])
                ->where('cashier_id', $cashier->id)
                ->where('status', 'aktif')
                ->lockForUpdate()
                ->latest('opened_at')
                ->first();

            if (! $shift) {
                throw new Exception('Shift aktif tidak ditemukan. Buka shift terlebih dahulu.');
            }

            $requestedItems = collect($data['items'])
                ->groupBy('product_id')
                ->map(function ($rows, $productId) {
                    return [
                        'product_id' => (int) $productId,
                        'qty' => (int) $rows->sum('qty'),
                    ];
                })
                ->values();

            if ($requestedItems->isEmpty()) {
                throw new Exception('Keranjang masih kosong.');
            }

            $products = Product::query()
                ->with(['category'])
                ->whereIn('id', $requestedItems->pluck('product_id'))
                ->where('status', 'aktif')
                ->where('status_ketersediaan', 'tersedia')
                ->whereHas('category', fn($query) => $query->where('status', 'aktif'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $requestedItems->count()) {
                throw new Exception('Ada produk yang tidak tersedia atau sudah nonaktif.');
            }

            $saleItems = collect();
            $subtotal = 0;

            foreach ($requestedItems as $requestedItem) {
                /** @var Product $product */
                $product = $products->get($requestedItem['product_id']);

                $qty = (int) $requestedItem['qty'];

                if ($qty <= 0) {
                    throw new Exception('Jumlah produk tidak valid.');
                }

                if ((int) $product->stock <= 0) {
                    throw new Exception("Produk {$product->nama_produk} sedang habis.");
                }

                if ((int) $product->stock < $qty) {
                    throw new Exception(
                        "Stok {$product->nama_produk} tidak cukup. Sisa stok: {$product->stock}."
                    );
                }

                $price = (float) $product->harga_jual;
                $lineSubtotal = $price * $qty;

                $subtotal += $lineSubtotal;

                $saleItems->push([
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'kode_produk' => $product->kode_produk,
                    'nama_produk' => $product->nama_produk,
                    'nama_kategori' => $product->category?->nama_kategori,
                    'harga_satuan' => $price,
                    'qty' => $qty,
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $bestPromo = $this->findBestPromo($saleItems, $subtotal);

            $promo = $bestPromo['promo'];
            $totalDiscount = $bestPromo['discount'];

            $taxableSubtotal = max(0, $subtotal - $totalDiscount);

            $tax = Tax::query()
                ->where('status', 'aktif')
                ->latest('id')
                ->first();

            $taxTotal = $tax
                ? $taxableSubtotal * ((float) $tax->persentase / 100)
                : 0;

            $grandTotal = $taxableSubtotal + $taxTotal;

            $paymentMethod = $data['payment_method'];

            $paidAmount = $paymentMethod === 'tunai'
                ? (float) $data['paid_amount']
                : $grandTotal;

            if ($paymentMethod === 'tunai' && $paidAmount < $grandTotal) {
                throw new Exception('Uang diterima kurang dari total bayar.');
            }

            $changeAmount = $paymentMethod === 'tunai'
                ? max(0, $paidAmount - $grandTotal)
                : 0;

            $sale = Sale::create([
                'kode_transaksi' => $this->generateTransactionCode(),

                'cashier_id' => $cashier->id,
                'pos_terminal_id' => $shift->pos_terminal_id,
                'cashier_shift_id' => $shift->id,

                'subtotal' => $subtotal,

                'promo_id' => $promo?->id,
                'nama_promo' => $promo?->nama_promo,
                'tipe_diskon_promo' => $promo?->tipe_diskon,
                'nilai_diskon_promo' => $promo ? (float) $promo->nilai_diskon : 0,
                'total_diskon' => (float) $totalDiscount,

                'tax_id' => $tax?->id,
                'nama_pajak' => $tax?->nama_pajak,
                'persentase_pajak' => $tax ? (float) $tax->persentase : 0,
                'total_pajak' => (float) $taxTotal,

                'grand_total' => (float) $grandTotal,
                'payment_method' => $paymentMethod,
                'paid_amount' => (float) $paidAmount,
                'change_amount' => (float) $changeAmount,

                'status' => 'selesai',
            ]);

            $sale->items()->createMany($saleItems->toArray());

            $this->reduceStockAfterSale(
                products: $products,
                requestedItems: $requestedItems,
                sale: $sale,
                cashier: $cashier,
            );

            $sale->load(['items', 'cashier', 'terminal', 'shift']);

            $this->createReceiptPrintJob($sale);

            return $sale;
        });
    }

    private function reduceStockAfterSale(
        Collection $products,
        Collection $requestedItems,
        Sale $sale,
        User $cashier
    ): void {
        foreach ($requestedItems as $requestedItem) {
            /** @var Product $product */
            $product = $products->get($requestedItem['product_id']);

            $qty = (int) $requestedItem['qty'];
            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore - $qty;

            if ($stockAfter < 0) {
                throw new Exception(
                    "Stok {$product->nama_produk} tidak cukup. Sisa stok: {$stockBefore}."
                );
            }

            $product->update([
                'stock' => $stockAfter,
                'status_ketersediaan' => $stockAfter > 0 ? 'tersedia' : 'habis',
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'note' => 'Stok keluar dari transaksi ' . $sale->kode_transaksi . '.',
                'created_by' => $cashier->id,
            ]);
        }
    }

    protected function findBestPromo(Collection $saleItems, float $subtotal): array
    {
        $today = now()->toDateString();

        $promos = Promo::query()
            ->with(['products:id'])
            ->where('status', 'aktif')
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_mulai')
                    ->orWhereDate('tanggal_mulai', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $today);
            })
            ->get();

        $bestPromo = null;
        $bestDiscount = 0;

        foreach ($promos as $promo) {
            $eligibleSubtotal = $subtotal;

            if ($promo->cakupan_promo === 'menu_tertentu') {
                $promoProductIds = $promo->products->pluck('id');

                $eligibleSubtotal = (float) $saleItems
                    ->whereIn('product_id', $promoProductIds)
                    ->sum('subtotal');
            }

            if ($eligibleSubtotal <= 0) {
                continue;
            }

            $discount = $promo->tipe_diskon === 'persentase'
                ? $eligibleSubtotal * ((float) $promo->nilai_diskon / 100)
                : (float) $promo->nilai_diskon;

            $discount = min($discount, $eligibleSubtotal, $subtotal);

            if ($discount > $bestDiscount) {
                $bestPromo = $promo;
                $bestDiscount = $discount;
            }
        }

        return [
            'promo' => $bestPromo,
            'discount' => $bestDiscount,
        ];
    }

    protected function createReceiptPrintJob(Sale $sale): void
    {
        $sale->loadMissing(['items', 'cashier', 'terminal']);

        PrintJob::create([
            'pos_terminal_id' => $sale->pos_terminal_id,
            'sale_id' => $sale->id,
            'cashier_shift_id' => $sale->cashier_shift_id,
            'type' => 'receipt',
            'payload' => $this->receiptPayload($sale),
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    protected function receiptPayload(Sale $sale): array
    {
        return [
            'transaction_id' => $sale->kode_transaksi,
            'receipt_number' => $this->shortReceiptNumber($sale->kode_transaksi),
            'created_at' => $sale->created_at?->toDateTimeString(),
            'queued_at' => now()->toDateTimeString(),

            'customer_name' => null,
            'cashier_name' => $sale->cashier?->name,
            'note' => '',

            'terminal' => [
                'id' => $sale->terminal?->id,
                'kode_terminal' => $sale->terminal?->kode_terminal,
                'nama_terminal' => $sale->terminal?->nama_terminal,
            ],

            'items' => $sale->items
                ->map(fn($item) => [
                    'name' => $item->nama_produk,
                    'category' => $item->nama_kategori,
                    'qty' => (int) $item->qty,
                    'price' => (float) $item->harga_satuan,
                    'subtotal' => (float) $item->subtotal,
                ])
                ->values()
                ->all(),

            'payment_method' => $this->receiptPaymentMethod($sale->payment_method),

            'subtotal' => (float) $sale->subtotal,
            'promo_name' => $sale->nama_promo,
            'discount' => (float) $sale->total_diskon,
            'tax_name' => $sale->nama_pajak,
            'tax_percentage' => (float) $sale->persentase_pajak,
            'tax' => (float) $sale->total_pajak,
            'total' => (float) $sale->grand_total,
            'paid_amount' => (float) $sale->paid_amount,
            'change_amount' => (float) $sale->change_amount,

            'summary' => [
                'subtotal' => (float) $sale->subtotal,
                'promo_name' => $sale->nama_promo,
                'discount' => (float) $sale->total_diskon,
                'tax_name' => $sale->nama_pajak,
                'tax_percentage' => (float) $sale->persentase_pajak,
                'tax' => (float) $sale->total_pajak,
                'total' => (float) $sale->grand_total,
                'paid_amount' => (float) $sale->paid_amount,
                'change_amount' => (float) $sale->change_amount,
            ],
        ];
    }

    protected function receiptPaymentMethod(?string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'tunai' => 'cash',
            'qris' => 'qris',
            'transfer' => 'transfer',
            default => 'payment',
        };
    }

    protected function shortReceiptNumber(string $transactionCode): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $transactionCode);

        if (strlen($clean) <= 6) {
            return $clean;
        }

        return substr($clean, -6);
    }

    private function generateTransactionCode(): string
    {
        $date = now();
        $counterDate = $date->toDateString();
        $prefix = 'RMHKM-' . $date->format('ymd') . '-';

        $counter = TransactionCounter::query()
            ->where('counter_date', $counterDate)
            ->lockForUpdate()
            ->first();

        if (! $counter) {
            $lastNumber = $this->getLastTransactionNumberForDate($date);

            try {
                TransactionCounter::create([
                    'counter_date' => $counterDate,
                    'last_number' => $lastNumber,
                ]);
            } catch (QueryException $exception) {
                if (! $this->isDuplicateCounterException($exception)) {
                    throw $exception;
                }
            }

            $counter = TransactionCounter::query()
                ->where('counter_date', $counterDate)
                ->lockForUpdate()
                ->firstOrFail();
        }

        $nextNumber = ((int) $counter->last_number) + 1;

        $counter->update([
            'last_number' => $nextNumber,
        ]);

        return $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function getLastTransactionNumberForDate($date): int
    {
        $prefix = 'RMHKM-' . $date->format('ymd') . '-';

        $lastCode = Sale::query()
            ->whereDate('created_at', $date->toDateString())
            ->where('kode_transaksi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode_transaksi');

        if ($lastCode && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastCode, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function isDuplicateCounterException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;

        return $sqlState === '23000' && in_array((int) $driverCode, [1062, 19], true);
    }

    public function getActivePromos(): Collection
    {
        $today = now()->toDateString();

        return Promo::query()
            ->with(['products:id'])
            ->where('status', 'aktif')
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_mulai')
                    ->orWhereDate('tanggal_mulai', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $today);
            })
            ->orderBy('nama_promo')
            ->get()
            ->map(function (Promo $promo) {
                return [
                    'id' => $promo->id,
                    'nama_promo' => $promo->nama_promo,
                    'tipe_diskon' => $promo->tipe_diskon,
                    'nilai_diskon' => (float) $promo->nilai_diskon,
                    'cakupan_promo' => $promo->cakupan_promo,
                    'product_ids' => $promo->products->pluck('id')->map(fn($id) => (int) $id)->values(),
                ];
            });
    }

    public function getActiveTax(): ?array
    {
        $tax = Tax::query()
            ->where('status', 'aktif')
            ->latest('id')
            ->first();

        if (! $tax) {
            return null;
        }

        return [
            'id' => $tax->id,
            'nama_pajak' => $tax->nama_pajak,
            'persentase' => (float) $tax->persentase,
        ];
    }
}