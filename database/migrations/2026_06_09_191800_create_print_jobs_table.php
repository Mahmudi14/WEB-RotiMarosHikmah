<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_terminal_id')
                ->constrained('pos_terminals')
                ->restrictOnDelete();

            $table->foreignId('sale_id')
                ->nullable()
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('cashier_shift_id')
                ->nullable()
                ->constrained('cashier_shifts')
                ->cascadeOnDelete();

            $table->enum('type', ['receipt', 'shift_report']);
            $table->json('payload');

            $table->enum('status', ['pending', 'printing', 'printed', 'failed'])
                ->default('pending');

            $table->unsignedInteger('attempts')->default(0);

            $table->timestamp('locked_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['pos_terminal_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};