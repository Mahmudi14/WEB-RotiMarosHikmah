<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'cashier_id',
        'pos_terminal_id',
        'cashier_shift_id',
        'subtotal',
        'promo_id',
        'nama_promo',
        'tipe_diskon_promo',
        'nilai_diskon_promo',
        'total_diskon',
        'tax_id',
        'nama_pajak',
        'persentase_pajak',
        'total_pajak',
        'grand_total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'nilai_diskon_promo' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'persentase_pajak' => 'decimal:2',
        'total_pajak' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id')->withTrashed();
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id')->withTrashed();
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => '-',
        };
    }

    public function getSubtotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }

    public function getTotalDiskonFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_diskon, 0, ',', '.');
    }

    public function getTotalPajakFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_pajak, 0, ',', '.');
    }

    public function getGrandTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->grand_total, 0, ',', '.');
    }

    public function getPaidAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->paid_amount, 0, ',', '.');
    }

    public function getChangeAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->change_amount, 0, ',', '.');
    }
}