<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('kode_produk')->unique()->nullable();
            $table->string('nama_produk');
            $table->string('slug')->unique();

            $table->text('deskripsi')->nullable();

            $table->decimal('harga_jual', 12, 2);

            $table->string('gambar')->nullable();

            $table->enum('status_ketersediaan', ['tersedia', 'habis'])
                ->default('tersedia');

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};