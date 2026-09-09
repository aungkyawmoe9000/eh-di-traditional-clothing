<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('opening_balance', 12, 2);
            $table->decimal('total_cash_sales', 12, 2);
            $table->decimal('total_expenses', 12, 2);
            $table->decimal('expected_closing_balance', 12, 2);
            $table->decimal('actual_closing_balance', 12, 2)->nullable();
            $table->decimal('discrepancy', 12, 2)->nullable();
            $table->string('status'); // e.g., 'open' or 'closed'
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
