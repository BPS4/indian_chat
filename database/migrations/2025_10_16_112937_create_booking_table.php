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
        Schema::create('booking', function (Blueprint $table) {
        $table->id(); // auto-increment primary key 'id'
        $table->string('booking_id')->unique(); // unique string booking ID
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('hotel_id');
        $table->date('checkin_date');
        $table->date('checkout_date');
        $table->integer('total_nights');
        $table->integer('total_guests');
        $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
        $table->timestamps();

        // Foreign keys
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
