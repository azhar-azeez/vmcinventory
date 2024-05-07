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
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId("user_id")->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Customer::class)
            ->constrained();
            $table->date('rent_date');
            $table->date('return_date');
            $table->integer('total_products');
            $table->integer('sub_total');
            $table->integer('vat');
            $table->integer('total');           
            $table->string('invoice_no');
            $table->string('payment_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};
