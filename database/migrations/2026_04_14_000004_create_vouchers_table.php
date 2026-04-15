<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('cost_points');
            $table->string('icon');
            $table->timestamps();
        });

        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->timestamp('claimed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
        Schema::dropIfExists('vouchers');
    }
};
