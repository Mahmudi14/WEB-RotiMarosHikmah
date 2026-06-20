<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();

            $table->string('nama_promo');
            $table->string('kode_promo')->unique()->nullable();

            $table->enum('tipe_diskon', ['persentase', 'nominal']);
            $table->decimal('nilai_diskon', 12, 2);

            $table->enum('cakupan_promo', ['semua_menu', 'menu_tertentu'])
                ->default('semua_menu');

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};