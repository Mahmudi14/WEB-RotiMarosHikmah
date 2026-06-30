<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'kode_produk',
        'nama_produk',
        'slug',
        'deskripsi',
        'harga_jual',
        'stock',
        'gambar',
        'status_ketersediaan',
        'status',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'stock' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'promo_product')
            ->withTimestamps();
    }

    public function getHargaJualFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}