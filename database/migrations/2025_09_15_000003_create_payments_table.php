<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('payment_method',50)->nullable();
            $table->enum('payment_status', ['pending', 'paid_dummy', 'paid_real', 'failed'])->default('pending');
            $table->decimal('payment_amount',10,2)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('transaction_id',100)->nullable();
            $table->string('payment_proof')->nullable();
            $table->text('qr_code_data')->nullable();
            $table->string('barcode_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
