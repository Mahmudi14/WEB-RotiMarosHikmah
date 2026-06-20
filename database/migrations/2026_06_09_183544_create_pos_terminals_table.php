<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_terminals', function (Blueprint $table) {
            $table->id();

            $table->string('kode_terminal')->unique();
            $table->string('nama_terminal');

            $table->string('bridge_token')->unique();

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamp('last_seen_at')->nullable();

            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_terminals');
    }
};