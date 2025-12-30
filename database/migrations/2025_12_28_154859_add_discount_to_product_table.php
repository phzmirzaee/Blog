<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->enum('discount_type', ['fixed', 'percent'])->default('fixed')->nullable();
            $table->boolean('is_active_discount')->default(false)->nullable();
            $table->string('discount_value')->nullable();
            $table->timestamp('start_date_discount')->nullable();
            $table->timestamp('end_date_discount')->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('is_active_discount');
            $table->dropColumn('discount_value');
            $table->dropColumn('start_date_discount');
            $table->dropColumn('end_date_discount');
        });
    }
};
