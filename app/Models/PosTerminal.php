<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosTerminal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_terminal',
        'nama_terminal',
        'bridge_token',
        'status',
        'last_seen_at',
        'deskripsi',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }

    public function getMaskedBridgeTokenAttribute(): string
    {
        if (! $this->bridge_token) {
            return '-';
        }

        return substr($this->bridge_token, 0, 8) . '••••••••' . substr($this->bridge_token, -8);
    }

    public function cashierShifts(): HasMany
    {
        return $this->hasMany(CashierShift::class, 'pos_terminal_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'pos_terminal_id');
    }

    public function cashierExpenses(): HasMany
    {
        return $this->hasMany(CashierExpense::class, 'pos_terminal_id');
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class, 'pos_terminal_id');
    }
}