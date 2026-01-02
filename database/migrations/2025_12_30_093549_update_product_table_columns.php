<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->unsignedInteger('price')->nullable()->change();
            $table->string('image')->nullable()->change();
        });
    }


    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
            $table->unsignedInteger('price')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
        });
    }
};
