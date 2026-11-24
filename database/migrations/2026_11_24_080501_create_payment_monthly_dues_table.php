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
        Schema::create('payment_monthly_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monthly_dues_id')->constrained('monthly_dues')->restrictOnDelete();
            $table->decimal('allocated_amount', 8, 2);
            $table->timestamps();

            $table->unique(['payment_id', 'monthly_dues_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_monthly_dues');
    }
};
