<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingGuestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('booking_guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('guest_name');
            $table->string('email')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('aadhar_no', 20)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_guests');
    }
}
