<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'nama_pajak',
        'persentase',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
    ];

    public function getPersentaseFormattedAttribute(): string
    {
        return rtrim(rtrim(number_format((float) $this->persentase, 2, ',', '.'), '0'), ',') . '%';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }
}