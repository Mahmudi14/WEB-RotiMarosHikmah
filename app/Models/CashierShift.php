<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    protected $fillable = [
        'cashier_id',
        'pos_terminal_id',
        'opening_cash',
        'closing_cash',
        'total_cash_sales',
        'total_non_cash_sales',
        'total_expenses',
        'expected_cash',
        'cash_difference',
        'opened_at',
        'closed_at',
        'status',
        'opening_note',
        'closing_note',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_non_cash_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cashier_shift_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CashierExpense::class, 'cashier_shift_id');
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class, 'cashier_shift_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'ditutup' => 'Ditutup',
            default => '-',
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    public function getOpeningCashFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->opening_cash, 0, ',', '.');
    }

    public function getClosingCashFormattedAttribute(): string
    {
        return $this->closing_cash === null
            ? '-'
            : 'Rp ' . number_format((float) $this->closing_cash, 0, ',', '.');
    }

    public function getExpectedCashFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->expected_cash, 0, ',', '.');
    }

    public function getCashDifferenceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->cash_difference, 0, ',', '.');
    }

    public function getTotalCashSalesFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_cash_sales, 0, ',', '.');
    }

    public function getTotalNonCashSalesFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_non_cash_sales, 0, ',', '.');
    }

    public function getTotalExpensesFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_expenses, 0, ',', '.');
    }
}