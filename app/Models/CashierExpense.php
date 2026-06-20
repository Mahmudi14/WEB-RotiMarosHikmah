<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierExpense extends Model
{
    protected $fillable = [
        'cashier_id',
        'cashier_shift_id',
        'pos_terminal_id',
        'tanggal_pengeluaran',
        'kategori_pengeluaran',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function getNominalFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->nominal, 0, ',', '.');
    }

    public function getTanggalPengeluaranFormattedAttribute(): string
    {
        return $this->tanggal_pengeluaran?->format('d M Y') ?? '-';
    }
}