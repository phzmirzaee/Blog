<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('forgot_password', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expired_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('forgot_password');
    }
};
