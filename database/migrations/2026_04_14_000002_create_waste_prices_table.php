<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_prices', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('sub_category');
            $table->integer('price_per_unit');
            $table->string('unit')->default('Kg');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_prices');
    }
};
