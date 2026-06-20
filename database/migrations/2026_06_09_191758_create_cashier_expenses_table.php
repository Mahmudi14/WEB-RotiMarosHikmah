<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cashier_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('cashier_shift_id')
                ->constrained('cashier_shifts')
                ->restrictOnDelete();

            $table->foreignId('pos_terminal_id')
                ->constrained('pos_terminals')
                ->restrictOnDelete();

            $table->date('tanggal_pengeluaran');
            $table->string('kategori_pengeluaran');
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['cashier_id', 'tanggal_pengeluaran']);
            $table->index(['cashier_shift_id', 'tanggal_pengeluaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_expenses');
    }
};