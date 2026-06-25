<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')
                ->default(0)
                ->after('harga_jual');

            $table->index(['stock', 'status_ketersediaan']);
        });

        DB::table('products')->update([
            'stock' => 0,
            'status_ketersediaan' => 'habis',
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['stock', 'status_ketersediaan']);
            $table->dropColumn('stock');
        });
    }
};