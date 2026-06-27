<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promo extends Model
{
    protected $fillable = [
        'nama_promo',
        'kode_promo',
        'tipe_diskon',
        'nilai_diskon',
        'cakupan_promo',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'nilai_diskon' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promo_product')
            ->withTimestamps();
    }

    public function getNilaiDiskonFormattedAttribute(): string
    {
        if ($this->tipe_diskon === 'persentase') {
            return rtrim(rtrim(number_format($this->nilai_diskon, 2, ',', '.'), '0'), ',') . '%';
        }

        return 'Rp ' . number_format($this->nilai_diskon, 0, ',', '.');
    }

    public function getTipeDiskonLabelAttribute(): string
    {
        return match ($this->tipe_diskon) {
            'persentase' => 'Persentase',
            'nominal' => 'Nominal',
            default => '-',
        };
    }

    public function getCakupanPromoLabelAttribute(): string
    {
        return match ($this->cakupan_promo) {
            'semua_menu' => 'Semua Menu',
            'menu_tertentu' => 'Menu Tertentu',
            default => '-',
        };
    }

    public function getPeriodeFormattedAttribute(): string
    {
        if (! $this->tanggal_mulai && ! $this->tanggal_selesai) {
            return 'Tidak dibatasi';
        }

        $mulai = $this->tanggal_mulai?->format('d M Y') ?? '-';
        $selesai = $this->tanggal_selesai?->format('d M Y') ?? '-';

        return "{$mulai} - {$selesai}";
    }
    public function getStatusEfektifAttribute(): string
    {
        return $this->is_berjalan ? 'aktif' : 'nonaktif';
    }

    public function getStatusEfektifLabelAttribute(): string
    {
        return $this->status_efektif === 'aktif' ? 'Aktif' : 'Nonaktif';
    }
    public function getStatusEfektifDescriptionAttribute(): string
    {
        $today = now()->toDateString();

        if ($today < $this->tanggal_mulai->toDateString()) {
            return 'Belum mulai';
        }

        if ($today > $this->tanggal_selesai->toDateString()) {
            return 'Sudah berakhir';
        }

        return 'Sedang berlaku';
    }


    public function getIsBerjalanAttribute(): bool
    {
        $today = now()->toDateString();

        return $today >= $this->tanggal_mulai->toDateString()
            && $today <= $this->tanggal_selesai->toDateString();
    }
}