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
        Schema::create('rent_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Rent::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('quantity');
            $table->unsignedInteger('per_day_price');
            $table->unsignedInteger('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_details');
    }
};
