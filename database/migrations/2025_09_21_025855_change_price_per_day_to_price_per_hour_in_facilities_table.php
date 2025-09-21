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
        Schema::table('facilities', function (Blueprint $table) {
            // Rename column from price_per_day to price_per_hour
            $table->renameColumn('price_per_day', 'price_per_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            // Rename back from price_per_hour to price_per_day
            $table->renameColumn('price_per_hour', 'price_per_day');
        });
    }
};
