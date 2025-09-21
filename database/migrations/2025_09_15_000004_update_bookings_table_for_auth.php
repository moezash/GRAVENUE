<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("bookings", function (Blueprint $table) {
            // Add user_id foreign key
            $table
                ->unsignedBigInteger("user_id")
                ->nullable()
                ->after("facility_id");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("set null");

            // Change date structure to single booking_date
            $table->date("booking_date")->after("event_name");

            // Add participants field
            $table->integer("participants")->default(1)->after("end_time");

            // Drop old fields that are no longer needed
            $table->dropColumn(["start_date", "end_date", "total_days"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("bookings", function (Blueprint $table) {
            // Add back old fields
            $table->date("start_date")->after("event_name");
            $table->date("end_date")->after("start_date");
            $table->integer("total_days")->after("end_time");

            // Drop new fields
            $table->dropForeign(["user_id"]);
            $table->dropColumn(["user_id", "booking_date", "participants"]);
        });
    }
};
