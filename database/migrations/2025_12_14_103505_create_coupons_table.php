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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->boolean('is_active');
            $table->unsignedBigInteger('usage_limit');
            $table->unsignedBigInteger('usage_limit_per_user');
            $table->unsignedBigInteger('value');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
