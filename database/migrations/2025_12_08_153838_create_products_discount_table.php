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
        Schema::create('products_discount', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('product');
            $table->enum('discount_type', ['fixed', 'percent'])->default('fixed');
            $table->boolean('is_active')->default(false);
            $table->string('value');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_discount');
    }
};
