<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_pajak',
        'persentase',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'deleted_at' => 'datetime',
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