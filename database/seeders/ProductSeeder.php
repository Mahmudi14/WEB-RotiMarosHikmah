<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categoryIds = DB::table('categories')
            ->pluck('id', 'slug');

        $products = [
            // Roti
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-001',
                'nama_produk' => 'Roti Maros Original',
                'deskripsi' => 'Roti maros klasik dengan tekstur lembut dan rasa khas.',
                'harga_jual' => 12000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-002',
                'nama_produk' => 'Roti Maros Coklat',
                'deskripsi' => 'Roti maros dengan isian coklat manis.',
                'harga_jual' => 14000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-003',
                'nama_produk' => 'Roti Maros Keju',
                'deskripsi' => 'Roti maros dengan isian keju gurih.',
                'harga_jual' => 15000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-004',
                'nama_produk' => 'Roti Maros Coklat Keju',
                'deskripsi' => 'Roti maros dengan kombinasi coklat dan keju.',
                'harga_jual' => 17000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-005',
                'nama_produk' => 'Roti Maros Pandan',
                'deskripsi' => 'Roti maros dengan aroma pandan.',
                'harga_jual' => 14000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-006',
                'nama_produk' => 'Roti Sobek Coklat',
                'deskripsi' => 'Roti sobek lembut dengan isian coklat.',
                'harga_jual' => 22000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-007',
                'nama_produk' => 'Roti Pisang Coklat',
                'deskripsi' => 'Roti manis dengan isian pisang dan coklat.',
                'harga_jual' => 10000,
            ],
            [
                'category_slug' => 'roti',
                'kode_produk' => 'RT-008',
                'nama_produk' => 'Roti Abon',
                'deskripsi' => 'Roti lembut dengan topping abon.',
                'harga_jual' => 11000,
            ],

            // Snack
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-001',
                'nama_produk' => 'Bolu Kukus',
                'deskripsi' => 'Bolu kukus lembut untuk cemilan.',
                'harga_jual' => 5000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-002',
                'nama_produk' => 'Kue Lapis',
                'deskripsi' => 'Kue lapis tradisional.',
                'harga_jual' => 5000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-003',
                'nama_produk' => 'Onde-Onde',
                'deskripsi' => 'Onde-onde dengan isian kacang hijau.',
                'harga_jual' => 4000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-004',
                'nama_produk' => 'Pastel',
                'deskripsi' => 'Pastel isi sayur.',
                'harga_jual' => 5000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-005',
                'nama_produk' => 'Risol Mayo',
                'deskripsi' => 'Risol dengan isian mayo dan smoked beef.',
                'harga_jual' => 6000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-006',
                'nama_produk' => 'Donat Gula',
                'deskripsi' => 'Donat lembut dengan taburan gula halus.',
                'harga_jual' => 6000,
            ],
            [
                'category_slug' => 'snack',
                'kode_produk' => 'SN-007',
                'nama_produk' => 'Donat Coklat',
                'deskripsi' => 'Donat lembut dengan topping coklat.',
                'harga_jual' => 7000,
            ],

            // Minuman
            [
                'category_slug' => 'minuman',
                'kode_produk' => 'MN-001',
                'nama_produk' => 'Air Mineral',
                'deskripsi' => 'Air mineral botol.',
                'harga_jual' => 5000,
            ],
            [
                'category_slug' => 'minuman',
                'kode_produk' => 'MN-002',
                'nama_produk' => 'Teh Kotak',
                'deskripsi' => 'Minuman teh kemasan.',
                'harga_jual' => 6000,
            ],
            [
                'category_slug' => 'minuman',
                'kode_produk' => 'MN-003',
                'nama_produk' => 'Kopi Susu',
                'deskripsi' => 'Minuman kopi susu kemasan.',
                'harga_jual' => 8000,
            ],
            [
                'category_slug' => 'minuman',
                'kode_produk' => 'MN-004',
                'nama_produk' => 'Es Teh',
                'deskripsi' => 'Es teh manis segar.',
                'harga_jual' => 5000,
            ],
            [
                'category_slug' => 'minuman',
                'kode_produk' => 'MN-005',
                'nama_produk' => 'Es Jeruk',
                'deskripsi' => 'Es jeruk segar.',
                'harga_jual' => 7000,
            ],
        ];

        foreach ($products as $product) {
            $categoryId = $categoryIds[$product['category_slug']] ?? null;

            if (! $categoryId) {
                continue;
            }

            $stock = (int) ($product['stock'] ?? 20);

            DB::table('products')->updateOrInsert(
                ['kode_produk' => $product['kode_produk']],
                [
                    'category_id' => $categoryId,
                    'kode_produk' => $product['kode_produk'],
                    'nama_produk' => $product['nama_produk'],
                    'slug' => Str::slug($product['nama_produk']),
                    'deskripsi' => $product['deskripsi'],
                    'harga_jual' => $product['harga_jual'],
                    'stock' => $stock,
                    'gambar' => null,
                    'status_ketersediaan' => $stock > 0 ? 'tersedia' : 'habis',
                    'status' => 'aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}