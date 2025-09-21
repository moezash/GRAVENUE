<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('price_per_day',10,2)->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->string('category',50)->nullable();
            $table->text('features')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('facilities');
    }
};
