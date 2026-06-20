<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('kode_transaksi')->unique();

            $table->foreignId('cashier_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('pos_terminal_id')
                ->constrained('pos_terminals')
                ->restrictOnDelete();

            $table->foreignId('cashier_shift_id')
                ->constrained('cashier_shifts')
                ->restrictOnDelete();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->foreignId('promo_id')
                ->nullable()
                ->constrained('promos')
                ->nullOnDelete();

            $table->string('nama_promo')->nullable();
            $table->string('tipe_diskon_promo')->nullable();
            $table->decimal('nilai_diskon_promo', 12, 2)->default(0);
            $table->decimal('total_diskon', 12, 2)->default(0);

            $table->foreignId('tax_id')
                ->nullable()
                ->constrained('taxes')
                ->nullOnDelete();

            $table->string('nama_pajak')->nullable();
            $table->decimal('persentase_pajak', 5, 2)->default(0);
            $table->decimal('total_pajak', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            $table->enum('payment_method', ['tunai', 'qris', 'transfer']);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);

            $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['cashier_id', 'created_at']);
            $table->index(['cashier_shift_id', 'status']);
            $table->index(['payment_method', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};