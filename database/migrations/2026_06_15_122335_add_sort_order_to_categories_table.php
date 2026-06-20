<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('status');
        });

        DB::table('categories')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($category, $index) {
                DB::table('categories')
                    ->where('id', $category->id)
                    ->update([
                        'sort_order' => $index + 1,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};