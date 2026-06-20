<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cashier_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('pos_terminal_id')
                ->constrained('pos_terminals')
                ->restrictOnDelete();

            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->nullable();

            $table->decimal('total_cash_sales', 12, 2)->default(0);
            $table->decimal('total_non_cash_sales', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('expected_cash', 12, 2)->default(0);
            $table->decimal('cash_difference', 12, 2)->default(0);

            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();

            $table->enum('status', ['aktif', 'ditutup'])->default('aktif');

            $table->text('opening_note')->nullable();
            $table->text('closing_note')->nullable();

            $table->timestamps();

            $table->index(['cashier_id', 'status']);
            $table->index(['pos_terminal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};