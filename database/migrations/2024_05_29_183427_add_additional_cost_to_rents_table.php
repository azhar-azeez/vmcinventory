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
        Schema::table('rents', function (Blueprint $table) {
            //
            $table->decimal('additional_cost', 10, 2)->default(0.00)->after('total');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rents', function (Blueprint $table) {
            //
            $table->dropColumn('additional_cost');

        });
    }
};
