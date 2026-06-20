<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintJob extends Model
{
    protected $fillable = [
        'pos_terminal_id',
        'sale_id',
        'cashier_shift_id',
        'type',
        'payload',
        'status',
        'attempts',
        'locked_at',
        'printed_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'locked_at' => 'datetime',
        'printed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'receipt' => 'Struk Transaksi',
            'shift_report' => 'Laporan Shift',
            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'printing' => 'Sedang Print',
            'printed' => 'Selesai Print',
            'failed' => 'Gagal Print',
            default => '-',
        };
    }
}