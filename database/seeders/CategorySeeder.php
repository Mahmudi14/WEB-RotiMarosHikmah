<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'nama_kategori' => 'Roti',
                'deskripsi' => 'Aneka produk roti Roti Maros Hikmah.',
                'status' => 'aktif',
            ],
            [
                'nama_kategori' => 'Snack',
                'deskripsi' => 'Aneka snack, jajanan, dan cemilan pendamping.',
                'status' => 'aktif',
            ],
            [
                'nama_kategori' => 'Minuman',
                'deskripsi' => 'Aneka minuman pendamping roti dan snack.',
                'status' => 'aktif',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($category['nama_kategori'])],
                [
                    'nama_kategori' => $category['nama_kategori'],
                    'slug' => Str::slug($category['nama_kategori']),
                    'deskripsi' => $category['deskripsi'],
                    'status' => $category['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}