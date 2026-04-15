<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->decimal('est_weight', 8, 2);
            $table->decimal('actual_weight', 8, 2)->nullable();
            $table->string('method'); // Drop-off or Pick-up
            $table->enum('status', ['pending', 'weighing', 'complete', 'rejected'])->default('pending');
            $table->string('reject_reason')->nullable();
            $table->integer('total_price')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->string('dropoff_location')->nullable();
            $table->text('pickup_address')->nullable();
            $table->datetime('pickup_datetime')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
